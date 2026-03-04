<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Simpan')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Produk Baru')
                ->modalDescription('Apakah Anda yakin ingin menyimpan produk ini?')
                ->modalSubmitActionLabel('Ya, Simpan')
                ->action(function () {
                    $data = $this->form->getState();
                    $record = Product::create($data);
                    $this->record = $record;

                    Notification::make()
                        ->title('Produk berhasil dibuat')
                        ->success()
                        ->send();

                    return redirect(ProductResource::getUrl('index'));
                }),
        ];
    }
}
