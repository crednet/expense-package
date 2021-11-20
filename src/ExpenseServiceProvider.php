<?php

namespace Credpal\Expense;

use Credpal\Expense\Services\ExpenseService;
use Illuminate\Support\ServiceProvider;

class ExpenseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        $this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'expense');
        $this->mergeConfigFrom(__DIR__ . '/config/expense.php', 'expense');
        $this->publishes([
            __DIR__ . '/config/expense.php' => config_path('expense.php'),
            ]);
    }
}
