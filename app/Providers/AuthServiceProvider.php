<?php
namespace App\Providers;

// Import Model
use App\Models\ConfiscatedItem;
use App\Models\User;
use App\Models\Airline;       // <-- Tambahkan ini
use App\Models\Airport;       // <-- Tambahkan ini
use App\Models\Passenger;     // <-- Tambahkan ini
use App\Models\Flight;        // <-- Tambahkan ini
use App\Models\CommunicationLog;

// Import Policy
use App\Policies\ConfiscatedItemPolicy;
use App\Policies\UserPolicy;
use App\Policies\AirlinePolicy;      // <-- Tambahkan ini (Penyebab Error Utama)
use App\Policies\AirportPolicy;      // <-- Tambahkan ini
use App\Policies\PassengerPolicy;    // <-- Tambahkan ini
use App\Policies\FlightPolicy;       // <-- Tambahkan ini
use App\Policies\CommunicationLogPolicy;

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