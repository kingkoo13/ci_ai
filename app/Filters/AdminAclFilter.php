<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AdminAclFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $permissions = session()->get('admin_permissions') ?: [];

        // Full access
        if (in_array('*', $permissions)) {
            return;
        }

        // Parse path segments to check permissions
        // Format: admin/module/action
        $uri = $request->getUri()->getPath();
        $segments = explode('/', trim($uri, '/'));

        if (count($segments) >= 2 && $segments[0] === 'admin') {
            $module = $segments[1];
            
            // Allow access to dashboard and auth routes for everyone
            if (in_array($module, ['dashboard', 'login', 'logout'])) {
                return;
            }

            // Check if the module namespace is permitted
            if (!in_array($module, $permissions)) {
                session()->setFlashdata('error', 'Access Denied: You do not have permission to access the ' . esc(ucfirst($module)) . ' section.');
                return redirect()->to(base_url('admin/dashboard'));
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
