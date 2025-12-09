<?php

namespace App\Filament\Resources\TouristGuides;

use App\Filament\Resources\TouristGuides\Pages\CreateTouristGuide;
use App\Filament\Resources\TouristGuides\Pages\EditTouristGuide;
use App\Filament\Resources\TouristGuides\Pages\ListTouristGuides;
use App\Filament\Resources\TouristGuides\Schemas\TouristGuideForm;
use App\Filament\Resources\TouristGuides\Tables\TouristGuidesTable;
use App\Models\TouristGuide;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;

class TouristGuideResource extends Resource
{
    protected static ?string $model = TouristGuide::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'name';

    protected static ?string $navigationLabel = 'المرشدين السياحيين';
    
    protected static ?string $modelLabel = 'مرشد سياحي';
    
    protected static ?string $pluralModelLabel = 'المرشدين السياحيين';

    public static function form(Schema $schema): Schema
    {
        return TouristGuideForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TouristGuidesTable::configure($table);
    }
    
    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTouristGuides::route('/'),
            'create' => CreateTouristGuide::route('/create'),
            'edit'   => EditTouristGuide::route('/{record}/edit'),
        ];
    }
}