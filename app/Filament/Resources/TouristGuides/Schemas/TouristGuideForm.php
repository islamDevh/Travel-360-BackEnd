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
            Section::make('البيانات الأساسية')
                ->schema([
                    TextInput::make('name')
                        ->label('الاسم')
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('phone')
                        ->label('رقم الهاتف')
                        ->tel()
                        ->maxLength(255)
                        ->nullable(),

                    TextInput::make('email')
                        ->label('البريد الإلكتروني')
                        ->email()
                        ->maxLength(255)
                        ->nullable(),

                    FileUpload::make('profile_image')
                        ->label('الصورة الشخصية')
                        ->image()
                        ->directory('tourist-guides/profiles')
                        ->nullable()
                        ->maxSize(2048),
                ])
                ->columns(2),

            Section::make('الخبرات والمهارات')
                ->schema([
                    Textarea::make('experiences')
                        ->label('الخبرات')
                        ->rows(4)
                        ->nullable()
                        ->columnSpanFull(),

                    // Select::make('language_id')
                    //     ->label('اللغة')
                    //     ->searchable()
                    //     ->nullable()
                    //     ->preload(),

                    TextInput::make('years_of_experience')
                        ->label('سنوات الخبرة')
                        ->numeric()
                        ->minValue(0)
                        ->maxValue(50)
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('رخصة القيادة')
                ->schema([
                    FileUpload::make('driving_license_image')
                        ->label('صورة رخصة القيادة')
                        ->image()
                        ->directory('tourist-guides/licenses')
                        ->nullable()
                        ->maxSize(2048),

                    DatePicker::make('license_expiry_date')
                        ->label('تاريخ انتهاء الرخصة')
                        ->native(false)
                        ->displayFormat('d/m/Y')
                        ->nullable(),
                ])
                ->columns(2),

            Section::make('المستندات والملاحظات')
                ->schema([
                    FileUpload::make('cv')
                        ->label('السيرة الذاتية (CV)')
                        ->acceptedFileTypes(['application/pdf'])
                        ->directory('tourist-guides/cvs')
                        ->nullable()
                        ->maxSize(5120)
                        ->helperText('يرجى رفع ملف PDF فقط'),

                    Textarea::make('notes')
                        ->label('ملاحظات')
                        ->rows(3)
                        ->nullable()
                        ->columnSpanFull(),
                ]),
        ]);
    }
}