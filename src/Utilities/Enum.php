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
	public const DATABUNDLE = 'data bundle';
	public const VERIFY_ACCOUNT = 'account verification';
	public const MULTICHOICE_ADDON = 'multichoice addons';
	public const MULTICHOICE_SUBSCRIPTION = 'multichoice subscription';
	public const ELECTRICITY_REQUEST = 'electricity request';
	public const VERIFY_ELECTRICITY = 'verify electricity';
}
