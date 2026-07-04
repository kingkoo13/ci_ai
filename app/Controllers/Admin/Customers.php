<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Customers extends BaseController
{
    public function index()
    {
        $db = \Config\Database::connect();
        
        $customers = $db->table('customers')
                        ->orderBy('id', 'DESC')
                        ->get()
                        ->getResult();

        $data = [
            'menu'      => 'customers',
            'submenu'   => 'all_customers',
            'customers' => $customers,
        ];

        return view('admin/customers/grid', $data);
    }

    public function edit($id)
    {
        $db = \Config\Database::connect();
        
        $customer = $db->table('customers')->where('id', $id)->get()->getRow();
        if (!$customer) {
            session()->setFlashdata('error', 'Customer not found.');
            return redirect()->to(base_url('admin/customers'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'email'      => "required|valid_email|is_unique[customers.email,id,{$id}]",
                'first_name' => 'required',
                'last_name'  => 'required',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $db->transStart();

            $customerData = [
                'email'      => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'group_id'   => $this->request->getPost('group_id') ?? 1,
                'is_active'  => $this->request->getPost('is_active') ?? 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('customers')->where('id', $id)->update($customerData);

            // Save Customer Custom Attributes
            $db->table('eav_attribute_values')
               ->where('entity_type', 'customer')
               ->where('entity_id', $id)
               ->delete();

            $customerAttrs = $this->request->getPost('attributes') ?: [];
            $customerAttrInsert = [];
            foreach ($customerAttrs as $attrId => $value) {
                if ($value !== null && $value !== '') {
                    $customerAttrInsert[] = [
                        'entity_type'  => 'customer',
                        'entity_id'    => $id,
                        'attribute_id' => $attrId,
                        'value'        => $value
                    ];
                }
            }
            if (!empty($customerAttrInsert)) {
                $db->table('eav_attribute_values')->insertBatch($customerAttrInsert);
            }

            // Save Addresses changes & custom EAV Address values
            $addressesPost = $this->request->getPost('address') ?: [];
            foreach ($addressesPost as $addrId => $addrData) {
                $addressUpdate = [
                    'street'    => $addrData['street'],
                    'city'      => $addrData['city'],
                    'region'    => $addrData['region'],
                    'postcode'  => $addrData['postcode'],
                    'country'   => $addrData['country'],
                    'telephone' => $addrData['telephone']
                ];
                $db->table('customer_addresses')->where('id', $addrId)->where('customer_id', $id)->update($addressUpdate);

                // Save custom address EAV value
                $db->table('eav_attribute_values')
                   ->where('entity_type', 'address')
                   ->where('entity_id', $addrId)
                   ->delete();

                $addrCustomAttrs = $this->request->getPost("address_attributes.{$addrId}") ?: [];
                $addrAttrInsert = [];
                foreach ($addrCustomAttrs as $attrId => $value) {
                    if ($value !== null && $value !== '') {
                        $addrAttrInsert[] = [
                            'entity_type'  => 'address',
                            'entity_id'    => $addrId,
                            'attribute_id' => $attrId,
                            'value'        => $value
                        ];
                    }
                }
                if (!empty($addrAttrInsert)) {
                    $db->table('eav_attribute_values')->insertBatch($addrAttrInsert);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Failed to update customer details.');
                return redirect()->back()->withInput();
            }

            session()->setFlashdata('success', 'Customer account details have been updated successfully.');
            return redirect()->to(base_url('admin/customers'));
        }

        // Fetch customer custom EAV attributes
        $customerAttributes = $db->table('eav_attributes')
                                 ->where('entity_type', 'customer')
                                 ->get()
                                 ->getResult();
        foreach ($customerAttributes as $attr) {
            if ($attr->input_type === 'select') {
                $attr->options = $db->table('eav_attribute_options')->where('attribute_id', $attr->id)->get()->getResult();
            }
        }

        // Fetch customer values
        $customerValuesList = $db->table('eav_attribute_values')
                                  ->where('entity_type', 'customer')
                                  ->where('entity_id', $id)
                                  ->get()
                                  ->getResult();
        $customerValues = [];
        foreach ($customerValuesList as $val) {
            $customerValues[$val->attribute_id] = $val->value;
        }

        // Fetch addresses & address EAV values
        $addresses = $db->table('customer_addresses')->where('customer_id', $id)->get()->getResult();
        
        $addressAttributes = $db->table('eav_attributes')
                                ->where('entity_type', 'address')
                                ->get()
                                ->getResult();
        foreach ($addressAttributes as $attr) {
            if ($attr->input_type === 'select') {
                $attr->options = $db->table('eav_attribute_options')->where('attribute_id', $attr->id)->get()->getResult();
            }
        }

        $addressValues = [];
        foreach ($addresses as $addr) {
            $valList = $db->table('eav_attribute_values')
                          ->where('entity_type', 'address')
                          ->where('entity_id', $addr->id)
                          ->get()
                          ->getResult();
            
            $addressValues[$addr->id] = [];
            foreach ($valList as $v) {
                $addressValues[$addr->id][$v->attribute_id] = $v->value;
            }
        }

        $data = [
            'menu'               => 'customers',
            'submenu'            => 'all_customers',
            'customer'           => $customer,
            'customerAttributes' => $customerAttributes,
            'customerValues'     => $customerValues,
            'addresses'          => $addresses,
            'addressAttributes'  => $addressAttributes,
            'addressValues'      => $addressValues
        ];

        return view('admin/customers/form', $data);
    }

    // AJAX endpoint for dynamic loading (supporting modular lookups if needed)
    public function getAttributes()
    {
        $db = \Config\Database::connect();
        $type = $this->request->getGet('type') ?: 'customer';
        $entityId = (int)$this->request->getGet('entity_id') ?: 0;

        $attributes = $db->table('eav_attributes')
                         ->where('entity_type', $type)
                         ->get()
                         ->getResult();

        if (empty($attributes)) {
            echo '<p style="color:var(--color-text-muted); font-style:italic;">No custom attributes defined.</p>';
            return;
        }

        $values = [];
        if ($entityId > 0) {
            $valObjects = $db->table('eav_attribute_values')
                             ->where('entity_type', $type)
                             ->where('entity_id', $entityId)
                             ->get()
                             ->getResult();
            foreach ($valObjects as $valObj) {
                $values[$valObj->attribute_id] = $valObj->value;
            }
        }

        $html = '';
        foreach ($attributes as $attr) {
            $val = isset($values[$attr->id]) ? esc($values[$attr->id]) : '';
            $required = $attr->is_required ? 'required' : '';
            $reqStar = $attr->is_required ? ' <span class="required">*</span>' : '';
            $inputName = $type === 'address' ? 'address_attributes[' . $entityId . '][' . $attr->id . ']' : 'attributes[' . $attr->id . ']';

            $html .= '<div class="form-group">';
            $html .= '<label>' . esc($attr->frontend_label) . $reqStar . '</label>';
            $html .= '<div class="form-control-wrapper">';

            if ($attr->input_type === 'text') {
                $html .= '<input type="text" name="' . $inputName . '" value="' . $val . '" class="form-control" ' . $required . '>';
            } elseif ($attr->input_type === 'textarea') {
                $html .= '<textarea name="' . $inputName . '" class="form-control" ' . $required . '>' . $val . '</textarea>';
            } elseif ($attr->input_type === 'boolean') {
                $selectedYes = ($val === '1') ? 'selected' : '';
                $selectedNo = ($val === '0') ? 'selected' : '';
                $html .= '<select name="' . $inputName . '" class="form-control" style="width: 150px;" ' . $required . '>';
                $html .= '<option value="0" ' . $selectedNo . '>No</option>';
                $html .= '<option value="1" ' . $selectedYes . '>Yes</option>';
                $html .= '</select>';
            } elseif ($attr->input_type === 'select') {
                $options = $db->table('eav_attribute_options')->where('attribute_id', $attr->id)->get()->getResult();
                $html .= '<select name="' . $inputName . '" class="form-control" style="width: 250px;" ' . $required . '>';
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
}
