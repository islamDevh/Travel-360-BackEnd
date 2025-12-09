<?php

namespace App\Filament\Resources\TouristGuides\Pages;

use App\Filament\Resources\TouristGuides\TouristGuideResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTouristGuide extends EditRecord
{
    protected static string $resource = TouristGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
