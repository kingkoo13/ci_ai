<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Categories extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $categories = $db->table('categories')->get()->getResult();

        // Selected Category details for the right panel form
        $selectedId = $this->request->getVar('id');
        $selectedCategory = null;
        if ($selectedId) {
            $selectedCategory = $db->table('categories')->where('id', $selectedId)->get()->getRow();
        }

        $data = [
            'menu'             => 'catalog',
            'submenu'          => 'categories',
            'categories'       => $categories,
            'selectedCategory' => $selectedCategory,
            'tree'             => $this->buildTree($categories),
        ];

        return view('admin/catalog/categories/tree', $data);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        
        $id = $this->request->getPost('id');
        $parent_id = $this->request->getPost('parent_id') ?: null;
        
        // Prevent setting a category as its own parent
        if ($id && $id == $parent_id) {
            session()->setFlashdata('error', 'A category cannot be its own parent.');
            return redirect()->back();
        }

        $db->transStart();

        $categoryData = [
            'name'        => $this->request->getPost('name'),
            'parent_id'   => $parent_id,
            'description' => $this->request->getPost('description'),
            'is_active'   => $this->request->getPost('is_active') ?? 1,
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        if ($id) {
            $db->table('categories')->where('id', $id)->update($categoryData);
            session()->setFlashdata('success', 'The category has been updated successfully.');
        } else {
            $categoryData['created_at'] = date('Y-m-d H:i:s');
            $db->table('categories')->insert($categoryData);
            $id = $db->insertID();
            session()->setFlashdata('success', 'The category has been created successfully.');
        }

        // Save EAV Category Attributes
        $db->table('eav_attribute_values')
           ->where('entity_type', 'category')
           ->where('entity_id', $id)
           ->delete();

        $customAttrs = $this->request->getPost('attributes') ?: [];
        $attrInsert = [];
        foreach ($customAttrs as $attrId => $value) {
            if ($value !== null && $value !== '') {
                $attrInsert[] = [
                    'entity_type'  => 'category',
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
            session()->setFlashdata('error', 'Failed to save category.');
            return redirect()->back()->withInput();
        }

        return redirect()->to(base_url('admin/catalog/categories?id=' . $id));
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();

        // Stop deleting root category
        if ($id == 1) {
            session()->setFlashdata('error', 'Cannot delete the Root Category.');
            return redirect()->to(base_url('admin/catalog/categories'));
        }

        $db->transStart();

        // Move subcategories up one level or set parent to null
        $category = $db->table('categories')->where('id', $id)->get()->getRow();
        if ($category) {
            $parentId = $category->parent_id ?: 1; // Fallback to Root
            $db->table('categories')->where('parent_id', $id)->update(['parent_id' => $parentId]);
            
            // Delete category values
            $db->table('eav_attribute_values')->where('entity_type', 'category')->where('entity_id', $id)->delete();
            // Delete mappings
            $db->table('product_categories')->where('category_id', $id)->delete();
            // Delete category
            $db->table('categories')->where('id', $id)->delete();

            session()->setFlashdata('success', 'The category has been deleted.');
        } else {
            session()->setFlashdata('error', 'Category not found.');
        }

        $db->transComplete();

        return redirect()->to(base_url('admin/catalog/categories'));
    }

    // AJAX category attributes retrieval helper
    public function getAttributes()
    {
        $db = \Config\Database::connect();
        $categoryId = (int)$this->request->getGet('category_id') ?: 0;

        // Fetch category attributes
        $attributes = $db->table('eav_attributes')
                         ->where('entity_type', 'category')
                         ->get()
                         ->getResult();

        if (empty($attributes)) {
            echo '<p style="color:var(--color-text-muted); font-style:italic;">No category custom attributes defined.</p>';
            return;
        }

        // Fetch existing category EAV values
        $values = [];
        if ($categoryId > 0) {
            $valObjects = $db->table('eav_attribute_values')
                             ->where('entity_type', 'category')
                             ->where('entity_id', $categoryId)
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

            $html .= '</div></div>';
        }

        echo $html;
    }

    /**
     * Build nested tree node arrays recursively.
     */
    private function buildTree(array $categories, $parentId = null)
    {
        $branch = [];

        foreach ($categories as $category) {
            // Compare parent_id
            $cParentId = $category->parent_id;
            if ($parentId === null) {
                // If checking root level (parent is null)
                $match = ($cParentId === null);
            } else {
                $match = ($cParentId == $parentId);
            }

            if ($match) {
                $children = $this->buildTree($categories, $category->id);
                $category->children = $children;
                $branch[] = $category;
            }
        }

        return $branch;
    }
}
