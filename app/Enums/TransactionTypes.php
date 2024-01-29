<?php

namespace App\Enums;
enum TransactionTypes : string
{
    case EXPENSE = 'expense';
    case INCOME  = 'income';
}
