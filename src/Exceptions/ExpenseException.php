<?php

namespace Credpal\Expense\Exceptions;

use Credpal\Expense\Traits\ExpenseError;
use Exception;
use Symfony\Component\HttpFoundation\Response;

class ExpenseException extends Exception
{
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
