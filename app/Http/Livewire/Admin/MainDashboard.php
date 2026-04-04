<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\Trip;
use App\Models\Voucher;
use App\Models\PayoutRequest; 
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MainDashboard extends Component
{
    public $searchDriver = '';
    public $searchCustomer = '';
    
    // متغيرات توليد الأكواد
    public $voucherAmount = 5000;
    public $voucherCount = 1;

    // 1. تفعيل المندوب
    public function activateDriver($driverId)
    {
        $user = User::where('role', 'driver')->find($driverId);
        if ($user) {
            $user->status = 'active';
            $user->save();
            session()->flash('message', 'تم تفعيل حساب المندوب ' . $user->name);
        }
    }

    // 2. معالجة طلب صرف المستحقات
   // ... داخل كلاس MainDashboard ...

    // 2. معالجة طلب صرف المستحقات (تعديل لضمان الدقة)
    public function approvePayout($requestId)
    {
        try {
            DB::transaction(function () use ($requestId) {
                // جلب الطلب مع بيانات المستخدم المرتبط به لتجنب كثرة الاستعلامات
                $paymentRequest = PayoutRequest::with('user')->where('status', 'pending')->find($requestId);

                if (!$paymentRequest) {
                    throw new \Exception("الطلب غير موجود أو تم معالجته مسبقاً.");
                }

                $user = $paymentRequest->user;

                if ($user) {
                    // التحقق: هل رصيد المستخدم الحالي يغطي المبلغ المطلوب؟ 
                    // (اختياري: إذا كنت تريد خصم المبلغ المطلوب فقط وليس تصفير الحساب بالكامل)
                    if ($user->balance < $paymentRequest->amount) {
                         // يمكنك إما إيقاف العملية أو المتابعة حسب منطق عملك
                         // throw new \Exception("رصيد المستخدم أقل من المبلغ المطلوب سحبه.");
                    }

                    // تصفير الرصيد أو خصم المبلغ المطلوب فقط
                    // $user->decrement('balance', $paymentRequest->amount); // لخصم المبلغ المطلوب فقط
                    $user->balance = 0; // لتصفير الحساب بالكامل كما في كودك الأصلي
                    $user->save();

                    // تحديث حالة الطلب
                    $paymentRequest->status = 'completed';
                    $paymentRequest->save();

                    session()->flash('message', 'تمت العملية: تم صرف مبلغ (' . number_format($paymentRequest->amount) . ' د.ع) للمستخدم ' . $user->name);
                }
            });
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ: ' . $e->getMessage());
        }
    }

    // --- وظائف نظام الشحن ---
    public function generateVouchers()
    {
        $this->validate([
            'voucherAmount' => 'required|numeric|min:1000',
            'voucherCount' => 'required|integer|min:1|max:50',
        ]);

        for ($i = 0; $i < $this->voucherCount; $i++) {
            Voucher::create([
                'code' => Str::upper(Str::random(4) . '-' . Str::random(4) . '-' . Str::random(4)),
                'amount' => $this->voucherAmount,
                'is_used' => false,
            ]);
        }
        session()->flash('message', 'تم توليد الأكواد بنجاح');
    }

    public function deleteVoucher($id)
    {
        $voucher = Voucher::find($id);
        if ($voucher) {
            $voucher->delete();
            session()->flash('message', 'تم حذف الكود بنجاح');
        }
    }

    // --- وظائف إدارة طلبات التوصيل ---
    public function createTestTrip()
    {
        $customer = User::where('role', 'customer')->first();
        if ($customer) {
            Trip::create([
                'customer_id'       => $customer->id,
                'pickup_location'   => 'بغداد - حي الجامعة',
                'pickup_lat'        => 33.3151,
                'pickup_long'       => 44.3486,
                'dropoff_location'  => 'بغداد - زيونة',
                'dropoff_lat'       => 33.3122,
                'dropoff_long'      => 44.4223,
                'distance'          => 12.0,
                'duration'          => 35,
                'fare'              => 5000,
                'package_price'     => 45000,
                'total_amount'      => 50000, 
                'payment_method'    => 'cash',
                'status'            => 'pending'
            ]);
            session()->flash('message', 'تم إنشاء طلب توصيل تجريبي');
        }
    }

    public function deleteTrip($tripId)
    {
        $trip = Trip::find($tripId);
        if ($trip) {
            $trip->delete();
            session()->flash('message', 'تم حذف طلب التوصيل');
        }
    }

    public function acceptTrip($tripId)
    {
        $driver = User::where('role', 'driver')->where('status', 'active')->first();
        $trip = Trip::find($tripId);
        if ($driver && $trip && $trip->status == 'pending') {
            $trip->update([
                'driver_id' => $driver->id,
                'status'    => 'accepted',
                'started_at' => now()
            ]);
            session()->flash('message', 'المندوب ' . $driver->name . ' استلم الطلب');
        }
    }

    public function render()
    {
        return view('livewire.admin.main-dashboard', [
            'stats' => [
                'total_drivers'      => User::where('role', 'driver')->count(),
                'pending_drivers'    => User::where('role', 'driver')->where('status', 'pending')->count(),
                'total_customers'    => User::where('role', 'customer')->count(),
                'drivers_total_balance'   => User::where('role', 'driver')->sum('balance') ?? 0,
                'customers_total_balance' => User::where('role', 'customer')->sum('balance') ?? 0,
                'unused_vouchers'    => Voucher::where('is_used', false)->count(),
                'pending_payouts'    => PayoutRequest::where('status', 'pending')->count(),
            ],
            'latest_drivers' => User::where('role', 'driver')
                ->when($this->searchDriver, function($q) {
                    $q->where('name', 'like', '%'.$this->searchDriver.'%')
                      ->orWhere('phone', 'like', '%'.$this->searchDriver.'%');
                })->latest()->take(10)->get(),

            // المتغير الجديد لجلب كافة المتاجر/الزبائن مع البحث
            'all_customers' => User::where('role', 'customer')
                ->when($this->searchCustomer, function($q) {
                    $q->where('name', 'like', '%'.$this->searchCustomer.'%')
                      ->orWhere('phone', 'like', '%'.$this->searchCustomer.'%');
                })->latest()->get(),

            'active_trips' => Trip::with(['customer', 'driver'])->latest()->take(10)->get(),
            'vouchers'     => Voucher::where('is_used', false)->latest()->get(),
            'payout_requests' => PayoutRequest::with('user')->where('status', 'pending')->latest()->get(),
        ])->layout('layouts.app');
    }
}