<?php

namespace App\Filament\Pages;

use App\Enums\QoriBelajar;
use App\Models\Pengaturan;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/** Superadmin menonaktifkan qori di fitur belajar, mis. karena izin audionya belum ada. */
class PengaturanQori extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSpeakerWave;

    protected static ?string $navigationLabel = 'Qori belajar';

    protected static ?string $title = 'Qori di fitur belajar';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->superadmin();
    }

    public function mount(): void
    {
        $this->form->fill(['nonaktif' => Pengaturan::ambil('qori_belajar_nonaktif', [])]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                CheckboxList::make('nonaktif')
                    ->label('Qori yang dinonaktifkan')
                    ->helperText('Qori yang dicentang disembunyikan dari halaman belajar. Minimal satu qori harus tetap aktif.')
                    ->options(collect(QoriBelajar::cases())->mapWithKeys(fn (QoriBelajar $q) => [$q->value => $q->nama()]))
                    ->rule(fn () => function (string $atribut, mixed $nilai, Closure $gagal) {
                        if (count($nilai ?? []) >= count(QoriBelajar::cases())) {
                            $gagal('Minimal satu qori harus tetap aktif.');
                        }
                    }),
            ])
            ->statePath('data');
    }

    public function content(Schema $schema): Schema
    {
        return $schema->components([
            Form::make([EmbeddedSchema::make('form')])
                ->id('form')
                ->livewireSubmitHandler('simpan')
                ->footer([Actions::make([Action::make('simpan')->label('Simpan')->submit('simpan')])]),
        ]);
    }

    public function simpan(): void
    {
        Pengaturan::simpan('qori_belajar_nonaktif', array_values($this->form->getState()['nonaktif'] ?? []));

        Notification::make()->success()->title('Pengaturan qori tersimpan')->send();
    }
}
