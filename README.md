## Expense package
A package to communicate with the expense service

[comment]: <> ([![Latest Version]&#40;https://img.shields.io/github/issues/crednet/expense-package.svg?style=flat-square&#41;]&#40;https://github.com/crednet/expense-package/releases&#41;)
[![Latest Issues](https://img.shields.io/github/issues/crednet/expense-package?style=flat-square)](https://github.com/crednet/expense-package/issues)
[![Latest Stars](https://img.shields.io/github/stars/crednet/expense-package?style=flat-square)](https://github.com/crednet/expense-package/stargazers)


### Installation

```bash
composer require credpal/expense

php artisan vendor:publish
# This will publish a config file expense.php which you can edit/update the values
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
