<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function fillForm(): void
    {
        $data = $this->record->toArray();

        foreach ($this->record->invoiceProduct as $index => $invoiceProduct) {
            $data['InvoiceProduct'][$index] = $invoiceProduct->toArray();
        }

        $this->form->fill($data);
    }
}
