<?php

namespace App\Filament\Pages;

use App\Models\Langganan;
use App\Models\Pengaturan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Actions;
use Filament\Schemas\Components\EmbeddedSchema;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

/** Superadmin menyalakan/mematikan langganan belajar dan mengatur harganya. */
class PengaturanLangganan extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?string $navigationLabel = 'Langganan belajar';

    protected static ?string $title = 'Langganan belajar';

    public ?array $data = [];

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->superadmin();
    }

    public function mount(): void
    {
        $this->form->fill(['berbayar' => Langganan::berbayar(), 'harga' => Langganan::harga()]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Toggle::make('berbayar')
                    ->label('Langganan aktif')
                    ->helperText('Mati = semua surat di fitur belajar gratis untuk akun yang login, dan halaman pembayaran ditutup. Langganan yang sudah dibayar tetap tercatat.'),
                TextInput::make('harga')
                    ->label('Harga per bulan')
                    ->prefix('Rp')
                    ->integer()
                    ->required()
                    ->minValue(1000)
                    ->helperText('Berlaku untuk tagihan baru; tagihan yang sudah dibuat tidak berubah.'),
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
        $data = $this->form->getState();
        Pengaturan::simpan('langganan_berbayar', (bool) $data['berbayar']);
        Pengaturan::simpan('langganan_harga', (int) $data['harga']);

        Notification::make()->success()->title('Pengaturan langganan tersimpan')->send();
    }
}
