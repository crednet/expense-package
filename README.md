## Expense package
A package to communicate with the expense service

### Installation

```bash
composer require credpal/expense
```
Add this code to handler method in app/Exceptions/Handler.php
```php
    /**
     * Render an exception into an HTTP response.
     * @param Request $request
     * @throws Throwable
     * @return Response
     */
    public function render($request, Throwable $e)
    {
        // from here
        if ($e instanceof \Credpal\Expense\Exceptions\ExpenseException) {
            $code = $e->getCode() ?: 500;
            if ($code < 500) {
                $response = [
                    "success" => false,
                    "message" => $e->getMessage(),
                    "data" => null
                ];
                return response()->json($response, $code);
            }
        }
        //to here

        return parent::render($request, $e);
    }
```
