<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [ 'status', 'transaction_id', 'payment_method', 'created_by', 'created_at' ];

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }
}
