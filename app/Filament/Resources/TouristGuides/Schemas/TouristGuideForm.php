<?php

namespace App\Filament\Resources\TouristGuides\Schemas;

use App\Models\Language;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TouristGuideForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Basic Information')
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),

                    FileUpload::make('profile_image')
                        ->label('Profile Image')
                        ->image()
                        ->directory('tourist-guides/profiles')
                        ->nullable()
                        ->maxSize(2048),
                ])
                ->columns(2),

            Section::make('Experience & Skills')
                ->schema([
                    Textarea::make('experiences')
                        ->label('Experiences')
                        ->rows(4)
                        ->nullable()
                        ->columnSpanFull(),

                      // Select::make('language_id')
                      //     ->label('Language')
                      //     ->searchable()
                      //     ->nullable()
                      //     ->preload(),

                    TextInput::make('years_of_experience')
                        ->label('Years of Experience')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(50)
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('Driving License')
                ->schema([
                    FileUpload::make('driving_license_image')
                        ->label('Driving License Image')
                        ->image()
                        ->directory('tourist-guides/licenses')
                        ->nullable()
                        ->maxSize(2048),

                    DatePicker::make('license_expiry_date')
                        ->label('License Expiry Date')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('Documents & Notes')
                ->schema([
                    FileUpload::make('cv')
                        ->label('CV (PDF)')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('tourist-guides/cvs')
                        ->nullable()
                        ->maxSize(5120)
                        ->helperText('Please upload PDF file only'),

                    Textarea::make('notes')
                        ->label('Notes')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
