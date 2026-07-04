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

        // Move subcategories up one level or set parent to null
        $category = $db->table('categories')->where('id', $id)->get()->getRow();
        if ($category) {
            $parentId = $category->parent_id ?: 1; // Fallback to Root
            $db->table('categories')->where('parent_id', $id)->update(['parent_id' => $parentId]);
            
            // Delete mappings
            $db->table('product_categories')->where('category_id', $id)->delete();
            // Delete category
            $db->table('categories')->where('id', $id)->delete();

            session()->setFlashdata('success', 'The category has been deleted.');
        } else {
            session()->setFlashdata('error', 'Category not found.');
        }

        return redirect()->to(base_url('admin/catalog/categories'));
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
