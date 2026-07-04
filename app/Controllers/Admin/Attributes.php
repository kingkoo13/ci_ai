<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Attributes extends BaseController
{
    // Attributes Grid
    public function index()
    {
        $db = \Config\Database::connect();
        
        $attributes = $db->table('eav_attributes')
                         ->select('eav_attributes.*, attribute_sets.name as set_name')
                         ->join('attribute_sets', 'attribute_sets.id = eav_attributes.attribute_set_id', 'left')
                         ->orderBy('eav_attributes.entity_type', 'ASC')
                         ->orderBy('eav_attributes.id', 'DESC')
                         ->get()
                         ->getResult();

        $data = [
            'menu'       => 'stores',
            'submenu'    => 'attributes',
            'attributes' => $attributes
        ];

        return view('admin/system/attributes_grid', $data);
    }

    // New Attribute
    public function new()
    {
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'entity_type'    => 'required',
                'attribute_code' => 'required|alpha_dash',
                'frontend_label' => 'required',
                'input_type'     => 'required',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $db->transStart();

            $attrData = [
                'entity_type'      => $this->request->getPost('entity_type'),
                'attribute_code'   => strtolower($this->request->getPost('attribute_code')),
                'frontend_label'   => $this->request->getPost('frontend_label'),
                'input_type'       => $this->request->getPost('input_type'),
                'attribute_set_id' => $this->request->getPost('entity_type') === 'product' ? ($this->request->getPost('attribute_set_id') ?: null) : null,
                'is_required'      => $this->request->getPost('is_required') ?? 0,
                'created_at'       => date('Y-m-d H:i:s')
            ];

            $db->table('eav_attributes')->insert($attrData);
            $attrId = $db->insertID();

            // Insert dropdown options if select type
            if ($attrData['input_type'] === 'select') {
                $options = $this->request->getPost('options') ?: [];
                $optionsData = [];
                foreach ($options as $optVal) {
                    if (trim($optVal) !== '') {
                        $optionsData[] = [
                            'attribute_id' => $attrId,
                            'option_value' => trim($optVal)
                        ];
                    }
                }
                if (!empty($optionsData)) {
                    $db->table('eav_attribute_options')->insertBatch($optionsData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Failed to create attribute.');
                return redirect()->back()->withInput();
            }

            session()->setFlashdata('success', 'The attribute has been created successfully.');
            return redirect()->to(base_url('admin/stores/attributes'));
        }

        $sets = $db->table('attribute_sets')->get()->getResult();

        $data = [
            'menu'       => 'stores',
            'submenu'    => 'attributes',
            'isEdit'     => false,
            'attribute'  => null,
            'sets'       => $sets,
            'options'    => []
        ];

        return view('admin/system/attribute_form', $data);
    }

    // Edit Attribute
    public function edit($id)
    {
        $db = \Config\Database::connect();
        
        $attribute = $db->table('eav_attributes')->where('id', $id)->get()->getRow();
        if (!$attribute) {
            session()->setFlashdata('error', 'Attribute not found.');
            return redirect()->to(base_url('admin/stores/attributes'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'frontend_label' => 'required',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $db->transStart();

            $attrData = [
                'frontend_label'   => $this->request->getPost('frontend_label'),
                'attribute_set_id' => $attribute->entity_type === 'product' ? ($this->request->getPost('attribute_set_id') ?: null) : null,
                'is_required'      => $this->request->getPost('is_required') ?? 0,
            ];

            $db->table('eav_attributes')->where('id', $id)->update($attrData);

            // Re-sync options for select type dropdown
            if ($attribute->input_type === 'select') {
                $db->table('eav_attribute_options')->where('attribute_id', $id)->delete();
                
                $options = $this->request->getPost('options') ?: [];
                $optionsData = [];
                foreach ($options as $optVal) {
                    if (trim($optVal) !== '') {
                        $optionsData[] = [
                            'attribute_id' => $id,
                            'option_value' => trim($optVal)
                        ];
                    }
                }
                if (!empty($optionsData)) {
                    $db->table('eav_attribute_options')->insertBatch($optionsData);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Failed to update attribute.');
                return redirect()->back();
            }

            session()->setFlashdata('success', 'The attribute has been saved successfully.');
            return redirect()->to(base_url('admin/stores/attributes'));
        }

        $sets = $db->table('attribute_sets')->get()->getResult();
        $options = $db->table('eav_attribute_options')->where('attribute_id', $id)->get()->getResult();

        $data = [
            'menu'       => 'stores',
            'submenu'    => 'attributes',
            'isEdit'     => true,
            'attribute'  => $attribute,
            'sets'       => $sets,
            'options'    => $options
        ];

        return view('admin/system/attribute_form', $data);
    }

    // Delete Attribute
    public function delete($id)
    {
        $db = \Config\Database::connect();
        
        $db->transStart();
        $db->table('eav_attribute_values')->where('attribute_id', $id)->delete();
        $db->table('eav_attribute_options')->where('attribute_id', $id)->delete();
        $db->table('eav_attributes')->where('id', $id)->delete();
        $db->transComplete();

        session()->setFlashdata('success', 'The attribute has been deleted successfully.');
        return redirect()->to(base_url('admin/stores/attributes'));
    }

    // Attribute Sets list
    public function sets()
    {
        $db = \Config\Database::connect();
        $sets = $db->table('attribute_sets')->get()->getResult();

        $data = [
            'menu'    => 'stores',
            'submenu' => 'configuration', // Link under configuration grouping or distinct
            'sets'    => $sets
        ];

        return view('admin/system/sets_grid', $data);
    }

    // Add Attribute Set
    public function newSet()
    {
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => 'required|is_unique[attribute_sets.name]'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $db->table('attribute_sets')->insert([
                'name'       => $this->request->getPost('name'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            session()->setFlashdata('success', 'The attribute set has been created successfully.');
            return redirect()->to(base_url('admin/stores/attributes/sets'));
        }

        $data = [
            'menu'    => 'stores',
            'submenu' => 'configuration',
            'isEdit'  => false,
            'set'     => null
        ];

        return view('admin/system/set_form', $data);
    }

    // Edit Attribute Set
    public function editSet($id)
    {
        $db = \Config\Database::connect();
        $set = $db->table('attribute_sets')->where('id', $id)->get()->getRow();
        
        if (!$set) {
            session()->setFlashdata('error', 'Attribute set not found.');
            return redirect()->to(base_url('admin/stores/attributes/sets'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => "required|is_unique[attribute_sets.name,id,{$id}]"
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $db->table('attribute_sets')->where('id', $id)->update([
                'name' => $this->request->getPost('name')
            ]);

            session()->setFlashdata('success', 'The attribute set has been updated successfully.');
            return redirect()->to(base_url('admin/stores/attributes/sets'));
        }

        $data = [
            'menu'    => 'stores',
            'submenu' => 'configuration',
            'isEdit'  => true,
            'set'     => $set
        ];

        return view('admin/system/set_form', $data);
    }

    // Delete Attribute Set
    public function deleteSet($id)
    {
        $db = \Config\Database::connect();

        if ($id == 1) {
            session()->setFlashdata('error', 'The Default attribute set cannot be deleted.');
            return redirect()->to(base_url('admin/stores/attributes/sets'));
        }

        $db->transStart();
        // Nullify reference in products
        $db->table('products')->where('attribute_set_id', $id)->update(['attribute_set_id' => 1]);
        // Set NULL in attributes
        $db->table('eav_attributes')->where('attribute_set_id', $id)->update(['attribute_set_id' => null]);
        // Delete set
        $db->table('attribute_sets')->where('id', $id)->delete();
        $db->transComplete();

        session()->setFlashdata('success', 'The attribute set has been deleted successfully.');
        return redirect()->to(base_url('admin/stores/attributes/sets'));
    }
}
