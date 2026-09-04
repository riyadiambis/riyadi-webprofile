<?php

namespace App\Filament\Resources\Projects\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ProjectsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('urutan')
            ->columns([
                ImageColumn::make('gambar')
                    ->label('')
                    ->disk('public')
                    ->limit(3)
                    ->stacked(),
                TextColumn::make('nama')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold'),
                TextColumn::make('tahun')
                    ->sortable(),
                TextColumn::make('post.judul')
                    ->label('Tulisan tertaut')
                    ->placeholder('— segera ditulis —')
                    ->limit(30),
                IconColumn::make('dipin')
                    ->label('Dipin')
                    ->boolean(),
                TextColumn::make('urutan')
                    ->numeric()
                    ->sortable(),
            ])
            ->filters([
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
