<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use App\Filament\Resources\TransactionResource;
use App\Models\Payment;
use App\Models\Transaction;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['created_at'] = now();
        $data['created_by'] = auth()->id();

        return $data;
    }

    protected function handleRecordCreation($data): Model
    {
        $transaction = Transaction::create($data);

        Payment::create([
            'user_id' => auth()->id(),
            'transaction_id' => $transaction->id,
            'payment_method' => $data['payment_method'],
            'status' => $data['status'],
            'created_by' => auth()->id(),
            'created_at' => now(),
        ]);

        return $transaction;
    }
}
