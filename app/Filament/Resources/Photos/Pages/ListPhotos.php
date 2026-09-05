<?php

namespace App\Filament\Resources\Photos\Pages;

use App\Filament\Resources\Photos\PhotoResource;
use App\Models\Photo;
use App\Services\PipelineGambar;
use App\Services\TurunanGambar;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ListPhotos extends ListRecords
{
    protected static string $resource = PhotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),

            Action::make('unggah_banyak')
                ->label('Unggah banyak')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->form([
                    FileUpload::make('berkas')
                        ->label('')
                        ->image()
                        ->multiple()
                        ->minFiles(1)
                        ->disk('public')
                        ->maxSize(config('media.batas_unggah_kb'))
                        ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                            return app(PipelineGambar::class)->proses($file, 'galeri')['thumb'];
                        })
                        ->helperText('Bisa memilih banyak berkas sekaligus. Caption dan tanggal pengambilan diisi lewat Edit tiap foto.'),
                ])
                ->modalHeading('Unggah banyak foto')
                ->modalSubmitActionLabel('Unggah')
                ->action(function (array $data): void {
                    // Nilai formulir adalah path thumb (demi pratinjau native
                    // FileUpload); yang disimpan ke kolom gambar adalah basis
                    // namanya. Lihat docs/keputusan.md (keputusan Fase 4).
                    $urutan = (int) Photo::max('urutan') + 1;
                    $jumlah = 0;

                    foreach ($data['berkas'] ?? [] as $pathThumb) {
                        Photo::create([
                            'gambar' => TurunanGambar::basisDari($pathThumb),
                            'urutan' => $urutan++,
                        ]);

                        $jumlah++;
                    }

                    Notification::make()
                        ->title("{$jumlah} foto ditambahkan")
                        ->success()
                        ->send();
                }),
        ];
    }
}
