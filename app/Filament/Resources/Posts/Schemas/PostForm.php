<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Filament\RichBlocks\BlokYoutube;
use App\Services\PipelineGambar;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        /*
         | Tata letak ditetapkan EKSPLISIT satu kolom di akar, jangan
         | menumpang default Filament — default itu yang dulu membuat
         | Section berpasangan dua kolom, kolom kiri habis di sepertiga
         | atas lalu kosong panjang, dan editor konten terjepit di
         | separuh lebar padahal itu tempat kerja utama.
         |
         | Susunan yang diminta pemilik, semuanya selebar layar dari
         | atas ke bawah: Judul+Slug bersebelahan, Ringkasan, Sampul,
         | lalu editor selebar penuh, lalu Tautan project dan Publikasi
         | bersebelahan di baris bawah.
         */
        return $schema
            ->columns(1)
            ->components([
                Section::make()
                    ->schema([
                        TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $operation, ?string $state, Set $set) {
                                if ($operation === 'create') {
                                    $set('slug', Str::slug($state));
                                }
                            }),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->helperText('Terisi otomatis dari judul, tapi bisa diedit manual.'),
                        Textarea::make('ringkasan')
                            ->required()
                            ->rows(2)
                            ->columnSpanFull()
                            ->helperText('Tampil di kartu daftar journal dan meta description.'),
                        FileUpload::make('cover')
                            ->label('Sampul')
                            ->image()
                            ->disk('public')
                            ->maxSize(config('media.batas_unggah_kb'))
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return app(PipelineGambar::class)->proses($file)['thumb'];
                            })
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Section::make('Isi tulisan')
                    ->schema([
                        RichEditor::make('konten')
                            ->label('')
                            ->required()
                            // Tempat kerja utama: tinggi dilebihkan dari
                            // bawaan supaya tidak perlu menggulir sejak
                            // paragraf pertama.
                            ->extraInputAttributes(['style' => 'min-height: 32rem'])
                            ->fileAttachmentsDisk('public')
                            ->saveUploadedFileAttachmentUsing(function (TemporaryUploadedFile $file): string {
                                return app(PipelineGambar::class)->proses($file)['sedang'];
                            })
                            ->getFileAttachmentUrlUsing(function (string $file): string {
                                return Storage::disk('public')->url($file);
                            })
                            ->customBlocks([BlokYoutube::class])
                            ->columnSpanFull(),
                    ]),

                // Baris bawah: dua Section bersebelahan, sama tinggi.
                Grid::make(2)
                    ->schema([
                        Section::make('Tautan project')
                            ->description('Opsional. Tombol muncul di posisi paling atas tulisan kalau diisi.')
                            ->schema([
                                TextInput::make('tautan_project')
                                    ->label('URL')
                                    ->url(),
                                TextInput::make('label_tautan')
                                    ->label('Label tombol')
                                    ->placeholder('Contoh: Lihat repo, Coba demo'),
                            ])
                            ->columns(1),

                        Section::make('Publikasi')
                            ->schema([
                                Select::make('status')
                                    ->options([
                                        'draf' => 'Draf',
                                        'terbit' => 'Terbit',
                                    ])
                                    ->required()
                                    ->default('draf')
                                    ->live(),
                                Toggle::make('dipin')
                                    ->label('Tampilkan di beranda')
                                    ->default(false),
                                DateTimePicker::make('terbit_pada')
                                    ->label('Tanggal terbit')
                                    ->native(false)
                                    ->visible(fn (Get $get) => $get('status') === 'terbit'),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }
}
