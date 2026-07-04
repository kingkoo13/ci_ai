<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Products extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Paging & filtering variables
        $page = (int)$this->request->getGet('page') ?: 1;
        $perPage = 10;
        $offset = ($page - 1) * $perPage;

        $search = $this->request->getGet('search') ?: '';
        $filter_status = $this->request->getGet('filter_status');
        $filter_price_min = $this->request->getGet('filter_price_min');
        $filter_price_max = $this->request->getGet('filter_price_max');
        $filter_qty_min = $this->request->getGet('filter_qty_min');
        $filter_qty_max = $this->request->getGet('filter_qty_max');

        $builder = $db->table('products')
                      ->select('products.*, attribute_sets.name as set_name')
                      ->join('attribute_sets', 'attribute_sets.id = products.attribute_set_id', 'left');

        if (!empty($search)) {
            $builder->groupStart()
                    ->like('products.name', $search)
                    ->orLike('products.sku', $search)
                    ->groupEnd();
        }

        if ($filter_status !== null && $filter_status !== '') {
            $builder->where('products.status', (int)$filter_status);
        }

        if ($filter_price_min !== null && $filter_price_min !== '') {
            $builder->where('products.price >=', (float)$filter_price_min);
        }
        if ($filter_price_max !== null && $filter_price_max !== '') {
            $builder->where('products.price <=', (float)$filter_price_max);
        }

        if ($filter_qty_min !== null && $filter_qty_min !== '') {
            $builder->where('products.qty >=', (float)$filter_qty_min);
        }
        if ($filter_qty_max !== null && $filter_qty_max !== '') {
            $builder->where('products.qty <=', (float)$filter_qty_max);
        }

        // Count total matching records
        $totalBuilder = clone $builder;
        $totalRows = $totalBuilder->countAllResults(false);

        $products = $builder->orderBy('products.id', 'DESC')
                            ->limit($perPage, $offset)
                            ->get()
                            ->getResult();

        $totalPages = ceil($totalRows / $perPage);

        // Fetch category names for displaying in details inline
        foreach ($products as $product) {
            $catObjects = $db->table('product_categories')
                             ->select('categories.name')
                             ->join('categories', 'categories.id = product_categories.category_id')
                             ->where('product_categories.product_id', $product->id)
                             ->get()
                             ->getResult();
            
            $catNames = array_map(function($c) {
                return $c->name;
            }, $catObjects);

            $product->category_names = implode(', ', $catNames);
        }

        $data = [
            'menu'             => 'catalog',
            'submenu'          => 'products',
            'products'         => $products,
            'search'           => $search,
            'filter_status'    => $filter_status,
            'filter_price_min' => $filter_price_min,
            'filter_price_max' => $filter_price_max,
            'filter_qty_min'   => $filter_qty_min,
            'filter_qty_max'   => $filter_qty_max,
            'page'             => $page,
            'totalPages'       => $totalPages,
            'totalCount'       => $totalRows
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

            $db->transStart();

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
                'attribute_set_id'  => $this->request->getPost('attribute_set_id') ?: 1, // Default to Default set
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

            // Save custom EAV attributes
            $customAttrs = $this->request->getPost('attributes') ?: [];
            $attrInsert = [];
            foreach ($customAttrs as $attrId => $value) {
                if ($value !== null && $value !== '') {
                    $attrInsert[] = [
                        'entity_type'  => 'product',
                        'entity_id'    => $productId,
                        'attribute_id' => $attrId,
                        'value'        => $value
                    ];
                }
            }
            if (!empty($attrInsert)) {
                $db->table('eav_attribute_values')->insertBatch($attrInsert);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Failed to save product.');
                return redirect()->back()->withInput();
            }

            session()->setFlashdata('success', 'The product has been saved successfully.');
            return redirect()->to(base_url('admin/catalog/products'));
        }

        // Fetch category list
        $categories = $db->table('categories')
                         ->where('id >', 1)
                         ->get()
                         ->getResult();

        $sets = $db->table('attribute_sets')->get()->getResult();

        $data = [
            'menu'       => 'catalog',
            'submenu'    => 'products',
            'categories' => $categories,
            'sets'       => $sets,
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

            $db->transStart();

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
                'attribute_set_id'  => $this->request->getPost('attribute_set_id') ?: 1,
                'updated_at'        => date('Y-m-d H:i:s'),
            ];

            $db->table('products')->where('id', $id)->update($productData);

            // Re-map categories
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

            // Save custom EAV attributes
            $db->table('eav_attribute_values')
               ->where('entity_type', 'product')
               ->where('entity_id', $id)
               ->delete();

            $customAttrs = $this->request->getPost('attributes') ?: [];
            $attrInsert = [];
            foreach ($customAttrs as $attrId => $value) {
                if ($value !== null && $value !== '') {
                    $attrInsert[] = [
                        'entity_type'  => 'product',
                        'entity_id'    => $id,
                        'attribute_id' => $attrId,
                        'value'        => $value
                    ];
                }
            }
            if (!empty($attrInsert)) {
                $db->table('eav_attribute_values')->insertBatch($attrInsert);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Failed to update product.');
                return redirect()->back();
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

        $sets = $db->table('attribute_sets')->get()->getResult();

        $data = [
            'menu'       => 'catalog',
            'submenu'    => 'products',
            'categories' => $categories,
            'sets'       => $sets,
            'isEdit'     => true,
            'product'    => $product,
            'mappedCats' => $mappedCats
        ];

        return view('admin/catalog/products/form', $data);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        
        $db->transStart();
        $db->table('eav_attribute_values')->where('entity_type', 'product')->where('entity_id', $id)->delete();
        $db->table('product_categories')->where('product_id', $id)->delete();
        $db->table('products')->where('id', $id)->delete();
        $db->transComplete();

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

    // AJAX dynamic loading of product attributes
    public function getAttributes()
    {
        $db = \Config\Database::connect();
        $setId = (int)$this->request->getGet('set_id');
        $productId = (int)$this->request->getGet('product_id') ?: 0;

        // Fetch attributes of this set or global attributes
        $attributes = $db->table('eav_attributes')
                         ->where('entity_type', 'product')
                         ->groupStart()
                            ->where('attribute_set_id', $setId)
                            ->orWhere('attribute_set_id', null)
                         ->groupEnd()
                         ->get()
                         ->getResult();

        if (empty($attributes)) {
            echo '<p style="color:var(--color-text-muted); font-style:italic;">No custom attributes found for this set.</p>';
            return;
        }

        // Fetch existing values for this product
        $values = [];
        if ($productId > 0) {
            $valObjects = $db->table('eav_attribute_values')
                             ->where('entity_type', 'product')
                             ->where('entity_id', $productId)
                             ->get()
                             ->getResult();
            foreach ($valObjects as $valObj) {
                $values[$valObj->attribute_id] = $valObj->value;
            }
        }

        // Build HTML forms dynamically
        $html = '';
        foreach ($attributes as $attr) {
            $val = isset($values[$attr->id]) ? esc($values[$attr->id]) : '';
            $required = $attr->is_required ? 'required' : '';
            $reqStar = $attr->is_required ? ' <span class="required">*</span>' : '';

            $html .= '<div class="form-group">';
            $html .= '<label for="attr-' . $attr->id . '">' . esc($attr->frontend_label) . $reqStar . '</label>';
            $html .= '<div class="form-control-wrapper">';

            if ($attr->input_type === 'text') {
                $html .= '<input type="text" id="attr-' . $attr->id . '" name="attributes[' . $attr->id . ']" value="' . $val . '" class="form-control" ' . $required . '>';
            } elseif ($attr->input_type === 'textarea') {
                $html .= '<textarea id="attr-' . $attr->id . '" name="attributes[' . $attr->id . ']" class="form-control" ' . $required . '>' . $val . '</textarea>';
            } elseif ($attr->input_type === 'boolean') {
                $selectedYes = ($val === '1') ? 'selected' : '';
                $selectedNo = ($val === '0') ? 'selected' : '';
                $html .= '<select id="attr-' . $attr->id . '" name="attributes[' . $attr->id . ']" class="form-control" style="width: 150px;" ' . $required . '>';
                $html .= '<option value="0" ' . $selectedNo . '>No</option>';
                $html .= '<option value="1" ' . $selectedYes . '>Yes</option>';
                $html .= '</select>';
            } elseif ($attr->input_type === 'select') {
                $options = $db->table('eav_attribute_options')->where('attribute_id', $attr->id)->get()->getResult();
                $html .= '<select id="attr-' . $attr->id . '" name="attributes[' . $attr->id . ']" class="form-control" style="width: 250px;" ' . $required . '>';
                $html .= '<option value="">-- Select Option --</option>';
                foreach ($options as $opt) {
                    $selected = ($val === $opt->option_value) ? 'selected' : '';
                    $html .= '<option value="' . esc($opt->option_value) . '" ' . $selected . '>' . esc($opt->option_value) . '</option>';
                }
                $html .= '</select>';
            }

            $html .= '<span class="form-note">Code: <code>' . esc($attr->attribute_code) . '</code></span>';
            $html .= '</div></div>';
        }

        echo $html;
    }
}
