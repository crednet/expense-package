<?php

namespace Credpal\Expense\Traits;

use Credpal\Expense\Exceptions\ExpenseException;
use Illuminate\Http\Response;

trait ExpenseError
{

    /**
     * @throws ExpenseException
     */
    public static function abortIfNoServiceIsSpecified(): void
    {
        throw new ExpenseException(
            trans('expense::exception.no_service'),
            Response::HTTP_PRECONDITION_FAILED
        );
    }

    /**
     * @param null|string $message
     * @throws ExpenseException
     */
    public static function abortIfUnsuccessfulResponse($message = null, $code = Response::HTTP_FAILED_DEPENDENCY): void
    {
        $msg = ($message) ?: trans('expense::exception.unsuccessful_transaction');
        throw new ExpenseException(
            $msg,
            $code
        );
    }
    
    public static function setErrors(array $errors)
    {
        self::$error = $errors;
    }

    /**
     *
     */
    public static function errors()
    {
        return self::$error;
    }
    
    
}
