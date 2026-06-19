<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\Customer;
use App\Policies\AdminPolicy;
use App\Policies\AppointmentPolicy;
use App\Policies\CustomerPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Appointment::class => AppointmentPolicy::class,
        Customer::class    => CustomerPolicy::class,
        Admin::class       => AdminPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
