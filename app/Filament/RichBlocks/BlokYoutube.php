<?php

namespace App\Filament\RichBlocks;

use Filament\Actions\Action;
use Filament\Forms\Components\RichEditor\RichContentCustomBlock;
use Filament\Forms\Components\TextInput;

/**
 * Sematan video YouTube di dalam editor journal.
 *
 * Pemilik cukup menempel tautan. ID videonya diambil dan disimpan
 * sebagai data-config pada penanda <div data-type="customBlock"> di
 * dalam HTML `konten` — lihat Filament\Forms\Components\RichEditor\
 * TipTapExtensions\CustomBlockExtension, atribut `config` benar-benar
 * dirender ke HTML, sementara `preview` (dipakai method toPreviewHtml
 * di bawah) hanya tampil saat mengedit di panel dan tidak ikut
 * tersimpan.
 *
 * toHtml() sengaja belum diimplementasikan. Merender penanda ini jadi
 * iframe adalah pekerjaan Fase 2B, lihat docs/fitur/01b-journal-publik.md.
 */
class BlokYoutube extends RichContentCustomBlock
{
    public static function getId(): string
    {
        return 'youtube';
    }

    public static function getLabel(): string
    {
        return 'Video YouTube';
    }

    public static function configureEditorAction(Action $action): Action
    {
        return $action->schema([
            TextInput::make('url')
                ->label('Tautan YouTube')
                ->required()
                ->url()
                ->rule('regex:/(youtube\.com|youtu\.be)/i')
                ->helperText('Tempel tautan videonya, misalnya https://youtu.be/xxxxxxxxxxx'),
        ]);
    }

    public static function getPreviewLabel(array $data): string
    {
        $id = static::idVideo($data['url'] ?? '');

        return $id ? "Video YouTube ({$id})" : 'Video YouTube';
    }

    public static function toPreviewHtml(array $data): ?string
    {
        $id = static::idVideo($data['url'] ?? '');

        if (! $id) {
            return '<div class="p-4 text-sm text-danger-600">Tautan YouTube tidak dikenali.</div>';
        }

        $sampul = "https://img.youtube.com/vi/{$id}/hqdefault.jpg";

        return <<<HTML
            <div class="not-prose overflow-hidden rounded-lg border" style="aspect-ratio: 16 / 9;">
                <img src="{$sampul}" alt="Sampul video YouTube" style="width: 100%; height: 100%; object-fit: cover;" />
            </div>
            HTML;
    }

    /**
     * Mengambil ID video dari bentuk tautan YouTube yang umum: youtu.be,
     * watch?v=, embed/, dan shorts/.
     */
    public static function idVideo(string $url): ?string
    {
        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([A-Za-z0-9_-]{11})/', $url, $cocok)) {
            return $cocok[1];
        }

        return null;
    }
}
