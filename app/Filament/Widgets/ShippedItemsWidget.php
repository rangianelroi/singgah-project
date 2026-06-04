<?php

namespace App\Filament\Widgets;

use App\Models\ConfiscatedItem;
use Filament\Widgets\Widget;

class ShippedItemsWidget extends Widget
{
    protected string $view = 'filament.widgets.shipped-items-widget';
    protected int | string | array $columnSpan = 'full';

    public function getItems()
    {
        return ConfiscatedItem::with(['passenger', 'flight', 'latestStatusLog', 'communications'])
            ->shipped()
            ->latest()
            ->paginate(5);
    }

    public static function canView(): bool
    {
        return in_array(auth()->user()->role, ['team_leader_avsec']);
    }
}
