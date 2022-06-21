<?php

namespace Credpal\Expense\Http\Controllers;

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Http\Requests\TransferRequest;
use Credpal\Expense\Services\TransferService;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class TransferController
 * @package Credpal\Expense\Http\Controllers
 */
class TransferController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return $this->success(null);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param TransferRequest $request
     * @return JsonResponse
     * @throws ExpenseException
     */
    public function store(TransferRequest $request): JsonResponse
    {
        logger('controller');
        $data = $request->only([
            'user_id',
			'wallet_id',
			'account_id',
            'wallet_type',
            'amount',
            'cbs_account_number',
            'account_number',
            'account_name',
            'bank_code'
        ]);
        $credentials = collect($data);
        $credentials->put('description', $request->description ?? null);
        $transfer = new TransferService($credentials);
        $result = $transfer->makeTransfer();
	logger('response gotten from transfer');
        return $this->success($result['data']);
    }

	/**
	 * @param TransferRequest $request
	 *
	 * @return JsonResponse
	 * @throws ExpenseException
	 */
	public function transfer(TransferRequest $request): JsonResponse
	{
		$credentials = $request->collect();
		$credentials->put('description', $request->description ?? 'Transferring funds from cash wallet');
		$transfer = new TransferService($credentials);
		$result = $transfer->makeTransfer();
		return $this->success($result['data']);
	}

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ExpenseException
     */
    public function webhook(Request $request): JsonResponse
    {
        $reference = $request['data']['reference'];
        $status = $request['data']['status'];
        $data = $request['data'];
        $serviceSignature = request()->header('x-credpal-signature');
        $privateKey = getPrivateKey(Enum::EXPENSE);
        $signature = hash_hmac('sha512', json_encode($data), $privateKey);
        $bool = hash_equals($serviceSignature, $signature);
        if (!$bool) {
            return $this->error(null, 'Unsuccessful', Response::HTTP_EXPECTATION_FAILED);
        }
        if (!$reference || !$status) {
            return $this->error(null, 'Unsuccessful', Response::HTTP_UNPROCESSABLE_ENTITY);
        }
        $result = TransferService::finalizeTransaction($reference, $status);
        return $this->success($result);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request  $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        return $this->success([]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        return $this->success([]);
    }
}
