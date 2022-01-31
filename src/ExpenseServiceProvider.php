<?php

namespace Credpal\Expense;

use Credpal\Expense\Services\ExpenseProcess;
use Illuminate\Support\ServiceProvider;

class ExpenseServiceProvider extends ServiceProvider
{
	public function register(): void
	{
		$this->app->singleton('expense',  ExpenseProcess::class);
		$this->mergeConfigFrom(__DIR__ . '/Config/expense.php', 'expense');
	}

	public function boot(): void
	{
		$this->loadRoutesFrom(__DIR__ . '/routes/api.php');
		$this->loadTranslationsFrom(__DIR__ . '/resources/lang', 'expense');
		$this->publishes([
			__DIR__ . '/Config/expense.php' => config_path('expense.php'),
		]);
	}
}
