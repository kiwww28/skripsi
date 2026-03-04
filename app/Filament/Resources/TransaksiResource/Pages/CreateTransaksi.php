<?php

namespace App\Filament\Resources\TransaksiResource\Pages;

use App\Filament\Resources\TransaksiResource;
use App\Models\Transaksi;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateTransaksi extends CreateRecord
{
    protected static string $resource = TransaksiResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Simpan')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Transaksi Baru')
                ->modalDescription('Apakah Anda yakin ingin menyimpan transaksi ini?')
                ->modalSubmitActionLabel('Ya, Simpan')
                ->action(function () {

                    $data = $this->form->getState();

                    $record = Transaksi::create($data);

                    $this->record = $record;

                    Notification::make()
                        ->title('Transaksi berhasil dibuat')
                        ->success()
                        ->send();

                    return redirect(
                        TransaksiResource::getUrl('index')
                    );
                }),
        ];
    }
}
