<?php

namespace App\Filament\Resources\TouristGuides\Pages;

use App\Filament\Resources\TouristGuides\TouristGuideResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTouristGuides extends ListRecords
{
    protected static string $resource = TouristGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
