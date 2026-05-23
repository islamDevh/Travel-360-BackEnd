<?php

namespace App\Services;

use App\Models\Language;

class LookupService
{
    /**
     * Return all active languages for the multi-select picker.
     */
    public function languages(): array
    {
        return Language::select('id', 'name')->get()->toArray();
    }

    /**
     * Return the list of supported countries.
     */
    public function countries(): array
    {
        return [
            'Saudi Arabia',
            'United Arab Emirates',
            'Kuwait',
            'Bahrain',
            'Qatar',
            'Oman',
            'Jordan',
            'Lebanon',
            'Syria',
            'Iraq',
            'Yemen',
            'Egypt',
            'Sudan',
            'Libya',
            'Tunisia',
            'Algeria',
            'Morocco',
            'Mauritania',
            'Somalia',
            'Djibouti',
            'Comoros',
            'Pakistan',
            'India',
            'Bangladesh',
            'Philippines',
            'Indonesia',
            'Malaysia',
            'Turkey',
            'Iran',
            'Ethiopia',
            'Nigeria',
            'Kenya',
            'Tanzania',
            'Uganda',
            'Ghana',
            'South Africa',
            'United States',
            'United Kingdom',
            'France',
            'Germany',
            'Italy',
            'Spain',
            'Russia',
            'China',
            'Japan',
            'South Korea',
            'Brazil',
            'Canada',
            'Australia',
            'New Zealand',
        ];
    }

    /**
     * Return all Saudi Arabia regions grouped with their cities.
     */
    public function saudiAreas(): array
    {
        return [
            [
                'region' => 'Riyadh',
                'cities' => [
                    'Riyadh', 'Al Kharj', 'Al Majma\'ah', 'Dawadmi', 'Zulfi',
                    'Shaqra', 'Afif', 'Al Quwayiyah', 'Wadi Al Dawasir', 'Al Sulayyil',
                ],
            ],
            [
                'region' => 'Mecca (Makkah)',
                'cities' => [
                    'Mecca', 'Jeddah', 'Taif', 'Al Qunfudhah', 'Rabigh',
                    'Lith', 'Al Jumum', 'Khulays', 'Adham', 'Qal\'at Bishah',
                ],
            ],
            [
                'region' => 'Medina (Madinah)',
                'cities' => [
                    'Medina', 'Yanbu', 'Al Ula', 'Mahd Al Dahab', 'Badr',
                    'Al Henakiyah', 'Khaybar', 'Al Mahd',
                ],
            ],
            [
                'region' => 'Eastern Province',
                'cities' => [
                    'Dammam', 'Al Ahsa', 'Al Qatif', 'Dhahran', 'Jubail',
                    'Khobar', 'Hafar Al Batin', 'Ras Tanura', 'Abqaiq', 'Safwa',
                ],
            ],
            [
                'region' => 'Asir',
                'cities' => [
                    'Abha', 'Khamis Mushait', 'Bisha', 'Sarat Abidah', 'Rijal Alma',
                    'Muhayil', 'Dhahran Al Janub', 'Ahad Rafidah', 'Al Namas',
                ],
            ],
            [
                'region' => 'Jizan',
                'cities' => [
                    'Jizan', 'Sabya', 'Abu Arish', 'Samtah', 'Al Dayer',
                    'Farasan', 'Al Ardah', 'Al Idabi', 'Al Harth',
                ],
            ],
            [
                'region' => 'Najran',
                'cities' => [
                    'Najran', 'Sharurah', 'Badr Al Janoub', 'Hubuna', 'Yadamah',
                    'Thar', 'Al Kharkhir',
                ],
            ],
            [
                'region' => 'Al Baha',
                'cities' => [
                    'Al Baha', 'Baljurashi', 'Al Mikhwah', 'Al Qura', 'Qilwah',
                    'Al Aqiq', 'Al Makhwah',
                ],
            ],
            [
                'region' => 'Al Jouf',
                'cities' => [
                    'Sakaka', 'Domat Al Jandal', 'Al Qurayyat', 'Tayma', 'Al Haditha',
                ],
            ],
            [
                'region' => 'Northern Borders',
                'cities' => [
                    'Arar', 'Rafha', 'Turaif', 'Al Uwayqilah', 'Rawd al Jibal',
                ],
            ],
            [
                'region' => 'Tabuk',
                'cities' => [
                    'Tabuk', 'Al Wajh', 'Duba', 'Umluj', 'Al Bid', 'Haql',
                    'Qal\'at Al Akabah',
                ],
            ],
            [
                'region' => 'Hail',
                'cities' => [
                    'Hail', 'Baqaa', 'Al Ghazalah', 'Ash Shinan', 'Al Asyah',
                    'Mawqaq',
                ],
            ],
            [
                'region' => 'Qassim',
                'cities' => [
                    'Buraidah', 'Unaizah', 'Ar Rass', 'Al Bukayriyah', 'Riyadh Al Khabra',
                    'Al Badai', 'Uyun Al Jiwa', 'Al Mithnab', 'Al Nabhaniyah',
                ],
            ],
        ];
    }
}
