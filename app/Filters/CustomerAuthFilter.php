<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class CustomerAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // If customer is not logged in, redirect to store login
        if (!session()->get('customer_logged_in')) {
            session()->setFlashdata('error', 'You must log in to access your customer dashboard.');
            return redirect()->to(base_url('customer/account/login'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
