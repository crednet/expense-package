<?php

namespace Credpal\Expense\Exceptions;

use Credpal\Expense\Traits\ExpenseError;
use Exception;
use App\Traits\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ExpenseException extends Exception
{
    use JsonResponse;
    
    public function render()
    {
        $response = [
            "success" => false,
            "message" => $this->getMessage(),
        ];

        if ($this->getCode() == Response::HTTP_UNPROCESSABLE_ENTITY) {
            $response["error"] = ExpenseError::errors();
        }

        return response()->json($response, $this->getCode());
    }
}
