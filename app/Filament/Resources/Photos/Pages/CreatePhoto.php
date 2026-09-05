<?php

namespace App\Filament\Resources\Photos\Pages;

use App\Filament\Resources\Photos\PhotoResource;
use App\Services\TurunanGambar;
use Filament\Resources\Pages\CreateRecord;

class CreatePhoto extends CreateRecord
{
    protected static string $resource = PhotoResource::class;

    /**
     * Nilai formulir adalah path thumb (demi pratinjau native
     * FileUpload); kolom gambar menyimpan basis namanya. Lihat
     * docs/keputusan.md (keputusan Fase 4).
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (filled($data['gambar'] ?? null)) {
            $data['gambar'] = TurunanGambar::basisDari($data['gambar']);
        }

        return $data;
    }
}
