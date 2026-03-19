<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\DriverDetail;
use App\Models\Trip; // تفعيل الموديل هنا

class MainDashboard extends Component
{
    public $searchDriver = '';
    public $searchCustomer = '';

    // 1. دالة تفعيل السائق
    public function activateDriver($driverId)
    {
        $user = User::where('role', 'driver')->find($driverId);
        if ($user) {
            $user->update(['status' => 'active']);
            session()->flash('message', 'تم تفعيل حساب السائق ' . $user->name);
        }
    }

    // 2. دالة محاكاة إنشاء رحلة ببيانات الجدول الشاملة
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
                'status'            => 'pending' // بانتظار سائق
            ]);
            session()->flash('message', 'تم إرسال طلب رحلة تجريبي من ' . $customer->name);
        } else {
            session()->flash('message', 'لم يتم العثور على زبون لإتمام المحاكاة');
        }
    }

    // 3. دالة محاكاة قبول السائق للرحلة
    public function acceptTrip($tripId)
    {
        // جلب أول سائق نشط (أحمد مثلاً)
        $driver = User::where('role', 'driver')->where('status', 'active')->first();
        $trip = Trip::find($tripId);

        if ($driver && $trip && $trip->status == 'pending') {
            $trip->update([
                'driver_id' => $driver->id,
                'status'    => 'accepted', // تم القبول
                'started_at' => now()
            ]);
            session()->flash('message', 'السائق ' . $driver->name . ' قبل الرحلة وهو في الطريق للزبون');
        }
    }

    public function render()
    {
        $stats = [
            'total_drivers'     => User::where('role', 'driver')->count(),
            'pending_drivers'   => User::where('role', 'driver')->where('status', 'pending')->count(),
            'total_customers'   => User::where('role', 'customer')->count(),
            'total_wallet'      => DriverDetail::sum('wallet_balance') ?? 0,
        ];

        $latest_drivers = User::where('role', 'driver')
            ->when($this->searchDriver, function($q) {
                $q->where('name', 'like', '%'.$this->searchDriver.'%');
            })
            ->latest()->take(5)->get();

        $latest_customers = User::where('role', 'customer')
            ->when($this->searchCustomer, function($q) {
                $q->where('name', 'like', '%'.$this->searchCustomer.'%');
            })
            ->latest()->take(5)->get();

        return view('livewire.admin.main-dashboard', [
            'stats'            => $stats,
            'latest_drivers'   => $latest_drivers,
            'latest_customers' => $latest_customers,
            // جلب الرحلات الحية مع بيانات الزبون والسائق
            'active_trips'     => Trip::with(['customer', 'driver'])->latest()->take(10)->get()
        ])->layout('layouts.app');
    }
}