<?php

namespace Credpal\Expense\Http\Controllers;

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Http\Requests\Baxi\AirtimeRequest;
use Credpal\Expense\Services\Bills\BaxiService;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaxiController
{
	public function airtimeRequest(AirtimeRequest $request)
	{
		$credentials = collect($request);
		$baxi = new BaxiService($credentials);
		$result = $baxi->requestAirtime();
		return $this->success($result);
	}
}