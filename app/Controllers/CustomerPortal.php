<?php

namespace App\Controllers;

class CustomerPortal extends BaseController
{
    // Customer Login
    public function login()
    {
        if (session()->get('customer_logged_in')) {
            return redirect()->to(base_url('customer/account'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $db = \Config\Database::connect();
            $email = $this->request->getPost('email');

            $customer = $db->table('customers')->where('email', $email)->where('is_active', 1)->get()->getRow();
            if ($customer) {
                // Mock login: session credentials match directly
                session()->set([
                    'customer_logged_in' => true,
                    'customer_id'        => $customer->id,
                    'customer_email'     => $customer->email,
                    'customer_firstname' => $customer->first_name,
                    'customer_lastname'  => $customer->last_name
                ]);
                session()->setFlashdata('success', 'You have logged in successfully.');
                return redirect()->to(base_url('customer/account'));
            } else {
                session()->setFlashdata('error', 'Invalid email address or account is inactive.');
                return redirect()->back()->withInput();
            }
        }

        $data = [
            'pageLayout' => '1column',
            'pageTitle'  => 'Customer Login'
        ];
        return view('storefront/customer/login', $data);
    }

    // Customer Registration
    public function register()
    {
        if (session()->get('customer_logged_in')) {
            return redirect()->to(base_url('customer/account'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $db = \Config\Database::connect();
            
            $validation = \Config\Services::validation();
            $validation->setRules([
                'email'      => 'required|valid_email|is_unique[customers.email]',
                'first_name' => 'required',
                'last_name'  => 'required'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', 'Please correct the errors in the registration form.');
                return redirect()->back()->withInput();
            }

            // Insert new customer record
            $db->transStart();
            $custData = [
                'email'      => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'group_id'   => 1,
                'is_active'  => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $db->table('customers')->insert($custData);
            $customerId = $db->insertID();

            // Set a default empty address card to prevent null pointers
            $db->table('customer_addresses')->insert([
                'customer_id'         => $customerId,
                'street'              => 'Enter street name',
                'city'                => 'City',
                'region'              => 'Region',
                'postcode'            => '00000',
                'country'             => 'United States',
                'telephone'           => '000-000-0000',
                'is_default_billing'  => 1,
                'is_default_shipping' => 1
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Registration failed. Please try again.');
                return redirect()->back()->withInput();
            }

            // Automate login
            session()->set([
                'customer_logged_in' => true,
                'customer_id'        => $customerId,
                'customer_email'     => $custData['email'],
                'customer_firstname' => $custData['first_name'],
                'customer_lastname'  => $custData['last_name']
            ]);

            session()->setFlashdata('success', 'Account registered successfully. Welcome to LUMACI!');
            return redirect()->to(base_url('customer/account'));
        }

        $data = [
            'pageLayout' => '1column',
            'pageTitle'  => 'Create Customer Account'
        ];
        return view('storefront/customer/register', $data);
    }

    // Customer Logout
    public function logout()
    {
        session()->remove(['customer_logged_in', 'customer_id', 'customer_email', 'customer_firstname', 'customer_lastname']);
        session()->setFlashdata('success', 'You have signed out successfully.');
        return redirect()->to(base_url('/'));
    }

    // Customer Account Dashboard
    public function dashboard()
    {
        $db = \Config\Database::connect();
        $customerId = session()->get('customer_id');

        // Fetch customer details
        $customer = $db->table('customers')->where('id', $customerId)->get()->getRow();
        
        // Fetch orders
        $orders = $db->table('orders')
                     ->where('customer_id', $customerId)
                     ->orderBy('id', 'DESC')
                     ->get()
                     ->getResult();

        // Fetch address cards
        $addresses = $db->table('customer_addresses')
                        ->where('customer_id', $customerId)
                        ->get()
                        ->getResult();

        // Load custom address EAV values for displaying inside the address cards
        $addressValues = [];
        foreach ($addresses as $addr) {
            $vals = $db->table('eav_attribute_values')
                       ->select('eav_attributes.frontend_label, eav_attribute_values.value')
                       ->join('eav_attributes', 'eav_attributes.id = eav_attribute_values.attribute_id')
                       ->where('eav_attribute_values.entity_type', 'address')
                       ->where('eav_attribute_values.entity_id', $addr->id)
                       ->get()
                       ->getResult();
            
            $addressValues[$addr->id] = $vals;
        }

        $data = [
            'pageLayout'    => '2columns-left', // Sidebar menu
            'customer'      => $customer,
            'orders'        => $orders,
            'addresses'     => $addresses,
            'addressValues' => $addressValues
        ];

        return view('storefront/customer/dashboard', $data);
    }

    // Edit Customer Address Book details
    public function editAddress($id)
    {
        $db = \Config\Database::connect();
        $customerId = session()->get('customer_id');

        // Fetch address, verify owner bounds
        $address = $db->table('customer_addresses')
                      ->where('id', $id)
                      ->where('customer_id', $customerId)
                      ->get()
                      ->getRow();

        if (!$address) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Address card not found.");
        }

        // Fetch Address EAV custom Attributes (e.g. delivery instructions, gate code)
        $addressAttributes = $db->table('eav_attributes')
                               ->where('entity_type', 'address')
                               ->get()
                               ->getResult();

        // Fetch saved EAV values
        $savedVals = $db->table('eav_attribute_values')
                        ->where('entity_type', 'address')
                        ->where('entity_id', $id)
                        ->get()
                        ->getResult();

        $savedValuesMap = [];
        foreach ($savedVals as $sv) {
            $savedValuesMap[$sv->attribute_id] = $sv->value;
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $db->transStart();

            // 1. Update basic address record
            $db->table('customer_addresses')->where('id', $id)->update([
                'street'    => $this->request->getPost('street'),
                'city'      => $this->request->getPost('city'),
                'region'    => $this->request->getPost('region'),
                'postcode'  => $this->request->getPost('postcode'),
                'country'   => $this->request->getPost('country'),
                'telephone' => $this->request->getPost('telephone')
            ]);

            // 2. Save Custom Address EAV Attributes
            $customAttrInput = $this->request->getPost('attributes') ?: [];
            foreach ($addressAttributes as $attr) {
                $valInput = isset($customAttrInput[$attr->id]) ? $customAttrInput[$attr->id] : '';

                // Check if value already exists
                $exists = $db->table('eav_attribute_values')
                             ->where('entity_type', 'address')
                             ->where('entity_id', $id)
                             ->where('attribute_id', $attr->id)
                             ->get()
                             ->getRow();

                if ($exists) {
                    $db->table('eav_attribute_values')
                       ->where('id', $exists->id)
                       ->update(['value' => $valInput]);
                } else {
                    $db->table('eav_attribute_values')->insert([
                        'entity_type'  => 'address',
                        'entity_id'    => $id,
                        'attribute_id' => $attr->id,
                        'value'        => $valInput
                    ]);
                }
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                session()->setFlashdata('error', 'Failed to save address details.');
                return redirect()->back()->withInput();
            }

            session()->setFlashdata('success', 'Address details updated successfully.');
            return redirect()->to(base_url('customer/account'));
        }

        $data = [
            'pageLayout'        => '2columns-left',
            'address'           => $address,
            'addressAttributes' => $addressAttributes,
            'savedValuesMap'    => $savedValuesMap
        ];

        return view('storefront/customer/address_form', $data);
    }
}
