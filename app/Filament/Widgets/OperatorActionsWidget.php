<?php
namespace App\Filament\Widgets;

use Filament\Widgets\Widget;

class OperatorActionsWidget extends Widget
{
    protected string $view = 'filament.widgets.operator-actions-widget';

    // Widget ini hanya bisa dilihat oleh Operator AVSEC
    public static function canView(): bool
    {
        return auth()->user()->role === 'operator_avsec';
    }
}