<?php

namespace App\Http\Livewire;

use Livewire\Component;

class DriverProfile extends Component
{
    public $user;

public function mount()
{
    // جلب المستخدم مع تفاصيل سيارته
    $this->user = auth()->user()->load('driverDetail');
}

public function render()
{
    return view('livewire.driver.profile');
}
    
}
