<?php

namespace App\Filament\Resources\SiteTexts\Schemas;

use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteTextForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Teks beranda')
                    ->description('Bahasa Inggris opsional. Beranda jatuh ke bahasa Indonesia kalau kosong, lihat docs/fitur/05-dwibahasa.md.')
                    ->schema([
                        TextInput::make('kunci')
                            ->label('Kunci')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->columnSpanFull()
                            ->helperText('Hanya kunci perkenalan dan penutup yang dipakai beranda di v1.'),
                        Textarea::make('nilai_id')
                            ->label('Indonesia')
                            ->required()
                            ->rows(4),
                        Textarea::make('nilai_en')
                            ->label('Inggris')
                            ->rows(4),
                    ])
                    ->columns(2),
            ]);
    }
}
