<?php

namespace Credpal\Expense\Services;

use Credpal\Expense\Exceptions\ExpenseException;
use Credpal\Expense\Utilities\Enum;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ExpenseService
{
    private $token;
    private $headers;
    private $errors;

    public function __construct()
    {
        $this->setConfigKeys();
        $this->setHeaders();
    }
    
    private function setConfigKeys()
    {
        $this->base_url = config('expense.test.base_url');
        $this->token = config('expense.test.private_key');
        
        if (app()->environment('production')) {
            $this->base_url = config('expense.live.base_url');
            $this->token = config('expense.live.private_key');
        }
    }

    private function setHeaders(): void
    {
       $this->headers = [
            'Accept' => 'application/json',
            'Authorization' => "Bearer " . $this->token
       ];
    }
    

    private function getHeaders(): array
    {
        return $this->headers;
    }

    private function setErrors(array $errors)
    {
        $this->errors = $errors;
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function sendRequest(string $urlPath, array $data = [])
    {
        try {
            $response = Http::withToken($this->token)->withHeaders($this->getHeaders())->post($urlPath, $data);

            if (!$response->successful())
            {
                return $this->handleErrorResponse($response);
            }

            return $response->json('data');
        } catch (\Exception $e) {
            throw new ExpenseException($e->getMessage(), $e->getCode());
        }
    }

    public function searchTicket(array $data)
    {
        return $this->sendRequest($this->base_url . 'trips/search', $data);
    }
    
    public function confirmTicket(array $data)
    {
        return $this->sendRequest($this->base_url . 'trips/confirm-ticket-price', $data);
    }

    public function bookTicket(array $data)
    {
        return $this->sendRequest($this->base_url . 'trips/book-ticket', $data);
    }

    public function flightRules(array $data)
    {
        return $this->sendRequest($this->base_url . 'trips/flight-rules', $data);
    }
    
    public function flightReservations(array $data)
    {
        return $this->sendRequest($this->base_url . 'trips/my-reservation', $data);
    }

    public function cancel(array $data)
    {
        return $this->sendRequest($this->base_url . 'trips/cancel-ticket', $data);
    }

    private function handleErrorResponse(Response $response)
    {
        if ($response->status() == SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY) {
            $this->setErrors($response->json()['errors']);
            throw new ExpenseException($response->json()['message'], SymfonyResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        throw new ExpenseException($response->json()["message"], $response->status());
    }
}