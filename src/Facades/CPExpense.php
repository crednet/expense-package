<?php

namespace Credpal\Expense\Facades;

use Illuminate\Support\Facades\Facade;

class CPExpense extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cpexpense';
    }
}