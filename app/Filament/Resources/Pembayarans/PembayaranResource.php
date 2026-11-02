<?php

namespace App\Filament\Resources\Pembayarans;

use App\Enums\StatusBayar;
use App\Filament\Resources\Pembayarans\Pages\ManagePembayarans;
use App\Models\Pembayaran;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

/** Superadmin mencocokkan nominal unik dengan mutasi QRIS/rekening, lalu menyetujui → langganan bertambah. */
class PembayaranResource extends Resource
{
    protected static ?string $model = Pembayaran::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static ?string $modelLabel = 'pembayaran';

    protected static ?string $pluralModelLabel = 'pembayaran';

    public static function canAccess(): bool
    {
        return (bool) auth()->user()?->superadmin();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getNavigationBadge(): ?string
    {
        return Pembayaran::where('status', StatusBayar::Dilaporkan)->count() ?: null;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Dibuat')->since()->sortable(),
                TextColumn::make('user.email')->label('Akun')->searchable()->copyable(),
                TextColumn::make('nominal')->formatStateUsing(fn (int $state) => 'Rp '.number_format($state, 0, ',', '.'))->searchable(),
                TextColumn::make('bulan')->suffix(' bln'),
                TextColumn::make('metode')->placeholder('—'),
                TextColumn::make('pengirim')->placeholder('—'),
                TextColumn::make('bukti')->placeholder('—')
                    ->formatStateUsing(fn () => 'Lihat')
                    ->url(fn (Pembayaran $record) => $record->bukti ? Storage::temporaryUrl($record->bukti, now()->addMinutes(10)) : null, shouldOpenInNewTab: true),
                TextColumn::make('status')->badge(),
            ])
            ->filters([
                SelectFilter::make('status')->options(StatusBayar::class),
            ])
            ->recordActions([
                Action::make('setujui')
                    ->label('Setujui')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalDescription(fn (Pembayaran $record) => "Pastikan Rp ".number_format($record->nominal, 0, ',', '.')." sudah masuk. Langganan {$record->user->email} bertambah {$record->bulan} bulan.")
                    ->visible(fn (Pembayaran $record) => $record->status->terbuka())
                    ->action(function (Pembayaran $record) {
                        $record->setujui();
                        Notification::make()->success()->title('Langganan aktif sampai '.$record->user->langganan->berakhir_pada->translatedFormat('j F Y'))->send();
                    }),
                Action::make('tolak')
                    ->label('Tolak')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Pembayaran $record) => $record->status->terbuka())
                    ->action(fn (Pembayaran $record) => $record->update(['status' => StatusBayar::Ditolak])),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePembayarans::route('/'),
        ];
    }
}
