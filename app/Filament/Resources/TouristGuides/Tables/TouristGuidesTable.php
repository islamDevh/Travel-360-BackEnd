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
                    ->label('الصورة')
                    ->circular()
                    ->defaultImageUrl(url('/images/default-avatar.png')),

                TextColumn::make('name')
                    ->label('الاسم')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label('الهاتف')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('البريد الإلكتروني')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('language.name')
                    ->label('اللغة')
                    ->badge()
                    ->sortable(),

                TextColumn::make('years_of_experience')
                    ->label('سنوات الخبرة')
                    ->numeric()
                    ->sortable()
                    ->suffix(' سنة'),

                TextColumn::make('license_expiry_date')
                    ->label('انتهاء الرخصة')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn($state) => $state && $state->isPast() ? 'danger' : 'success'),

                TextColumn::make('created_at')
                    ->label('تاريخ الإضافة')
                    ->dateTime('d/m/Y h:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            
            ->filters([
                // SelectFilter::make('language_id')
                //     ->label('اللغة')
                //     ->relationship('language', 'name')
                //     ->preload(),
            ])
            ->emptyStateHeading('لا يوجد مرشدين سياحيين')
            ->emptyStateDescription('ابدأ بإضافة أول مرشد سياحي')
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
