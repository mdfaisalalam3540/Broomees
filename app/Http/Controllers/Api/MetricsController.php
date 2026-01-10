<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MetricsController extends Controller
{
    public function reputation(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'average_reputation' => User::avg('reputation_score'),
            'median_reputation' => $this->calculateMedianReputation(),
            'max_reputation' => User::max('reputation_score'),
            'min_reputation' => User::min('reputation_score'),
            'std_dev_reputation' => $this->calculateStdDevReputation(),
            'reputation_distribution' => $this->getReputationDistribution(),
        ];

        return response()->json($stats);
    }

    private function calculateMedianReputation()
    {
        $count = User::count();
        $median = User::orderBy('reputation_score')
            ->skip(floor($count / 2))
            ->take(1)
            ->value('reputation_score');

        return $median;
    }

    private function calculateStdDevReputation()
    {
        $avg = User::avg('reputation_score');
        $variance = User::selectRaw('AVG(POW(reputation_score - ?, 2)) as variance', [$avg])
            ->value('variance');

        return $variance ? sqrt($variance) : 0;
    }

    private function getReputationDistribution()
    {
        return [
            '0-5' => User::whereBetween('reputation_score', [0, 5])->count(),
            '5-10' => User::whereBetween('reputation_score', [5, 10])->count(),
            '10-20' => User::whereBetween('reputation_score', [10, 20])->count(),
            '20+' => User::where('reputation_score', '>', 20)->count(),
        ];
    }
}