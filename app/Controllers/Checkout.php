<?php

namespace App\Controllers;

class Checkout extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $cart = session()->get('cart') ?: [];
        if (empty($cart)) {
            session()->setFlashdata('error', 'Your shopping cart is empty.');
            return redirect()->to(base_url('cart'));
        }

        // Subtotal calculation
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
        $shipping = 10.00;
        $grandTotal = $subtotal + $shipping;

        // Logged-in customer handling
        $customerId = session()->get('customer_id');
        $customer = null;
        $defaultAddress = null;
        if ($customerId) {
            $customer = $db->table('customers')->where('id', $customerId)->get()->getRow();
            $defaultAddress = $db->table('customer_addresses')
                                 ->where('customer_id', $customerId)
                                 ->where('is_default_shipping', 1)
                                 ->get()
                                 ->getRow();
        }

        // Fetch custom Order EAV Attributes (e.g. delivery date, gift wrap msg)
        $orderAttributes = $db->table('eav_attributes')
                              ->where('entity_type', 'order')
                              ->get()
                              ->getResult();

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            // Validate checkout parameters
            $validation = \Config\Services::validation();
            $validation->setRules([
                'email'      => 'required|valid_email',
                'first_name' => 'required',
                'last_name'  => 'required',
                'street'     => 'required',
                'city'       => 'required',
                'postcode'   => 'required',
                'telephone'  => 'required'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', 'Please correct the errors in the address form.');
                return redirect()->back()->withInput();
            }

            // Start Order Creation Transaction
            $db->transStart();

            // 1. Insert order details
            $orderData = [
                'customer_id'          => $customerId ?: null,
                'customer_email'       => $this->request->getPost('email'),
                'customer_firstname'   => $this->request->getPost('first_name'),
                'customer_lastname'    => $this->request->getPost('last_name'),
                'status'               => 'pending',
                'subtotal'             => $subtotal,
                'shipping_amount'      => $shipping,
                'shipping_description' => 'Flat Rate - Fixed',
                'grand_total'          => $grandTotal,
                'created_at'           => date('Y-m-d H:i:s'),
                'updated_at'           => date('Y-m-d H:i:s')
            ];
            $db->table('orders')->insert($orderData);
            $orderId = $db->insertID();

            // Set unique increment ID
            $incrementId = strval(100000000 + $orderId);
            $db->table('orders')->where('id', $orderId)->update(['increment_id' => $incrementId]);

            // 2. Insert order items and deduct stock qty
            foreach ($cart as $item) {
                // Deduct stock
                $product = $db->table('products')->where('id', $item['id'])->get()->getRow();
                if ($product) {
                    $newQty = max(0, $product->qty - $item['qty']);
                    $inStock = $newQty > 0 ? 1 : 0;
                    $db->table('products')->where('id', $item['id'])->update([
                        'qty'         => $newQty,
                        'is_in_stock' => $inStock
                    ]);
                }

                $db->table('order_items')->insert([
                    'order_id'      => $orderId,
                    'product_id'    => $item['id'],
                    'sku'           => $item['sku'],
                    'name'          => $item['name'],
                    'price'         => $item['price'],
                    'qty_ordered'   => $item['qty'],
                    'qty_invoiced'  => 0,
                    'qty_shipped'   => 0,
                    'row_total'     => $item['price'] * $item['qty']
                ]);
            }

            // 3. Save Custom Order Attributes to EAV values table
            $customAttrInput = $this->request->getPost('attributes') ?: [];
            foreach ($orderAttributes as $attr) {
                if (isset($customAttrInput[$attr->id]) && $customAttrInput[$attr->id] !== '') {
                    $db->table('eav_attribute_values')->insert([
                        'entity_type'  => 'order',
                        'entity_id'    => $orderId,
                        'attribute_id' => $attr->id,
                        'value'        => $customAttrInput[$attr->id]
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Checkout processing failed. Please try again.');
                return redirect()->back()->withInput();
            }

            // Clean cart and store checkout success state
            session()->remove('cart');
            session()->set('last_order_id', $orderId);

            return redirect()->to(base_url('checkout/success'));
        }

        $data = [
            'pageLayout'      => '1column',
            'cart'            => $cart,
            'subtotal'        => $subtotal,
            'shipping'        => $shipping,
            'grandTotal'      => $grandTotal,
            'customer'        => $customer,
            'defaultAddress'  => $defaultAddress,
            'orderAttributes' => $orderAttributes
        ];

        return view('storefront/checkout/index', $data);
    }

    public function success()
    {
        $db = \Config\Database::connect();
        
        $orderId = session()->get('last_order_id');
        if (!$orderId) {
            return redirect()->to(base_url('/'));
        }

        $order = $db->table('orders')->where('id', $orderId)->get()->getRow();
        
        // Remove state so refreshing success page redirects to home
        session()->remove('last_order_id');

        $data = [
            'pageLayout' => '1column',
            'order'      => $order
        ];

        return view('storefront/checkout/success', $data);
    }
}
