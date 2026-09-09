<?php

namespace App\Filament\Pages;

use App\Models\Profile;
use App\Models\SiteText;
use App\Services\PipelineGambar;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

/**
 * Satu-satunya halaman untuk seluruh isi beranda: foto profil, enam
 * tautan sosial, dan teks perkenalan/penutup dwibahasa.
 *
 * Menggantikan Dasbor bawaan Filament — halaman ini yang menempati path
 * '/' panel, jadi login mendarat langsung di sini. Menu "Teks beranda"
 * yang dulu terpisah dilebur ke sini; tidak boleh ada dua menu untuk
 * satu halaman publik.
 *
 * Menyimpan ke DUA tabel sekali simpan: `profiles` (satu baris) dan
 * `site_texts` (dua baris berkunci perkenalan/penutup). site_texts
 * sengaja tetap jadi tabel sendiri, lihat docs/keputusan.md.
 */
class Beranda extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string $routePath = '/';

    /**
     * Menempati path '/' panel, menggantikan Dasbor. Properti
     * $routePath saja tidak cukup — Filament membacanya lewat metode
     * ini, persis seperti Dashboard bawaan.
     */
    public static function getRoutePath(Panel $panel): string
    {
        return static::$routePath;
    }

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected static ?string $title = 'Beranda';

    protected static ?string $navigationLabel = 'Beranda';

    protected static ?int $navigationSort = -1;

    /**
     * @var array<string, mixed>
     */
    public ?array $data = [];

    /**
     * Membungkus form dengan tombol Simpan di kakinya, pola yang sama
     * dipakai halaman Edit bawaan Filament. Halaman ini memakai view
     * bawaan Filament, jadi tidak ada Blade kustom yang perlu dirawat.
     */
    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Form::make([EmbeddedSchema::make('form')])
                    ->id('form')
                    ->livewireSubmitHandler('save')
                    ->footer([
                        Actions::make($this->getFormActions())->key('form-actions'),
                    ]),
            ]);
    }

    public function mount(): void
    {
        $profil = Profile::ambil();
        $teks = SiteText::whereIn('kunci', ['perkenalan', 'penutup'])->get()->keyBy('kunci');

        $this->form->fill([
            'foto' => $profil->foto,
            'email' => $profil->email,
            'linkedin' => $profil->linkedin,
            'github' => $profil->github,
            'tiktok' => $profil->tiktok,
            'instagram' => $profil->instagram,
            'youtube' => $profil->youtube,
            'perkenalan_id' => $teks->get('perkenalan')?->nilai_id,
            'perkenalan_en' => $teks->get('perkenalan')?->nilai_en,
            'penutup_id' => $teks->get('penutup')?->nilai_id,
            'penutup_en' => $teks->get('penutup')?->nilai_en,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->columns(1)
            ->components([
                Section::make('Foto profil')
                    ->description('Diproses jadi tiga ukuran WebP seperti gambar lain. Kosongkan kalau belum ada — beranda menampilkan kotak inisial sebagai gantinya.')
                    ->schema([
                        FileUpload::make('foto')
                            ->label('')
                            ->image()
                            ->disk('public')
                            ->maxSize(config('media.batas_unggah_kb'))
                            ->saveUploadedFileUsing(function (TemporaryUploadedFile $file): string {
                                return app(PipelineGambar::class)->proses($file, 'profil')['thumb'];
                            })
                            ->columnSpanFull(),
                    ]),

                Section::make('Tautan sosial')
                    ->description('Tautan yang dikosongkan tidak dirender di beranda — tombolnya hilang, bukan tampil mati.')
                    ->schema([
                        TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->maxLength(255)
                            ->helperText('Dipakai tombol Email dan tombol "Kirim email" di bagian penutup.'),
                        TextInput::make('linkedin')->label('LinkedIn')->url()->maxLength(255),
                        TextInput::make('github')->label('GitHub')->url()->maxLength(255),
                        TextInput::make('tiktok')->label('TikTok')->url()->maxLength(255),
                        TextInput::make('instagram')->label('Instagram')->url()->maxLength(255),
                        TextInput::make('youtube')->label('YouTube')->url()->maxLength(255),
                    ])
                    ->columns(2),

                Section::make('Teks beranda')
                    ->description('Bahasa Inggris opsional. Beranda jatuh ke bahasa Indonesia kalau kosong, lihat docs/fitur/05-dwibahasa.md.')
                    ->schema([
                        Textarea::make('perkenalan_id')
                            ->label('Perkenalan — Indonesia')
                            ->required()
                            ->rows(5),
                        Textarea::make('perkenalan_en')
                            ->label('Perkenalan — Inggris')
                            ->rows(5),
                        Textarea::make('penutup_id')
                            ->label('Penutup — Indonesia')
                            ->required()
                            ->rows(5),
                        Textarea::make('penutup_en')
                            ->label('Penutup — Inggris')
                            ->rows(5),
                    ])
                    ->columns(2),
            ]);
    }

    public function save(): void
    {
        $data = $this->form->getState();

        Profile::ambil()->update([
            'foto' => $data['foto'] ?: null,
            'email' => $data['email'] ?: null,
            'linkedin' => $data['linkedin'] ?: null,
            'github' => $data['github'] ?: null,
            'tiktok' => $data['tiktok'] ?: null,
            'instagram' => $data['instagram'] ?: null,
            'youtube' => $data['youtube'] ?: null,
        ]);

        foreach (['perkenalan', 'penutup'] as $kunci) {
            SiteText::updateOrCreate(
                ['kunci' => $kunci],
                [
                    'nilai_id' => $data["{$kunci}_id"],
                    'nilai_en' => $data["{$kunci}_en"] ?: null,
                ],
            );
        }

        Notification::make()->success()->title('Beranda tersimpan')->send();
    }

    /**
     * @return array<Action>
     */
    public function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->submit('save'),
        ];
    }
}
