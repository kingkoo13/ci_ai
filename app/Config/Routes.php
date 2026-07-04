<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', function() {
    return redirect()->to(base_url('admin/dashboard'));
});

// Authentication routes
$routes->match(['get', 'post'], 'admin/login', 'Admin\Auth::login');
$routes->get('admin/logout', 'Admin\Auth::logout');

// Admin panel routes with Auth and ACL filters
$routes->group('admin', ['filter' => ['admin_auth', 'admin_acl']], function (RouteCollection $routes) {
    // Dashboard
    $routes->get('dashboard', 'Admin\Dashboard::index');

    // Catalog Products
    $routes->get('catalog/products', 'Admin\Products::index');
    $routes->match(['get', 'post'], 'catalog/products/new', 'Admin\Products::new');
    $routes->match(['get', 'post'], 'catalog/products/edit/(:num)', 'Admin\Products::edit/$1');
    $routes->post('catalog/products/delete/(:num)', 'Admin\Products::delete/$1');
    $routes->post('catalog/products/massStatus', 'Admin\Products::massStatus');

    // Catalog Categories
    $routes->get('catalog/categories', 'Admin\Categories::index');
    $routes->post('catalog/categories/save', 'Admin\Categories::save');
    $routes->post('catalog/categories/delete/(:num)', 'Admin\Categories::delete/$1');

    // Sales Orders
    $routes->get('sales/orders', 'Admin\Orders::index');
    $routes->get('sales/orders/view/(:num)', 'Admin\Orders::view/$1');
    $routes->post('sales/orders/invoice/(:num)', 'Admin\Orders::invoice/$1');
    $routes->post('sales/orders/ship/(:num)', 'Admin\Orders::ship/$1');
    $routes->post('sales/orders/cancel/(:num)', 'Admin\Orders::cancel/$1');

    // Sales Invoices & Shipments
    $routes->get('sales/invoices', 'Admin\Invoices::index');
    $routes->get('sales/invoices/view/(:num)', 'Admin\Invoices::view/$1');
    $routes->get('sales/shipments', 'Admin\Shipments::index');
    $routes->get('sales/shipments/view/(:num)', 'Admin\Shipments::view/$1');

    // Customers
    $routes->get('customers', 'Admin\Customers::index');
    $routes->match(['get', 'post'], 'customers/edit/(:num)', 'Admin\Customers::edit/$1');

    // Content CMS
    $routes->get('content/pages', 'Admin\Content::pages');
    $routes->match(['get', 'post'], 'content/pages/edit/(:num)', 'Admin\Content::editPage/$1');
    $routes->get('content/blocks', 'Admin\Content::blocks');
    $routes->match(['get', 'post'], 'content/blocks/edit/(:num)', 'Admin\Content::editBlock/$1');

    // Stores Configuration
    $routes->get('stores/configuration', 'Admin\Stores::configuration');
    $routes->post('stores/configuration/save', 'Admin\Stores::save');

    // System Cache, Roles & Users
    $routes->get('system/cache', 'Admin\System::cache');
    $routes->post('system/cache/flush', 'Admin\System::flush');
    
    // EAV Attributes & Sets CRUD
    $routes->get('stores/attributes', 'Admin\Attributes::index');
    $routes->match(['get', 'post'], 'stores/attributes/new', 'Admin\Attributes::new');
    $routes->match(['get', 'post'], 'stores/attributes/edit/(:num)', 'Admin\Attributes::edit/$1');
    $routes->post('stores/attributes/delete/(:num)', 'Admin\Attributes::delete/$1');
    
    $routes->get('stores/attributes/sets', 'Admin\Attributes::sets');
    $routes->match(['get', 'post'], 'stores/attributes/sets/new', 'Admin\Attributes::newSet');
    $routes->match(['get', 'post'], 'stores/attributes/sets/edit/(:num)', 'Admin\Attributes::editSet/$1');
    $routes->post('stores/attributes/sets/delete/(:num)', 'Admin\Attributes::deleteSet/$1');
    
    // AJAX Dynamic loaders
    $routes->get('catalog/products/getAttributes', 'Admin\Products::getAttributes');
    $routes->get('catalog/categories/getAttributes', 'Admin\Categories::getAttributes');
    $routes->get('customers/getAttributes', 'Admin\Customers::getAttributes');
    $routes->get('sales/orders/getAttributes', 'Admin\Orders::getAttributes');
    
    // System Roles CRUD
    $routes->get('system/roles', 'Admin\System::roles');
    $routes->match(['get', 'post'], 'system/roles/new', 'Admin\System::newRole');
    $routes->match(['get', 'post'], 'system/roles/edit/(:num)', 'Admin\System::editRole/$1');
    $routes->post('system/roles/delete/(:num)', 'Admin\System::deleteRole/$1');

    // System Admin Users CRUD
    $routes->get('system/users', 'Admin\System::users');
    $routes->match(['get', 'post'], 'system/users/new', 'Admin\System::newUser');
    $routes->match(['get', 'post'], 'system/users/edit/(:num)', 'Admin\System::editUser/$1');
    $routes->post('system/users/delete/(:num)', 'Admin\System::deleteUser/$1');
});
