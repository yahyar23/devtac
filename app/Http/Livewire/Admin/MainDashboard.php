<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\DriverDetail;
use App\Models\Trip;
use App\Models\Voucher;
use Illuminate\Support\Str;

class MainDashboard extends Component
{
    public $searchDriver = '';
    public $searchCustomer = '';
    
    // متغيرات توليد الأكواد
    public $voucherAmount = 5000;
    public $voucherCount = 1;

    // 1. دالة تفعيل السائق
    public function activateDriver($driverId)
    {
        $user = User::where('role', 'driver')->find($driverId);
        if ($user) {
            $user->update(['status' => 'active']);
            session()->flash('message', 'تم تفعيل حساب السائق ' . $user->name);
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

        session()->flash('message', 'تم توليد ' . $this->voucherCount . ' كود شحن بنجاح');
    }

    public function deleteVoucher($id)
    {
        Voucher::find($id)->delete();
        session()->flash('message', 'تم حذف الكود بنجاح');
    }

    // --- وظائف المحاكاة ---

    public function createTestTrip()
    {
        $customer = User::where('role', 'customer')->first();
        if ($customer) {
            Trip::create([
                'customer_id'       => $customer->id,
                'pickup_location'   => 'بغداد - المنصور',
                'pickup_lat'        => 33.3151,
                'pickup_long'       => 44.3486,
                'dropoff_location'  => 'بغداد - الكرادة',
                'dropoff_lat'       => 33.3122,
                'dropoff_long'      => 44.4223,
                'distance'          => 7.5,
                'duration'          => 20,
                'fare'              => 8000,
                'payment_method'    => 'cash',
                'status'            => 'pending'
            ]);
            session()->flash('message', 'تم إرسال طلب رحلة تجريبي');
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
            session()->flash('message', 'السائق ' . $driver->name . ' قبل الرحلة');
        }
    }

    public function render()
    {
        $stats = [
            'total_drivers'     => User::where('role', 'driver')->count(),
            'pending_drivers'   => User::where('role', 'driver')->where('status', 'pending')->count(),
            'total_customers'   => User::where('role', 'customer')->count(),
            'total_wallet'      => DriverDetail::sum('wallet_balance') ?? 0,
            'unused_vouchers'   => Voucher::where('is_used', false)->count(),
        ];

        return view('livewire.admin.main-dashboard', [
            'stats'            => $stats,
            // تم تعديل جلب السائقين ليشمل حقول الصور والهاتف والسيارة بشكل كامل
            'latest_drivers'   => User::where('role', 'driver')
                                    ->when($this->searchDriver, function($q) {
                                        $q->where(function($sub) {
                                            $sub->where('name', 'like', '%'.$this->searchDriver.'%')
                                                ->orWhere('phone', 'like', '%'.$this->searchDriver.'%');
                                        });
                                    })
                                    ->latest()
                                    ->take(10) // عرض آخر 10 طلبات بدلاً من 5
                                    ->get(),
            'latest_customers' => User::where('role', 'customer')
                                    ->when($this->searchCustomer, fn($q) => $q->where('name', 'like', '%'.$this->searchCustomer.'%'))
                                    ->latest()->take(5)->get(),
            'active_trips'     => Trip::with(['customer', 'driver'])->latest()->take(10)->get(),
            'vouchers'         => Voucher::where('is_used', false)->latest()->get()
        ])->layout('layouts.app');
    }
}