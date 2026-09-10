<?php

namespace App\Filament\Resources\Photos\Schemas;

use App\Services\PipelineGambar;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PhotoForm
{
    public static function configure(Schema $schema): Schema
    {
        /*
         | Satu kolom eksplisit di akar, sama seperti form Tulisan dan
         | Project — supaya tata letaknya tidak bergantung pada default
         | Filament. Isi Section-nya sendiri sudah wajar sejak Fase 4
         | (gambar dan caption selebar penuh, tanggal dan urutan
         | berpasangan) jadi tidak diubah.
         */
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        FileUpload::make('gambar')
                            ->label('Gambar')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->maxSize(config('media.batas_unggah_kb'))
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return app(PipelineGambar::class)->proses($file, 'galeri')['thumb'];
                            })
                            ->helperText('Gambar diproses jadi tiga ukuran WebP. Tidak bisa diganti setelah dibuat; hapus foto lalu unggah ulang kalau keliru.')
                            ->visible(fn (string $operation) => $operation === 'create')
                            ->columnSpanFull(),
                        TextInput::make('caption')
                            ->label('Caption')
                            ->maxLength(255)
                            ->helperText('Muncul saat foto dibuka di halaman galeri.')
                            ->columnSpanFull(),
                        DatePicker::make('diambil_pada')
                            ->label('Tanggal pengambilan')
                            ->native(false),
                        TextInput::make('urutan')
                            ->numeric()
                            ->default(0)
                            ->helperText('Angka lebih kecil tampil lebih dulu.'),
                    ])
                    ->columns(2),
            ]);
    }
}
