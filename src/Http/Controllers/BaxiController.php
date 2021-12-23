<?php

namespace Credpal\Expense\Http\Controllers;

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Http\Requests\Baxi\AirtimeRequest;
use Credpal\Expense\Http\Requests\Baxi\DataBundleRequest;
use Credpal\Expense\Http\Requests\Baxi\ElectricityRequest;
use Credpal\Expense\Http\Requests\Baxi\MultichoiceAddonRequest;
use Credpal\Expense\Http\Requests\Baxi\MutltichoiceBundleRequest;
use Credpal\Expense\Http\Requests\Baxi\VerifyAccountDetailsRequest;
use Credpal\Expense\Http\Requests\Baxi\VerifyElectricityUserRequest;
use Credpal\Expense\Services\Bills\BaxiService;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class BaxiController extends Controller
{
	protected array $requestBody;

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getAllBillTransactions(): JsonResponse
	{
		$url = config('expense.expense.base_url') . '/bills/baxi';
		$result = sendRequestAndThrowExceptionOnFailure($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBillers(): JsonResponse
	{
		$url = config('expense.expense.base_url') . '/bills/baxi/billers';
		$result = sendRequestAndThrowExceptionOnFailure($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBillerServices(): JsonResponse
	{
		$url = config('expense.expense.base_url') . '/bills/baxi/biller-services';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getAllBillerCategory(): JsonResponse
	{
		$url = config('expense.expense.base_url') . '/bills/baxi/biller-categories';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param string $category
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBillerByCategory(string $category): JsonResponse
	{
		$url = config('expense.expense.base_url') . "/bills/baxi/biller-by-category/{$category}";
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getAirtimeProviders(): JsonResponse
	{
		$url = config('expense.expense.base_url') . '/bills/baxi/airtime-providers';
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
		$url = config('expense.expense.base_url') . '/bills/baxi/databundle-providers';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param string $provider
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getBundleByProvider(string $provider): JsonResponse
	{
		$url = config('expense.expense.base_url') . "/bills/baxi/provider-bundles/{$provider}";
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
		$this->requestBody = [
			'service_type' => $request['service_type'],
			'account_number' => $request['account_number'],
		];
		$url = config('expense.expense.base_url') . '/bills/baxi/verify-account-details';
		$result = sendRequestTo($url, $this->requestBody, getPrivateKey(Enum::EXPENSE));
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getCabletvProviders(): JsonResponse
	{
		$url = config('expense.expense.base_url') . '/bills/baxi/cabletv-providers';
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
		$url = config('expense.expense.base_url') . "/bills/baxi/multichoice-bundles-list/{$provider}";
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param MultichoiceAddonRequest $request
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getMultichoiceAddons(MultichoiceAddonRequest $request): JsonResponse
	{
		$this->requestBody = [
			'service_type' => $request['service_type'],
			'product_code' => $request['product_code'],
		];
		$url = config('expense.expense.base_url') . '/bills/baxi/multichoice/addons';
		$result = sendRequestTo($url, $this->requestBody, getPrivateKey(Enum::EXPENSE));
		return $this->success($result);
	}

	/**
	 * @param MutltichoiceBundleRequest $request
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function multichoiceRequest(MutltichoiceBundleRequest $request): JsonResponse
	{
		$credentials = collect($request);
		$baxi = new BaxiService($credentials);
		$result = $baxi->multichoiceRequest();
		return $this->success($result);
	}

	/**
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function getElectricityBillers(): JsonResponse
	{
		$url = config('expense.expense.base_url') . '/bills/baxi/electricity-billers';
		$result = sendRequestTo($url, null, getPrivateKey(Enum::EXPENSE), 'get');
		return $this->success($result);
	}

	/**
	 * @param VerifyElectricityUserRequest $request
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function verifyElectricityUser(VerifyElectricityUserRequest $request): JsonResponse
	{
		$this->requestBody = [
			'service_type' => $request['service_type'],
			'account_number' => $request['account_number'],
		];
		$url = config('expense.expense.base_url') . '/bills/baxi/verify-electricity-user';
		$result = sendRequestTo($url, $this->requestBody, getPrivateKey(Enum::EXPENSE));
		return $this->success($result);
	}

	/**
	 * @param ElectricityRequest $request
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function electricityRequest(ElectricityRequest $request): JsonResponse
	{
		$credentials = collect($request);
		$baxi = new BaxiService($credentials);
		$result = $baxi->electricityRequest();
		return $this->success($result);
	}
}
