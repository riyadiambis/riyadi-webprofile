<?php

namespace App\Filament\Resources\Photos\Tables;

use App\Models\Photo;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PhotosTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->columns([
                ImageColumn::make('gambar')
                    ->label('')
                    ->size(72)
                    ->getStateUsing(fn (Photo $record): string => $record->gambarUrls()['thumb']),
                TextColumn::make('caption')
                    ->placeholder('— tanpa caption —')
                    ->limit(40)
                    ->searchable(),
                TextColumn::make('diambil_pada')
                    ->date('d M Y')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
