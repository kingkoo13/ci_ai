<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Shipments extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $shipments = $db->table('shipments')
                        ->select('shipments.*, orders.increment_id as order_increment_id, orders.customer_firstname, orders.customer_lastname, orders.created_at as order_created_at')
                        ->join('orders', 'orders.id = shipments.order_id')
                        ->orderBy('shipments.created_at', 'DESC')
                        ->get()
                        ->getResult();

        $data = [
            'menu'      => 'sales',
            'submenu'   => 'shipments',
            'shipments' => $shipments,
        ];

        return view('admin/sales/shipments/grid', $data);
    }

    public function view($id)
    {
        $db = \Config\Database::connect();
        
        $shipment = $db->table('shipments')->where('id', $id)->get()->getRow();
        if (!$shipment) {
            session()->setFlashdata('error', 'Shipment not found.');
            return redirect()->to(base_url('admin/sales/shipments'));
        }

        $order = $db->table('orders')->where('id', $shipment->order_id)->get()->getRow();
        $items = $db->table('order_items')->where('order_id', $shipment->order_id)->get()->getResult();

        $data = [
            'menu'     => 'sales',
            'submenu'  => 'shipments',
            'shipment' => $shipment,
            'order'    => $order,
            'items'    => $items,
        ];

        return view('admin/sales/shipments/view', $data);
    }
}
