<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use TransactionTypes;

class Transaction extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->hasOne(User::class);
    }

    protected static function booted(): void
    {
        static::created(function (Transaction $transaction) {
            $balance = Balance::where('user_id', auth()->user()->id)->first();

            if ($transaction->type == TransactionTypes::EXPENSE->value) {
                $balance->update([
                    'total_amount' =>  $balance->total_amount - $transaction->amount,
                ]);
            } else if ($transaction->type == TransactionTypes::INCOME->value) {
                $balance->update([
                    'total_amount' =>  $balance->total_amount + $transaction->amount,
                ]);
            }
            
        });
    }
}
