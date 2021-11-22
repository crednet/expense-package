<?php

namespace Credpal\Expense\Http\Controllers;

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Http\Requests\Baxi\AirtimeRequest;
use Credpal\Expense\Http\Requests\Baxi\DataBundleRequest;
use Credpal\Expense\Http\Requests\Baxi\MultichoiceAddonRequest;
use Credpal\Expense\Http\Requests\Baxi\VerifyAccountDetailsRequest;
use Credpal\Expense\Services\Bills\BaxiService;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaxiController extends Controller
{
	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBillers(): JsonResponse
	{
		$url = config('expense.bills_url') . 'baxi/billers';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBillerServices(): JsonResponse
	{
		$url = config('expense.bills_url') . 'baxi/biller-services';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getAllBillerCategory(): JsonResponse
	{
		$url = config('expense.bills_url') . 'baxi/biller-categories';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param $category
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBillerByCategory($category): JsonResponse
	{
		$url = config('expense.bills_url') . "baxi/biller-by-category/{$category}";
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getAirtimeProviders(): JsonResponse
	{
		$url = config('expense.bills_url') . 'baxi/airtime-providers';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

    /**
     * @param AirtimeRequest $request
     * @return JsonResponse
	 * @throws ExpenseException
     */
    public function airtimeRequest(AirtimeRequest $request): JsonResponse
	{
        $credentials = collect($request);
        $baxi = new BaxiService($credentials);
        $result = $baxi->requestAirtime();
        return $this->success($result);
    }

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getDatabundleProviders(): JsonResponse
	{
		$url = config('expense.bills_url') . 'baxi/databundle-providers';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param $provider
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBundleByProvider($provider): JsonResponse
	{
		$url = config('expense.bills_url') . "baxi/provider-bundles/{$provider}";
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param DataBundleRequest $request
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function dataBundleRequest(DatabundleRequest $request): JsonResponse
	{
		$credentials = collect($request);
		$baxi = new BaxiService($credentials);
		$result = $baxi->requestDatabundle();
		return $this->success($result);
	}

	/**
	 * @param VerifyAccountDetailsRequest $request
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function verifyAccountDetails(VerifyAccountDetailsRequest $request): JsonResponse
	{
		$credentials = collect($request);
		$baxi = new BaxiService($credentials);
		$result = $baxi->verifyAccountDetails();
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getCabletvProviders(): JsonResponse
	{
		$url = config('expense.bills_url') . 'baxi/cabletv-providers';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param $provider
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getMultichoiceBundles($provider): JsonResponse
	{
		$url = config('expense.bills_url') . "baxi/multichoice-bundles-list/{$provider}";
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	public function getMultichoiceAddons(MultichoiceAddonRequest $request): JsonResponse
	{
		$credentials = collect($request);
		$baxi = new BaxiService($credentials);
		$result = $baxi->getMultichoiceAddons();
		return $this->success($result);
	}

	public function multichoiceRequest(MutltichoiceBundleRequest $request): JsonResponse
	{
		$credentials = collect($request);
		$baxi = new BaxiService($credentials);
		$result = $baxi->multichoiceRequest();
		return $this->success($result);
	}
}
