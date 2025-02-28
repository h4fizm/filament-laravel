<?php

namespace App\Filament\Resources\PerformanceReviewResource\Pages;

use App\Filament\Resources\PerformanceReviewResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerformanceReview extends EditRecord
{
    protected static string $resource = PerformanceReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
