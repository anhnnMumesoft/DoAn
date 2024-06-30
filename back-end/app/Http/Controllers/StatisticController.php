<?php

namespace App\Http\Controllers;

use App\Services\StatisticService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StatisticController extends Controller
{
    protected $statisticService;

    public function __construct(StatisticService $statisticService)
    {
        $this->statisticService = $statisticService;
    }

    public function getStatisticStockProduct(Request $request)
    {
        $data = $request->only(['limit', 'offset']);
        $result = $this->statisticService->getStatisticStockProduct($data);
        return response()->json($result);
    }
    public function getCountCardStatistic()
    {
        $response = $this->statisticService->getCountCardStatistic();
        return $response;
    }
    public function getCountStatusOrder(Request $request)
    {
        $data = $request->only(['oneDate', 'twoDate', 'type']);
        $response = $this->statisticService->getCountStatusOrder($data);
        return $response;
    }
    public function getStatisticByMonth(Request $request)
    {
        $data = $request->all();
        $response = $this->statisticService->getStatisticByMonth($data);
        return response()->json($response);
    }
    public function getStatisticByDay(Request $request)
    {
        $data = $request->all();
        $response = $this->statisticService->getStatisticByDay($data);
        return response()->json($response);
    }
    public function getStatisticOverturn(Request $request)
    {
        $data = $request->all();
        $result = $this->statisticService->getStatisticOverturn($data);

        return response()->json($result);
    }

    public function getStatisticProfit(Request $request)
    {
        $data = $request->all();
        $result = $this->statisticService->getStatisticProfit($data);

        return response()->json($result);
    }
}
