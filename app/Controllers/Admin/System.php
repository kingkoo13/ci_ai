<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class System extends BaseController
{
    // Cache management view
    public function cache()
    {
        $data = [
            'menu'    => 'system',
            'submenu' => 'cache',
        ];

        return view('admin/system/cache', $data);
    }

    // Flush cache action
    public function flush()
    {
        // Flush CodeIgniter cache
        $cache = \Config\Services::cache();
        $cache->clean();

        // Also clean the writable/cache files manually if needed
        $cachePath = WRITEPATH . 'cache/';
        if (is_dir($cachePath)) {
            $files = glob($cachePath . '*');
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== '.htaccess' && basename($file) !== 'index.html') {
                    unlink($file);
                }
            }
        }

        session()->setFlashdata('success', 'The cache storage has been flushed successfully.');
        return redirect()->to(base_url('admin/system/cache'));
    }

    // User roles list grid
    public function roles()
    {
        $db = \Config\Database::connect();
        
        $roles = $db->table('admin_roles')->get()->getResult();

        $data = [
            'menu'    => 'system',
            'submenu' => 'roles',
            'roles'   => $roles
        ];

        return view('admin/system/roles_grid', $data);
    }

    // Add new Role & ACL permissions
    public function newRole()
    {
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => 'required|is_unique[admin_roles.name]'
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            // Parse selected permissions checkmarks
            $perms = $this->request->getPost('permissions') ?: [];
            if ($this->request->getPost('full_access')) {
                $perms = ['*'];
            }

            $roleData = [
                'name'        => $this->request->getPost('name'),
                'permissions' => json_encode($perms),
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            $db->table('admin_roles')->insert($roleData);

            session()->setFlashdata('success', 'The role has been created successfully.');
            return redirect()->to(base_url('admin/system/roles'));
        }

        $data = [
            'menu'        => 'system',
            'submenu'     => 'roles',
            'isEdit'      => false,
            'role'        => null,
            'permissions' => []
        ];

        return view('admin/system/role_form', $data);
    }

    // Edit administrative Role details & ACL permissions
    public function editRole($id)
    {
        $db = \Config\Database::connect();
        
        $role = $db->table('admin_roles')->where('id', $id)->get()->getRow();
        if (!$role) {
            session()->setFlashdata('error', 'Role not found.');
            return redirect()->to(base_url('admin/system/roles'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            $validation->setRules([
                'name' => "required|is_unique[admin_roles.name,id,{$id}]"
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            // Parse selected permissions checkmarks
            $perms = $this->request->getPost('permissions') ?: [];
            if ($this->request->getPost('full_access')) {
                $perms = ['*'];
            }

            $roleData = [
                'name'        => $this->request->getPost('name'),
                'permissions' => json_encode($perms),
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            $db->table('admin_roles')->where('id', $id)->update($roleData);

            // If the current admin's role was updated, update their session permissions immediately
            if (session()->get('admin_role_id') == $id) {
                session()->set('admin_permissions', $perms);
            }

            session()->setFlashdata('success', 'Role permissions have been saved successfully.');
            return redirect()->to(base_url('admin/system/roles'));
        }

        $data = [
            'menu'        => 'system',
            'submenu'     => 'roles',
            'isEdit'      => true,
            'role'        => $role,
            'permissions' => json_decode($role->permissions, true) ?: []
        ];

        return view('admin/system/role_form', $data);
    }

    // Delete administrative Role
    public function deleteRole($id)
    {
        $db = \Config\Database::connect();

        if ($id == 1) {
            session()->setFlashdata('error', 'The primary Administrators role cannot be deleted.');
            return redirect()->to(base_url('admin/system/roles'));
        }

        // Check if role is assigned to any admin user
        $userCount = $db->table('admin_users')->where('role_id', $id)->countAllResults();
        if ($userCount > 0) {
            session()->setFlashdata('error', 'Cannot delete role because it is assigned to ' . $userCount . ' admin user(s).');
            return redirect()->to(base_url('admin/system/roles'));
        }

        $db->table('admin_roles')->where('id', $id)->delete();
        session()->setFlashdata('success', 'The role has been deleted successfully.');
        return redirect()->to(base_url('admin/system/roles'));
    }

    // Admin users list grid
    public function users()
    {
        $db = \Config\Database::connect();
        
        $users = $db->table('admin_users')
                    ->select('admin_users.*, admin_roles.name as role_name')
                    ->join('admin_roles', 'admin_roles.id = admin_users.role_id')
                    ->get()
                    ->getResult();

        $data = [
            'menu'    => 'system',
            'submenu' => 'users',
            'users'   => $users
        ];

        return view('admin/system/users_grid', $data);
    }

    // Add new administrative user
    public function newUser()
    {
        $db = \Config\Database::connect();

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'username'   => 'required|min_length[3]|is_unique[admin_users.username]',
                'email'      => 'required|valid_email|is_unique[admin_users.email]',
                'password'   => 'required|min_length[6]',
                'first_name' => 'required',
                'last_name'  => 'required',
                'role_id'    => 'required|is_natural_no_zero',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $userData = [
                'username'   => $this->request->getPost('username'),
                'email'      => $this->request->getPost('email'),
                'password'   => password_hash($this->request->getPost('password'), PASSWORD_BCRYPT),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'role_id'    => $this->request->getPost('role_id'),
                'is_active'  => $this->request->getPost('is_active') ?? 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('admin_users')->insert($userData);

            session()->setFlashdata('success', 'The user has been created successfully.');
            return redirect()->to(base_url('admin/system/users'));
        }

        $roles = $db->table('admin_roles')->get()->getResult();

        $data = [
            'menu'    => 'system',
            'submenu' => 'users',
            'isEdit'  => false,
            'user'    => null,
            'roles'   => $roles
        ];

        return view('admin/system/user_form', $data);
    }

    // Edit administrative user details
    public function editUser($id)
    {
        $db = \Config\Database::connect();
        
        $user = $db->table('admin_users')->where('id', $id)->get()->getRow();
        if (!$user) {
            session()->setFlashdata('error', 'User not found.');
            return redirect()->to(base_url('admin/system/users'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'username'   => "required|min_length[3]|is_unique[admin_users.username,id,{$id}]",
                'email'      => "required|valid_email|is_unique[admin_users.email,id,{$id}]",
                'first_name' => 'required',
                'last_name'  => 'required',
                'role_id'    => 'required|is_natural_no_zero',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $userData = [
                'username'   => $this->request->getPost('username'),
                'email'      => $this->request->getPost('email'),
                'first_name' => $this->request->getPost('first_name'),
                'last_name'  => $this->request->getPost('last_name'),
                'role_id'    => $this->request->getPost('role_id'),
                'is_active'  => $this->request->getPost('is_active') ?? 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            // Only update password if a new one is typed
            $newPassword = $this->request->getPost('password');
            if (!empty($newPassword)) {
                $userData['password'] = password_hash($newPassword, PASSWORD_BCRYPT);
            }

            $db->table('admin_users')->where('id', $id)->update($userData);

            session()->setFlashdata('success', 'The user has been updated successfully.');
            return redirect()->to(base_url('admin/system/users'));
        }

        $roles = $db->table('admin_roles')->get()->getResult();

        $data = [
            'menu'    => 'system',
            'submenu' => 'users',
            'isEdit'  => true,
            'user'    => $user,
            'roles'   => $roles
        ];

        return view('admin/system/user_form', $data);
    }

    // Delete administrative user
    public function deleteUser($id)
    {
        $db = \Config\Database::connect();

        // Safety controls
        if ($id == 1) {
            session()->setFlashdata('error', 'The primary administrator account cannot be deleted.');
            return redirect()->to(base_url('admin/system/users'));
        }

        if ($id == session()->get('admin_id')) {
            session()->setFlashdata('error', 'You cannot delete your own logged-in account.');
            return redirect()->to(base_url('admin/system/users'));
        }

        $db->table('admin_users')->where('id', $id)->delete();
        
        session()->setFlashdata('success', 'The admin user has been deleted successfully.');
        return redirect()->to(base_url('admin/system/users'));
    }
}
