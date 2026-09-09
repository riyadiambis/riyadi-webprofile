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
        /*
         | Satu kolom eksplisit di akar: keempat Section ditumpuk selebar
         | penuh. Sebelumnya menumpang default Filament, jadi Section
         | berpasangan dua kolom dan tingginya tidak pernah sejajar —
         | sisi kanan menyisakan ruang kosong panjang. Bagian Gambar
         | ikut mendapat lebar penuh karena itu yang paling sering
         | dipakai.
         */
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('nama')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),
                        /*
                         | Batas tahun yang masuk akal, diminta pemilik:
                         | 2000 sampai tahun berjalan ditambah satu (satu
                         | tahun ke depan supaya project yang dijadwalkan
                         | tetap bisa dicatat). Batas atasnya dihitung per
                         | permintaan, jadi ikut bergeser sendiri tiap
                         | pergantian tahun tanpa perlu disunting.
                         | Kolomnya tetap string, tidak ada migrasi baru.
                         */
                        TextInput::make('tahun')
                            ->required()
                            ->numeric()
                            ->minValue(2000)
                            ->maxValue(now()->year + 1)
                            ->maxLength(4)
                            ->helperText('Antara 2000 dan '.(now()->year + 1).'.'),
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
