<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Products extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('products');

        // Capture filters from request
        $q = $this->request->getVar('q');
        $id = $this->request->getVar('filter_id');
        $sku = $this->request->getVar('filter_sku');
        $name = $this->request->getVar('filter_name');
        $priceMin = $this->request->getVar('filter_price_min');
        $priceMax = $this->request->getVar('filter_price_max');
        $qtyMin = $this->request->getVar('filter_qty_min');
        $qtyMax = $this->request->getVar('filter_qty_max');
        $status = $this->request->getVar('filter_status');

        // Apply filters
        if (!empty($q)) {
            $builder->groupStart()
                    ->like('name', $q)
                    ->orLike('sku', $q)
                    ->groupEnd();
        }
        if (!empty($id)) {
            $builder->where('id', $id);
        }
        if (!empty($sku)) {
            $builder->like('sku', $sku);
        }
        if (!empty($name)) {
            $builder->like('name', $name);
        }
        if ($priceMin !== null && $priceMin !== '') {
            $builder->where('price >=', $priceMin);
        }
        if ($priceMax !== null && $priceMax !== '') {
            $builder->where('price <=', $priceMax);
        }
        if ($qtyMin !== null && $qtyMin !== '') {
            $builder->where('qty >=', $qtyMin);
        }
        if ($qtyMax !== null && $qtyMax !== '') {
            $builder->where('qty <=', $qtyMax);
        }
        if ($status !== null && $status !== '') {
            $builder->where('status', $status);
        }

        // Clone builder for count
        $countBuilder = clone $builder;
        $totalCount = $countBuilder->countAllResults(false);

        // Pagination setup
        $perPage = 20;
        $page = (int)($this->request->getVar('page') ?: 1);
        if ($page < 1) $page = 1;
        $offset = ($page - 1) * $perPage;

        $products = $builder->orderBy('id', 'DESC')
                            ->limit($perPage, $offset)
                            ->get()
                            ->getResult();

        $totalPages = ceil($totalCount / $perPage);

        $data = [
            'menu'          => 'catalog',
            'submenu'       => 'products',
            'products'      => $products,
            'q'             => $q,
            'filter_id'     => $id,
            'filter_sku'    => $sku,
            'filter_name'   => $name,
            'filter_price_min' => $priceMin,
            'filter_price_max' => $priceMax,
            'filter_qty_min' => $qtyMin,
            'filter_qty_max' => $qtyMax,
            'filter_status' => $status,
            'page'          => $page,
            'totalPages'    => $totalPages,
            'totalCount'    => $totalCount,
        ];

        return view('admin/catalog/products/grid', $data);
    }

    public function new()
    {
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'sku'   => 'required|is_unique[products.sku]',
                'name'  => 'required',
                'price' => 'required|decimal',
                'qty'   => 'required|decimal',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $productData = [
                'sku'               => $this->request->getPost('sku'),
                'name'              => $this->request->getPost('name'),
                'description'       => $this->request->getPost('description'),
                'short_description' => $this->request->getPost('short_description'),
                'price'             => $this->request->getPost('price'),
                'special_price'     => $this->request->getPost('special_price') ?: null,
                'qty'               => $this->request->getPost('qty'),
                'is_in_stock'       => $this->request->getPost('qty') > 0 ? 1 : 0,
                'status'            => $this->request->getPost('status') ?? 1,
                'image_url'         => $this->request->getPost('image_url') ?: 'assets/images/placeholder.jpg',
                'created_at'        => date('Y-m-d H:i:s'),
                'updated_at'        => date('Y-m-d H:i:s'),
            ];

            $db->table('products')->insert($productData);
            $productId = $db->insertID();

            // Map categories
            $categoriesSelected = $this->request->getPost('categories') ?: [];
            if (!empty($categoriesSelected)) {
                $mappings = [];
                foreach ($categoriesSelected as $catId) {
                    $mappings[] = [
                        'product_id'  => $productId,
                        'category_id' => $catId
                    ];
                }
                $db->table('product_categories')->insertBatch($mappings);
            }

            session()->setFlashdata('success', 'The product has been saved successfully.');
            return redirect()->to(base_url('admin/catalog/products'));
        }

        // Fetch category list for the category selector tab
        $categories = $db->table('categories')
                         ->where('id >', 1) // skip Root Default Category
                         ->get()
                         ->getResult();

        $data = [
            'menu'       => 'catalog',
            'submenu'    => 'products',
            'categories' => $categories,
            'isEdit'     => false,
            'product'    => null,
            'mappedCats' => []
        ];

        return view('admin/catalog/products/form', $data);
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();
        
        $product = $db->table('products')->where('id', $id)->get()->getRow();
        if (!$product) {
            session()->setFlashdata('error', 'Product not found.');
            return redirect()->to(base_url('admin/catalog/products'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'sku'   => "required|is_unique[products.sku,id,{$id}]",
                'name'  => 'required',
                'price' => 'required|decimal',
                'qty'   => 'required|decimal',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $productData = [
                'sku'               => $this->request->getPost('sku'),
                'name'              => $this->request->getPost('name'),
                'description'       => $this->request->getPost('description'),
                'short_description' => $this->request->getPost('short_description'),
                'price'             => $this->request->getPost('price'),
                'special_price'     => $this->request->getPost('special_price') ?: null,
                'qty'               => $this->request->getPost('qty'),
                'is_in_stock'       => $this->request->getPost('qty') > 0 ? 1 : 0,
                'status'            => $this->request->getPost('status') ?? 1,
                'image_url'         => $this->request->getPost('image_url') ?: 'assets/images/placeholder.jpg',
                'updated_at'        => date('Y-m-d H:i:s'),
            ];

            $db->table('products')->where('id', $id)->update($productData);

            // Re-map categories (delete old, add new)
            $db->table('product_categories')->where('product_id', $id)->delete();
            $categoriesSelected = $this->request->getPost('categories') ?: [];
            if (!empty($categoriesSelected)) {
                $mappings = [];
                foreach ($categoriesSelected as $catId) {
                    $mappings[] = [
                        'product_id'  => $id,
                        'category_id' => $catId
                    ];
                }
                $db->table('product_categories')->insertBatch($mappings);
            }

            session()->setFlashdata('success', 'The product has been saved successfully.');
            return redirect()->to(base_url('admin/catalog/products'));
        }

        $categories = $db->table('categories')
                         ->where('id >', 1)
                         ->get()
                         ->getResult();

        $mappedCatObjects = $db->table('product_categories')
                              ->where('product_id', $id)
                              ->get()
                              ->getResult();
        $mappedCats = array_map(function($item) {
            return $item->category_id;
        }, $mappedCatObjects);

        $data = [
            'menu'       => 'catalog',
            'submenu'    => 'products',
            'categories' => $categories,
            'isEdit'     => true,
            'product'    => $product,
            'mappedCats' => $mappedCats
        ];

        return view('admin/catalog/products/form', $data);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        
        // Remove mappings and the product itself
        $db->table('product_categories')->where('product_id', $id)->delete();
        $db->table('products')->where('id', $id)->delete();

        session()->setFlashdata('success', 'The product has been deleted.');
        return redirect()->to(base_url('admin/catalog/products'));
    }

    public function massStatus()
    {
        $productIds = $this->request->getPost('product_ids') ?: [];
        $status = (int)$this->request->getPost('status');

        if (!empty($productIds)) {
            $db = \Config\Database::connect();
            $db->table('products')
               ->whereIn('id', $productIds)
               ->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
            
            session()->setFlashdata('success', 'A total of ' . count($productIds) . ' record(s) have been updated.');
        } else {
            session()->setFlashdata('error', 'Please select products to update.');
        }

        return redirect()->to(base_url('admin/catalog/products'));
    }
}
