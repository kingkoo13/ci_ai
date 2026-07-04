<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Orders extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $orders = $db->table('orders')
                     ->orderBy('created_at', 'DESC')
                     ->get()
                     ->getResult();

        $data = [
            'menu'    => 'sales',
            'submenu' => 'orders',
            'orders'  => $orders,
        ];

        return view('admin/sales/orders/grid', $data);
    }

    public function view($id)
    {
        $db = \Config\Database::connect();

        // Fetch Order details
        $order = $db->table('orders')->where('id', $id)->get()->getRow();
        if (!$order) {
            session()->setFlashdata('error', 'Order not found.');
            return redirect()->to(base_url('admin/sales/orders'));
        }

        // Fetch Customer details & addresses
        $customer = null;
        $address = null;
        if ($order->customer_id) {
            $customer = $db->table('customers')->where('id', $order->customer_id)->get()->getRow();
            $address = $db->table('customer_addresses')
                          ->where('customer_id', $order->customer_id)
                          ->where('is_default_billing', 1)
                          ->get()
                          ->getRow();
        }

        // Fetch items ordered
        $items = $db->table('order_items')->where('order_id', $id)->get()->getResult();

        // Fetch existing Invoices and Shipments
        $invoices = $db->table('invoices')->where('order_id', $id)->get()->getResult();
        $shipments = $db->table('shipments')->where('order_id', $id)->get()->getResult();

        $data = [
            'menu'      => 'sales',
            'submenu'   => 'orders',
            'order'     => $order,
            'customer'  => $customer,
            'address'   => $address,
            'items'     => $items,
            'invoices'  => $invoices,
            'shipments' => $shipments,
        ];

        return view('admin/sales/orders/view', $data);
    }

    public function invoice($id)
    {
        $db = \Config\Database::connect();
        $order = $db->table('orders')->where('id', $id)->get()->getRow();

        if (!$order || $order->status !== 'pending') {
            session()->setFlashdata('error', 'Invoice cannot be generated for this order.');
            return redirect()->back();
        }

        $db->transStart();

        // Update items invoiced qty
        $db->table('order_items')
           ->where('order_id', $id)
           ->update(['qty_invoiced' => $db->raw('qty_ordered')]);

        // Generate Invoice record
        $invoiceIncrementId = '30000' . str_pad($id, 4, '0', STR_PAD_LEFT);
        $db->table('invoices')->insert([
            'increment_id' => $invoiceIncrementId,
            'order_id'     => $id,
            'grand_total'  => $order->grand_total,
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        // Update Order status to processing
        $db->table('orders')
           ->where('id', $id)
           ->update([
               'status'     => 'processing',
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Failed to generate invoice.');
        } else {
            session()->setFlashdata('success', 'Invoice #' . $invoiceIncrementId . ' has been created successfully.');
        }

        return redirect()->to(base_url('admin/sales/orders/view/' . $id));
    }

    public function ship($id)
    {
        $db = \Config\Database::connect();
        $order = $db->table('orders')->where('id', $id)->get()->getRow();

        if (!$order || $order->status !== 'processing') {
            session()->setFlashdata('error', 'Shipment cannot be created for this order.');
            return redirect()->back();
        }

        $trackingNumber = $this->request->getPost('tracking_number');
        $carrier = $this->request->getPost('carrier') ?: 'Custom';

        if (empty($trackingNumber)) {
            session()->setFlashdata('error', 'Tracking number is required to ship.');
            return redirect()->back();
        }

        $db->transStart();

        // Update items shipped qty
        $db->table('order_items')
           ->where('order_id', $id)
           ->update(['qty_shipped' => $db->raw('qty_ordered')]);

        // Generate Shipment record
        $shipmentIncrementId = '40000' . str_pad($id, 4, '0', STR_PAD_LEFT);
        $db->table('shipments')->insert([
            'increment_id' => $shipmentIncrementId,
            'order_id'     => $id,
            'tracks'       => json_encode([['carrier' => $carrier, 'number' => $trackingNumber]]),
            'created_at'   => date('Y-m-d H:i:s')
        ]);

        // Update Order status to complete
        $db->table('orders')
           ->where('id', $id)
           ->update([
               'status'     => 'complete',
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Failed to ship order.');
        } else {
            session()->setFlashdata('success', 'Shipment #' . $shipmentIncrementId . ' has been created successfully.');
        }

        return redirect()->to(base_url('admin/sales/orders/view/' . $id));
    }

    public function cancel($id)
    {
        $db = \Config\Database::connect();
        $order = $db->table('orders')->where('id', $id)->get()->getRow();

        if (!$order || !in_array($order->status, ['pending', 'processing'])) {
            session()->setFlashdata('error', 'This order cannot be canceled.');
            return redirect()->back();
        }

        $db->transStart();

        // Update status to canceled
        $db->table('orders')
           ->where('id', $id)
           ->update([
               'status'     => 'canceled',
               'updated_at' => date('Y-m-d H:i:s')
           ]);

        // Restock products qty
        $items = $db->table('order_items')->where('order_id', $id)->get()->getResult();
        foreach ($items as $item) {
            if ($item->product_id) {
                $db->table('products')
                   ->where('id', $item->product_id)
                   ->increment('qty', $item->qty_ordered);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Failed to cancel order.');
        } else {
            session()->setFlashdata('success', 'Order #' . $order->increment_id . ' has been canceled.');
        }

        return redirect()->to(base_url('admin/sales/orders/view/' . $id));
    }
}
