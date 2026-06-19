<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\Appointment;
use App\Models\BlockedSchedule;
use App\Models\Customer;
use App\Models\Holiday;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

/**
 * Registra o morph map para relacionamentos polimórficos.
 *
 * Sem isso, o Laravel armazenaria o FQCN completo (App\Models\Customer)
 * nas colunas *_type, o que é frágil a refatorações e verboso no banco.
 *
 * Com o morph map, created_by_type/cancelled_by_type armazenam apenas
 * 'customer' ou 'admin' — alinhado com ActorType::value.
 */
class MorphMapServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Relation::enforceMorphMap([
            'customer'         => Customer::class,
            'admin'            => Admin::class,
            'appointment'      => Appointment::class,
            'holiday'          => Holiday::class,
            'blocked_schedule' => BlockedSchedule::class,
        ]);
    }
}
