<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminPanelSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin Roles
        $roleData = [
            'name'        => 'Administrators',
            'permissions' => json_encode(['*']), // Grant all permissions
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s')
        ];
        $this->db->table('admin_roles')->insert($roleData);
        $roleId = $this->db->insertID();

        // 2. Admin Users
        $userData = [
            'username'   => 'admin',
            'password'   => password_hash('admin123', PASSWORD_BCRYPT),
            'email'      => 'admin@example.com',
            'first_name' => 'John',
            'last_name'  => 'Admin',
            'role_id'    => $roleId,
            'is_active'  => 1,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->table('admin_users')->insert($userData);

        // 3. Core Config Data
        $configData = [
            ['path' => 'general/store_information/name', 'value' => 'Magento 2 CI Edition'],
            ['path' => 'general/store_information/phone', 'value' => '+1 (555) 123-4567'],
            ['path' => 'trans_email/ident_general/email', 'value' => 'store@example.com'],
            ['path' => 'trans_email/ident_sales/email', 'value' => 'sales@example.com'],
            ['path' => 'sales/shipping/flat_rate_active', 'value' => '1'],
            ['path' => 'sales/shipping/flat_rate_price', 'value' => '10.00'],
            ['path' => 'catalog/frontend/grid_per_page', 'value' => '20'],
        ];
        $this->db->table('core_config_data')->insertBatch($configData);

        // 4. Categories
        $categories = [
            ['id' => 1, 'name' => 'Default Category', 'parent_id' => null, 'description' => 'Root Category', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'name' => 'Men', 'parent_id' => 1, 'description' => 'Men Apparel & Shoes', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 3, 'name' => 'Women', 'parent_id' => 1, 'description' => 'Women Apparel & Shoes', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 4, 'name' => 'Accessories', 'parent_id' => 1, 'description' => 'Watches, Bags & Wallets', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 5, 'name' => 'Tops', 'parent_id' => 2, 'description' => 'Men Shirts & Jackets', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 6, 'name' => 'Footwear', 'parent_id' => 2, 'description' => 'Men Shoes', 'is_active' => 1, 'created_at' => date('Y-m-d H:i:s')]
        ];
        $this->db->table('categories')->insertBatch($categories);

        // 5. Products
        $products = [
            [
                'id' => 1,
                'sku' => 'shoes-01',
                'name' => 'Sleek Athletic Running Shoes',
                'description' => 'Ultra-breathable sports running shoes designed for maximum comfort and speed.',
                'short_description' => 'Lightweight running shoes.',
                'price' => 89.99,
                'special_price' => 79.99,
                'qty' => 45,
                'is_in_stock' => 1,
                'status' => 1,
                'image_url' => 'assets/images/shoes-01.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'sku' => 'jacket-02',
                'name' => 'Premium Vintage Leather Jacket',
                'description' => 'Handcrafted 100% genuine leather jacket featuring multiple zippered pockets and robust padding.',
                'short_description' => 'Classic leather jacket.',
                'price' => 249.99,
                'special_price' => null,
                'qty' => 12,
                'is_in_stock' => 1,
                'status' => 1,
                'image_url' => 'assets/images/jacket-02.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'sku' => 'shirt-03',
                'name' => 'Slim Fit Cotton Oxford Shirt',
                'description' => 'Tailored from premium long-staple cotton for a comfortable, clean-cut professional appearance.',
                'short_description' => 'White dress shirt.',
                'price' => 49.99,
                'special_price' => 39.99,
                'qty' => 80,
                'is_in_stock' => 1,
                'status' => 1,
                'image_url' => 'assets/images/shirt-03.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 4,
                'sku' => 'watch-04',
                'name' => 'Chronograph Stainless Steel Watch',
                'description' => 'Waterproof quartz watch with mineral glass, full stopwatch functions, and solid calendar window.',
                'short_description' => 'Luxury metal watch.',
                'price' => 199.99,
                'special_price' => null,
                'qty' => 20,
                'is_in_stock' => 1,
                'status' => 1,
                'image_url' => 'assets/images/watch-04.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 5,
                'sku' => 'backpack-05',
                'name' => 'Waterproof Travel Laptop Backpack',
                'description' => 'High-capacity smart backpack with USB charging port, anti-theft zipper compartment, and heavy duty cushions.',
                'short_description' => 'Functional laptop backpack.',
                'price' => 59.99,
                'special_price' => 49.99,
                'qty' => 35,
                'is_in_stock' => 1,
                'status' => 1,
                'image_url' => 'assets/images/backpack-05.jpg',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('products')->insertBatch($products);

        // Product Category maps
        $productCategories = [
            ['product_id' => 1, 'category_id' => 2],
            ['product_id' => 1, 'category_id' => 6],
            ['product_id' => 2, 'category_id' => 2],
            ['product_id' => 2, 'category_id' => 5],
            ['product_id' => 3, 'category_id' => 2],
            ['product_id' => 3, 'category_id' => 5],
            ['product_id' => 4, 'category_id' => 4],
            ['product_id' => 5, 'category_id' => 4],
        ];
        $this->db->table('product_categories')->insertBatch($productCategories);

        // 6. Customers
        $customers = [
            [
                'id' => 1,
                'email' => 'jane.doe@example.com',
                'first_name' => 'Jane',
                'last_name' => 'Doe',
                'group_id' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-10 days')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 2,
                'email' => 'robert.smith@example.com',
                'first_name' => 'Robert',
                'last_name' => 'Smith',
                'group_id' => 2, // Wholesale
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days')),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'id' => 3,
                'email' => 'alice.johnson@example.com',
                'first_name' => 'Alice',
                'last_name' => 'Johnson',
                'group_id' => 1,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('customers')->insertBatch($customers);

        // Customer Addresses
        $addresses = [
            [
                'customer_id' => 1,
                'street' => '100 Broadway Ave',
                'city' => 'New York',
                'region' => 'New York',
                'postcode' => '10005',
                'country' => 'United States',
                'telephone' => '123-456-7890',
                'is_default_billing' => 1,
                'is_default_shipping' => 1
            ],
            [
                'customer_id' => 2,
                'street' => '456 Market St',
                'city' => 'San Francisco',
                'region' => 'California',
                'postcode' => '94103',
                'country' => 'United States',
                'telephone' => '415-987-6543',
                'is_default_billing' => 1,
                'is_default_shipping' => 1
            ]
        ];
        $this->db->table('customer_addresses')->insertBatch($addresses);

        // 7. Orders & Items
        // Order 1: Complete order from 4 days ago
        $this->db->table('orders')->insert([
            'id' => 1,
            'increment_id' => '100000001',
            'customer_id' => 1,
            'customer_email' => 'jane.doe@example.com',
            'customer_firstname' => 'Jane',
            'customer_lastname' => 'Doe',
            'status' => 'complete',
            'subtotal' => 129.98,
            'grand_total' => 139.98,
            'shipping_amount' => 10.00,
            'shipping_description' => 'Flat Rate - Fixed',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days')),
            'updated_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ]);
        
        $this->db->table('order_items')->insertBatch([
            [
                'order_id' => 1,
                'product_id' => 1,
                'sku' => 'shoes-01',
                'name' => 'Sleek Athletic Running Shoes',
                'price' => 79.99,
                'qty_ordered' => 1,
                'qty_invoiced' => 1,
                'qty_shipped' => 1,
                'row_total' => 79.99
            ],
            [
                'order_id' => 1,
                'product_id' => 3,
                'sku' => 'shirt-03',
                'name' => 'Slim Fit Cotton Oxford Shirt',
                'price' => 49.99,
                'qty_ordered' => 1,
                'qty_invoiced' => 1,
                'qty_shipped' => 1,
                'row_total' => 49.99
            ]
        ]);

        // Invoice and Shipment for Order 1
        $this->db->table('invoices')->insert([
            'increment_id' => '300000001',
            'order_id' => 1,
            'grand_total' => 139.98,
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ]);
        $this->db->table('shipments')->insert([
            'increment_id' => '400000001',
            'order_id' => 1,
            'tracks' => json_encode([['carrier' => 'FedEx', 'number' => '123456789012']]),
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ]);

        // Order 2: Processing order from 2 days ago
        $this->db->table('orders')->insert([
            'id' => 2,
            'increment_id' => '100000002',
            'customer_id' => 2,
            'customer_email' => 'robert.smith@example.com',
            'customer_firstname' => 'Robert',
            'customer_lastname' => 'Smith',
            'status' => 'processing',
            'subtotal' => 249.99,
            'grand_total' => 259.99,
            'shipping_amount' => 10.00,
            'shipping_description' => 'Flat Rate - Fixed',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days')),
            'updated_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ]);
        $this->db->table('order_items')->insert([
            'order_id' => 2,
            'product_id' => 2,
            'sku' => 'jacket-02',
            'name' => 'Premium Vintage Leather Jacket',
            'price' => 249.99,
            'qty_ordered' => 1,
            'qty_invoiced' => 1,
            'qty_shipped' => 0,
            'row_total' => 249.99
        ]);
        $this->db->table('invoices')->insert([
            'increment_id' => '300000002',
            'order_id' => 2,
            'grand_total' => 259.99,
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
        ]);

        // Order 3: Pending order from yesterday
        $this->db->table('orders')->insert([
            'id' => 3,
            'increment_id' => '100000003',
            'customer_id' => 3,
            'customer_email' => 'alice.johnson@example.com',
            'customer_firstname' => 'Alice',
            'customer_lastname' => 'Johnson',
            'status' => 'pending',
            'subtotal' => 109.98,
            'grand_total' => 119.98,
            'shipping_amount' => 10.00,
            'shipping_description' => 'Flat Rate - Fixed',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day')),
            'updated_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]);
        $this->db->table('order_items')->insertBatch([
            [
                'order_id' => 3,
                'product_id' => 5,
                'sku' => 'backpack-05',
                'name' => 'Waterproof Travel Laptop Backpack',
                'price' => 49.99,
                'qty_ordered' => 1,
                'qty_invoiced' => 0,
                'qty_shipped' => 0,
                'row_total' => 49.99
            ],
            [
                'order_id' => 3,
                'product_id' => 5, // We can reuse product or use shirt
                'sku' => 'backpack-05',
                'name' => 'Waterproof Travel Laptop Backpack',
                'price' => 49.99,
                'qty_ordered' => 1,
                'qty_invoiced' => 0,
                'qty_shipped' => 0,
                'row_total' => 49.99
            ],
            [
                'order_id' => 3,
                'product_id' => null, // Dynamic custom item
                'sku' => 'gift-wrap',
                'name' => 'Premium Gift Wrapping Service',
                'price' => 10.00,
                'qty_ordered' => 1,
                'qty_invoiced' => 0,
                'qty_shipped' => 0,
                'row_total' => 10.00
            ]
        ]);

        // Order 4: Complete order from today
        $this->db->table('orders')->insert([
            'id' => 4,
            'increment_id' => '100000004',
            'customer_id' => 1,
            'customer_email' => 'jane.doe@example.com',
            'customer_firstname' => 'Jane',
            'customer_lastname' => 'Doe',
            'status' => 'complete',
            'subtotal' => 199.99,
            'grand_total' => 209.99,
            'shipping_amount' => 10.00,
            'shipping_description' => 'Flat Rate - Fixed',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $this->db->table('order_items')->insert([
            'order_id' => 4,
            'product_id' => 4,
            'sku' => 'watch-04',
            'name' => 'Chronograph Stainless Steel Watch',
            'price' => 199.99,
            'qty_ordered' => 1,
            'qty_invoiced' => 1,
            'qty_shipped' => 1,
            'row_total' => 199.99
        ]);
        $this->db->table('invoices')->insert([
            'increment_id' => '300000003',
            'order_id' => 4,
            'grand_total' => 209.99,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $this->db->table('shipments')->insert([
            'increment_id' => '400000002',
            'order_id' => 4,
            'tracks' => json_encode([['carrier' => 'DHL', 'number' => '987654321']]),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // 8. CMS Pages
        $cmsPages = [
            [
                'title' => 'Home Page',
                'identifier' => 'home',
                'content' => '<h1>Welcome to our main store front</h1><p>This is standard CMS homepage content.</p>',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'About Us',
                'identifier' => 'about-us',
                'content' => '<h1>About Our Company</h1><p>We supply high quality apparel and electronic accessories.</p>',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Privacy Policy',
                'identifier' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Details regarding data storage and policy details.</p>',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('cms_pages')->insertBatch($cmsPages);

        // 9. CMS Blocks
        $cmsBlocks = [
            [
                'title' => 'Footer Links Block',
                'identifier' => 'footer_links',
                'content' => '<ul><li><a href="/about-us">About Us</a></li><li><a href="/privacy-policy">Privacy Policy</a></li></ul>',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Promotion Banner Block',
                'identifier' => 'promo_banner',
                'content' => '<div class="promo-banner"><h2>Summer Sale! Get 20% off all sportswear.</h2></div>',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('cms_blocks')->insertBatch($cmsBlocks);
    }
}
