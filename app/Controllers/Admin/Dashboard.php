<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();

        // 1. KPI Cards data
        $lifetimeSales = $db->table('orders')
                            ->whereIn('status', ['complete', 'processing'])
                            ->selectSum('grand_total')
                            ->get()
                            ->getRow()
                            ->grand_total ?? 0;

        $totalOrders = $db->table('orders')->countAll();
        
        $averageOrder = 0;
        if ($totalOrders > 0) {
            $averageOrder = $lifetimeSales / $totalOrders;
        }

        $totalCustomers = $db->table('customers')->countAll();

        // 2. Chart Data: Past 7 days sales
        $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $dailySum = $db->table('orders')
                           ->whereIn('status', ['complete', 'processing'])
                           ->like('created_at', $date)
                           ->selectSum('grand_total')
                           ->get()
                           ->getRow()
                           ->grand_total ?? 0;
            $chartData[$date] = round($dailySum, 2);
        }

        // 3. Last 5 Orders
        $recentOrders = $db->table('orders')
                           ->orderBy('created_at', 'DESC')
                           ->limit(5)
                           ->get()
                           ->getResult();

        // 4. Bestsellers (Order Items grouped by SKU)
        $bestsellers = $db->table('order_items')
                          ->select('sku, name, SUM(qty_ordered) as total_qty, price')
                          ->groupBy('sku, name, price')
                          ->orderBy('total_qty', 'DESC')
                          ->limit(5)
                          ->get()
                          ->getResult();

        // 5. Popular Search Terms (seeded mockup values)
        $searchTerms = [
            ['term' => 'running shoes', 'uses' => 45],
            ['term' => 'leather jacket', 'uses' => 32],
            ['term' => 'laptop backpack', 'uses' => 19],
            ['term' => 'mens watches', 'uses' => 12],
            ['term' => 'white shirts', 'uses' => 8]
        ];

        $data = [
            'menu'          => 'dashboard',
            'submenu'       => '',
            'lifetimeSales' => $lifetimeSales,
            'averageOrder'  => $averageOrder,
            'totalOrders'   => $totalOrders,
            'totalCustomers'=> $totalCustomers,
            'chartLabels'   => array_keys($chartData),
            'chartValues'   => array_values($chartData),
            'recentOrders'  => $recentOrders,
            'bestsellers'   => $bestsellers,
            'searchTerms'   => $searchTerms,
        ];

        return view('admin/dashboard', $data);
    }
}
