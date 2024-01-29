<?php

use App\Enums\TransactionTypes;
use App\Models\Balance;
use App\Models\Transaction;
use Filament\Pages\Dashboard;
use Illuminate\Database\Eloquent\Collection;

use function Pest\Laravel\get;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertIsInt;
use function PHPUnit\Framework\assertIsNumeric;
use function PHPUnit\Framework\assertSame;

uses()->group('dashboard');

it('has dashboard page', function () {
    get("/")->assertOk();
});

it('can render page', function () {
    get(Dashboard::getUrl())->assertSuccessful();
});

it('can render balance with user', function () {
    $balance = Balance::where('user_id', auth()->user()->id)->first()->total_amount;
    assertIsNumeric($balance);
});

it('can render all transaction done for users', function () {
    $transactions = Transaction::count();
    assertIsInt($transactions);
});

it('can render all transactions with type expense', function () {
    $transactionExpense = Transaction::where('type', TransactionTypes::EXPENSE->value)->get();

    collect($transactionExpense)->each(function ($transaction) {
        expect($transaction['type'])->toBe('expense');
    });
});

it('can render all transactions with type expense are collection', function () {
    $transactionExpense = Transaction::where('type', TransactionTypes::EXPENSE->value)->get();

    expect($transactionExpense)->toBeInstanceOf(Collection::class);
});

it('can render all transaction with type income', function () {
    $transactionIncome= Transaction::where('type', TransactionTypes::INCOME->value)->get();

    collect($transactionIncome)->each(function ($transaction) {
        expect($transaction['type'])->toBe('income');
    });
});

it('can render all transactions with type income are collection', function () {
    $transactionExpense = Transaction::where('type', TransactionTypes::INCOME->value)->get();

    expect($transactionExpense)->toBeInstanceOf(Collection::class);
});
