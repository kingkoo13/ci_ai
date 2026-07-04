<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Invoices extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $invoices = $db->table('invoices')
                       ->select('invoices.*, orders.increment_id as order_increment_id, orders.customer_firstname, orders.customer_lastname, orders.created_at as order_created_at')
                       ->join('orders', 'orders.id = invoices.order_id')
                       ->orderBy('invoices.created_at', 'DESC')
                       ->get()
                       ->getResult();

        $data = [
            'menu'     => 'sales',
            'submenu'  => 'invoices',
            'invoices' => $invoices,
        ];

        return view('admin/sales/invoices/grid', $data);
    }

    public function view($id)
    {
        $db = \Config\Database::connect();
        
        $invoice = $db->table('invoices')->where('id', $id)->get()->getRow();
        if (!$invoice) {
            session()->setFlashdata('error', 'Invoice not found.');
            return redirect()->to(base_url('admin/sales/invoices'));
        }

        $order = $db->table('orders')->where('id', $invoice->order_id)->get()->getRow();
        $items = $db->table('order_items')->where('order_id', $invoice->order_id)->get()->getResult();

        $data = [
            'menu'     => 'sales',
            'submenu'  => 'invoices',
            'invoice'  => $invoice,
            'order'    => $order,
            'items'    => $items,
        ];

        return view('admin/sales/invoices/view', $data);
    }
}
