<?php

namespace App\Controllers;

class Cart extends BaseController
{
    // Cart details page
    public function index()
    {
        $cart = session()->get('cart') ?: [];
        
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $data = [
            'pageLayout' => '1column',
            'cart'       => $cart,
            'subtotal'   => $subtotal
        ];

        return view('storefront/cart/index', $data);
    }

    // Add item to cart
    public function add()
    {
        $db = \Config\Database::connect();
        
        $productId = (int)$this->request->getPost('product_id');
        $qty = (int)$this->request->getPost('qty') ?: 1;

        $product = $db->table('products')->where('id', $productId)->where('status', 1)->get()->getRow();
        if (!$product) {
            session()->setFlashdata('error', 'Product not found.');
            return redirect()->back();
        }

        if ($qty > $product->qty) {
            session()->setFlashdata('error', 'We do not have enough stock of this item (' . (int)$product->qty . ' left).');
            return redirect()->back();
        }

        $cart = session()->get('cart') ?: [];
        $price = $product->special_price ?: $product->price;

        if (isset($cart[$productId])) {
            $newQty = $cart[$productId]['qty'] + $qty;
            if ($newQty > $product->qty) {
                session()->setFlashdata('error', 'Cannot add more. We only have ' . (int)$product->qty . ' in stock.');
                return redirect()->back();
            }
            $cart[$productId]['qty'] = $newQty;
        } else {
            $cart[$productId] = [
                'id'        => $product->id,
                'sku'       => $product->sku,
                'name'      => $product->name,
                'price'     => $price,
                'qty'       => $qty,
                'image_url' => $product->image_url
            ];
        }

        session()->set('cart', $cart);
        session()->setFlashdata('success', 'You added ' . esc($product->name) . ' to your shopping cart.');
        return redirect()->to(base_url('cart'));
    }

    // Update quantities of multiple items
    public function update()
    {
        $db = \Config\Database::connect();
        $qtyInput = $this->request->getPost('qty') ?: [];
        $cart = session()->get('cart') ?: [];

        foreach ($qtyInput as $id => $newQty) {
            $newQty = (int)$newQty;
            if (isset($cart[$id])) {
                $product = $db->table('products')->where('id', $id)->get()->getRow();
                if ($product && $newQty > 0) {
                    // Check stock limit
                    if ($newQty > $product->qty) {
                        session()->setFlashdata('error', 'Quantity for ' . esc($product->name) . ' exceeds available stock (' . (int)$product->qty . ').');
                        continue;
                    }
                    $cart[$id]['qty'] = $newQty;
                } elseif ($newQty <= 0) {
                    unset($cart[$id]);
                }
            }
        }

        session()->set('cart', $cart);
        session()->setFlashdata('success', 'Shopping cart quantities updated.');
        return redirect()->to(base_url('cart'));
    }

    // Remove single item
    public function remove($id)
    {
        $cart = session()->get('cart') ?: [];
        if (isset($cart[$id])) {
            $name = $cart[$id]['name'];
            unset($cart[$id]);
            session()->set('cart', $cart);
            session()->setFlashdata('success', 'You removed ' . esc($name) . ' from your shopping cart.');
        }

        return redirect()->to(base_url('cart'));
    }

    // AJAX minicart partial layout snippet
    public function minicart()
    {
        $cart = session()->get('cart') ?: [];
        
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }

        $data = [
            'cart'     => $cart,
            'subtotal' => $subtotal
        ];

        return view('storefront/cart/minicart', $data);
    }
}
