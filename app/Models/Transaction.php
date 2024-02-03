<?php

namespace App\Models;

use App\Enums\TransactionTypes as EnumsTransactionTypes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TransactionTypes;

class Transaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [ 'type', 'amount', 'description', 'user_id', 'date_transaction', 'created_by' ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted(): void
    {
        static::created(function (Transaction $transaction) {
            $balance = Balance::where('user_id', auth()->user()->id)->first();

            if ($transaction->type == EnumsTransactionTypes::EXPENSE->value) {
                $balance->update([
                    'total_amount' =>  $balance->total_amount - $transaction->amount,
                ]);
            } else if ($transaction->type == EnumsTransactionTypes::INCOME->value) {
                $balance->update([
                    'total_amount' =>  $balance->total_amount + $transaction->amount,
                ]);
            }
            
        });
    }
}
