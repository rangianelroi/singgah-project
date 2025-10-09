<?php
namespace App\Providers;

use App\Models\ConfiscatedItem;
use App\Policies\ConfiscatedItemPolicy;
use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\CommunicationLog; // <-- Tambahkan ini
use App\Policies\CommunicationLogPolicy; // <-- Tambahkan ini
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        ConfiscatedItem::class => ConfiscatedItemPolicy::class,
        User::class => UserPolicy::class,
        Airline::class => AirlinePolicy::class, 
        Airport::class => AirportPolicy::class, 
        Passenger::class => PassengerPolicy::class, 
        Flight::class => FlightPolicy::class, 
        CommunicationLog::class => CommunicationLogPolicy::class,
    ];

    public function boot(): void
    {
        //
    }
}