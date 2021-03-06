<?php

namespace Credpal\Expense\Utilities;

class Enum
{
    public const DEBIT = 'debit';
    public const CREDIT = 'credit';
    public const TRANSFER = 'transfer';
    public const WALLET = 'wallet';
    public const EXPENSE = 'expense';
	public const FAILED = 'failed';
	public const PENDING = 'pending';
	public const SUCCESS = 'success';
    public const AIRTIME = 'airtime';
    public const TRIPS = 'trips';
    public const DATABUNDLE = 'data bundle';
    public const VERIFY_ACCOUNT = 'account verification';
    public const MULTICHOICE_ADDON = 'multichoice addons';
    public const MULTICHOICE_SUBSCRIPTION = 'multichoice subscription';
    public const ELECTRICITY_REQUEST = 'electricity request';
    public const VERIFY_ELECTRICITY = 'verify electricity';
	public const PRODUCTION = 'production';
    public const WALLET_TYPE_CREDIT = 'credpal_card';
    public const WALLET_TYPE_CASH= 'credpal_cash';
}
