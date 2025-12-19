<?php

namespace App\Filament\Resources\TouristGuides\Tables;

use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TouristGuidesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_image')
                    ->label('Profile Image')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('Phone')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('language.name')
                    ->label('Language')
                    ->badge()
                    ->sortable(),

                TextColumn::make('years_of_experience')
                    ->label('Years of Experience')
                    ->numeric()
                    ->sortable()
                    ->suffix(' years'),

                TextColumn::make('license_expiry_date')
                    ->label('License Expiry Date')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'success'),

                TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])

            ->filters([
                  // SelectFilter::make('language_id')
                  //     ->label('Language')
                  //     ->relationship('language', 'name')
                  //     ->preload(),
            ])

            ->emptyStateHeading('No Tourist Guides Found')
            ->emptyStateDescription('Start by creating the first tourist guide')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
