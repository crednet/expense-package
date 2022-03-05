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

		$this->registerConfig()->registerMigrations();
	}

	protected function registerConfig(): ExpenseServiceProvider
	{
		if ($this->app->runningInConsole()) {
			$this->publishes([
				__DIR__ . '/Config/expense.php' => config_path('expense.php'),
			], 'expense');
		}

		return $this;
	}

	protected function registerMigrations(): ExpenseServiceProvider
	{
		if ($this->app->runningInConsole()) {
			if (!class_exists('CreateTripsTable')) {
				$this->publishes([
					__DIR__ . '/Database/migrations/create_trips_table.stub' =>
						database_path('migrations/' . date('Y_m_d_His') . '_create_trips_table.php'),
				], 'migrations');
			}
			if (!class_exists('CreateTripTravellersTable')) {
				$this->publishes([
					__DIR__ . '/Database/migrations/create_trip_travellers_table.stub' =>
						database_path('migrations/' . date('Y_m_d_His') . '_create_trip_travellers_table.php'),
				], 'migrations');
			}
		}
	}
}
