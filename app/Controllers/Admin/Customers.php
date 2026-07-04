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

            $customerData = [
                'email'      => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'group_id'   => $this->request->getPost('group_id') ?? 1,
                'is_active'  => $this->request->getPost('is_active') ?? 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('customers')->where('id', $id)->update($customerData);

            session()->setFlashdata('success', 'Customer accounts have been updated successfully.');
            return redirect()->to(base_url('admin/customers'));
        }

        $data = [
            'menu'     => 'customers',
            'submenu'  => 'all_customers',
            'customer' => $customer,
        ];

        return view('admin/customers/form', $data);
    }
}
