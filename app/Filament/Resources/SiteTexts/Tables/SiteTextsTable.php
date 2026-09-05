<?php

namespace App\Filament\Resources\SiteTexts\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SiteTextsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kunci')
                    ->searchable()
                    ->weight('semibold'),
                TextColumn::make('nilai_id')
                    ->label('Indonesia')
                    ->limit(50),
                TextColumn::make('nilai_en')
                    ->label('Inggris')
                    ->limit(50)
                    ->formatStateUsing(fn (?string $state): string => blank($state) ? 'belum diterjemahkan' : $state)
                    ->badge()
                    ->color(fn (?string $state): string => blank($state) ? 'danger' : 'gray'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ]);
    }
}
