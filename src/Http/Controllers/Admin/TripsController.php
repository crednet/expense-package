<?php

namespace Credpal\Expense\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Credpal\Expense\Models\Trips;

class TripsController extends Controller
{
    public function index()
    {
        return $this->datatable(
            Trips::with('user.profile', 'user.company', 'tripsTravellers'),
            [
                "search" => function ($query, $searchString) {
                    $query->whereHas('user', function ($user) use ($searchString) {
                        $user->whereRaw("CONCAT(name, ' ', last_name) like '%{$searchString}%'")
                            ->orwhere('id', 'like', "%{$searchString}%")
                            ->orwhere('last_name', 'like', "%{$searchString}%")
                            ->orwhere('email', 'like', "%{$searchString}%")
                            ->orwhere('phone_no', 'like', "%{$searchString}%")
                            ->orwhereHas('profile', function ($profile) use ($searchString) {
                                $profile->where('bvn', 'like', "%{$searchString}%");
                            });
                    });
                },
                "filters" => [
                    "success" => function ($query) {
                        $query->whereStatus('success');
                    }
                ],
                "sort" => [
                    "column" => "created_at",
                    "order" => "desc"
                ]
            ]
        );
    }
}
