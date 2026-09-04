<?php

namespace App\Filament\Resources\Projects\Schemas;

use App\Services\PipelineGambar;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ProjectForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        TextInput::make('tahun')
                            ->required()
                            ->maxLength(4),
                    ])
                    ->columns(3),

                Section::make('Ringkasan')
                    ->description('Bahasa Inggris opsional. Kartu publik jatuh ke bahasa Indonesia kalau kosong.')
                    ->schema([
                        TextInput::make('ringkasan')
                            ->label('Indonesia')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('ringkasan_en')
                            ->label('Inggris'),
                    ])
                    ->columns(2),

                Section::make('Gambar')
                    ->description('Minimal satu. Urutan bisa diseret; urutan pertama dipakai sebagai gambar utama saat kartu diam.')
                    ->schema([
                        FileUpload::make('gambar')
                            ->label('')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->minFiles(1)
                            ->disk('public')
                            ->maxSize(config('media.batas_unggah_kb'))
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return app(PipelineGambar::class)->proses($file, 'project')['thumb'];
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Penautan dan publikasi')
                    ->schema([
                        Select::make('post_id')
                            ->label('Tulisan tertaut')
                            ->relationship('post', 'judul')
                            ->searchable()
                            ->nullable()
                            ->helperText('Kartu di halaman publik baru bisa diklik setelah tulisan ini berstatus terbit.'),
                        Toggle::make('dipin')
                            ->label('Tampilkan di beranda')
                            ->default(false),
                        TextInput::make('urutan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Angka lebih kecil tampil lebih dulu.'),
                    ])
                    ->columns(3),
            ]);
    }
}
