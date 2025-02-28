<?php

namespace App\Filament\Widgets;

use App\Models\Contract;
use App\Models\Employee;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class DashboardStats extends BaseWidget
{
    protected static ?int $sort = 1; // Urutan tampil di dashboard

    protected function getCards(): array
    {
        return [
            Card::make('Total Employees', Employee::count())
                ->description('Total registered employees')
                ->color('success'),

            Card::make('Active Contracts', Contract::where('contract_status', 'active')->count())
                ->description('Currently active contracts')
                ->color('primary'),

            Card::make('Expiring Soon', Contract::where('contract_status', 'expiring_soon')->count())
                ->description('Contracts expiring soon')
                ->color('warning'),

            Card::make('Not Renewed', Contract::where('contract_status', 'not_renewed')->count())
                ->description('Expired contracts not renewed')
                ->color('danger'),
        ];
    }
}
