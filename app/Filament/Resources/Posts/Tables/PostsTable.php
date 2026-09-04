<?php

namespace App\Filament\Resources\Posts\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                ImageColumn::make('cover')
                    ->label('')
                    ->disk('public')
                    ->square(),
                TextColumn::make('judul')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => $state === 'terbit' ? 'success' : 'gray')
                    ->formatStateUsing(fn (string $state) => $state === 'terbit' ? 'Terbit' : 'Draf'),
                IconColumn::make('dipin')
                    ->label('Dipin')
                    ->boolean(),
                TextColumn::make('terbit_pada')
                    ->label('Tanggal terbit')
                    ->dateTime('d M Y')
                    ->placeholder('—')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'draf' => 'Draf',
                        'terbit' => 'Terbit',
                    ]),
                TernaryFilter::make('dipin')
                    ->label('Dipin di beranda'),
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
