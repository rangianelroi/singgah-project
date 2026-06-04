<?php
namespace App\Providers;

// Import Model
use App\Models\ConfiscatedItem;
use App\Models\User;
use App\Models\Airline;     
use App\Models\Airport;     
use App\Models\Passenger;   
use App\Models\Flight;      
use App\Models\CommunicationLog;

// Import Policy
use App\Policies\ConfiscatedItemPolicy;
use App\Policies\UserPolicy;
use App\Policies\AirlinePolicy;      
use App\Policies\AirportPolicy;    
use App\Policies\PassengerPolicy;  
use App\Policies\FlightPolicy;     
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