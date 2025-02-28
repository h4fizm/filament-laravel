<?php

namespace App\Filament\Resources\PerformanceReviewResource\Pages;

use App\Filament\Resources\PerformanceReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerformanceReviews extends ListRecords
{
    protected static string $resource = PerformanceReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
