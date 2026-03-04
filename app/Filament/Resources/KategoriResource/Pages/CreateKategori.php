<?php

namespace App\Filament\Resources\KategoriResource\Pages;

use App\Filament\Resources\KategoriResource;
use App\Models\Kategori;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateKategori extends CreateRecord
{
    protected static string $resource = KategoriResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Simpan')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Kategori Baru')
                ->modalDescription('Apakah Anda yakin ingin menyimpan kategori ini?')
                ->modalSubmitActionLabel('Ya, Simpan')
                ->action(function () {
                    $data = $this->form->getState();
                    $record = Kategori::create($data);
                    $this->record = $record;

                    Notification::make()
                        ->title('Kategori berhasil dibuat')
                        ->success()
                        ->send();

                    return redirect(KategoriResource::getUrl('index'));
                }),
        ];
    }
}
