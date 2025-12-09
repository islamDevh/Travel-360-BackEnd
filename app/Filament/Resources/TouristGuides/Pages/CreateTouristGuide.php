<?php

namespace App\Filament\Resources\TouristGuides\Pages;

use App\Filament\Resources\TouristGuides\TouristGuideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTouristGuide extends CreateRecord
{
    protected static string $resource = TouristGuideResource::class;
    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Tourist Guide Created';
    }
}
