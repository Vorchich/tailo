<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class SeamstressStatisticController extends Controller
{
    public function incrementView(User $user)
    {
        $user->incrementViews();

        return response()->json([
            'message' => 'Views increment successfully',
            'result' => true,
        ]);
    }

    public function resetViews(User $user)
    {
        $user->resetViews();

        return response()->json([
            'message' => 'Views reset successfully',
            'result' => true,
        ]);
    }

    public function statistic(User $user)
    {
        return response([
            'data' => [
                'statistic' => [
                    'views' => $user->views,
                    'rating' => $user->getRatingAttribute(),
                    'successful_orders' => $user->seamstressOrders()->where('status', 'success')->count(),
                    'reviews' => $user->reviews()->count(),
                ],
                'result' => true,
            ]
        ]);
    }
}
