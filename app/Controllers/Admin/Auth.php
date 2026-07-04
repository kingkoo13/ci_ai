<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function login()
    {
        // If already logged in, redirect to dashboard
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            $db = \Config\Database::connect();
            $user = $db->table('admin_users')
                       ->select('admin_users.*, admin_roles.name as role_name, admin_roles.permissions')
                       ->join('admin_roles', 'admin_roles.id = admin_users.role_id')
                       ->where('username', $username)
                       ->where('is_active', 1)
                       ->get()
                       ->getRow();

            if ($user && password_verify($password, $user->password)) {
                $sessionData = [
                    'admin_id'         => $user->id,
                    'admin_username'   => $user->username,
                    'admin_email'      => $user->email,
                    'admin_firstname'  => $user->first_name,
                    'admin_lastname'   => $user->last_name,
                    'admin_role_id'    => $user->role_id,
                    'admin_role_name'  => $user->role_name,
                    'admin_permissions'=> json_decode($user->permissions, true) ?: [],
                    'admin_logged_in'  => true,
                ];
                session()->set($sessionData);
                session()->setFlashdata('success', 'Welcome back, ' . esc($user->first_name) . '!');
                return redirect()->to(base_url('admin/dashboard'));
            } else {
                session()->setFlashdata('error', 'Invalid username or password.');
                return redirect()->to(base_url('admin/login'));
            }
        }

        return view('admin/login');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('admin/login'));
    }
}
