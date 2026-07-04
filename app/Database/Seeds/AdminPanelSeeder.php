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

        // 5. EAV Attribute Sets
        $attributeSets = [
            ['id' => 1, 'name' => 'Default', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'name' => 'Apparel', 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 3, 'name' => 'Footwear', 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('attribute_sets')->insertBatch($attributeSets);

        // 6. EAV Attributes & Options
        // Entity types: product, category, customer, address, order
        $eavAttributes = [
            // Product Attributes
            ['id' => 1, 'entity_type' => 'product', 'attribute_code' => 'color', 'frontend_label' => 'Color', 'input_type' => 'select', 'attribute_set_id' => 2, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 2, 'entity_type' => 'product', 'attribute_code' => 'size', 'frontend_label' => 'Size', 'input_type' => 'text', 'attribute_set_id' => 2, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 3, 'entity_type' => 'product', 'attribute_code' => 'brand', 'frontend_label' => 'Brand', 'input_type' => 'select', 'attribute_set_id' => 1, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 4, 'entity_type' => 'product', 'attribute_code' => 'shoe_size', 'frontend_label' => 'Shoe Size', 'input_type' => 'text', 'attribute_set_id' => 3, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            
            // Category Attributes
            ['id' => 5, 'entity_type' => 'category', 'attribute_code' => 'category_image', 'frontend_label' => 'Category Banner Image', 'input_type' => 'text', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 6, 'entity_type' => 'category', 'attribute_code' => 'custom_layout', 'frontend_label' => 'Category Custom Layout', 'input_type' => 'select', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 7, 'entity_type' => 'category', 'attribute_code' => 'is_anchor', 'frontend_label' => 'Is Anchor', 'input_type' => 'boolean', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            
            // Customer Attributes
            ['id' => 8, 'entity_type' => 'customer', 'attribute_code' => 'date_of_birth', 'frontend_label' => 'Date of Birth', 'input_type' => 'text', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 9, 'entity_type' => 'customer', 'attribute_code' => 'vat_id', 'frontend_label' => 'Tax/VAT Number', 'input_type' => 'text', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 10, 'entity_type' => 'customer', 'attribute_code' => 'gender', 'frontend_label' => 'Gender', 'input_type' => 'select', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],

            // Address Attributes
            ['id' => 11, 'entity_type' => 'address', 'attribute_code' => 'delivery_instructions', 'frontend_label' => 'Delivery Instructions', 'input_type' => 'textarea', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 12, 'entity_type' => 'address', 'attribute_code' => 'gate_code', 'frontend_label' => 'Building Gate Code', 'input_type' => 'text', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],

            // Order Attributes
            ['id' => 13, 'entity_type' => 'order', 'attribute_code' => 'preferred_delivery_date', 'frontend_label' => 'Preferred Delivery Date', 'input_type' => 'text', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 14, 'entity_type' => 'order', 'attribute_code' => 'gift_message', 'frontend_label' => 'Gift Wrap Message', 'input_type' => 'textarea', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],

            // Page Layout attributes for Products & Categories
            ['id' => 15, 'entity_type' => 'product', 'attribute_code' => 'page_layout', 'frontend_label' => 'Page Layout', 'input_type' => 'select', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
            ['id' => 16, 'entity_type' => 'category', 'attribute_code' => 'page_layout', 'frontend_label' => 'Page Layout', 'input_type' => 'select', 'attribute_set_id' => null, 'is_required' => 0, 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('eav_attributes')->insertBatch($eavAttributes);

        // Options for selects
        $eavAttributeOptions = [
            // Color options (attribute_id 1)
            ['attribute_id' => 1, 'option_value' => 'Red'],
            ['attribute_id' => 1, 'option_value' => 'Blue'],
            ['attribute_id' => 1, 'option_value' => 'Black'],
            ['attribute_id' => 1, 'option_value' => 'Green'],

            // Brand options (attribute_id 3)
            ['attribute_id' => 3, 'option_value' => 'Nike'],
            ['attribute_id' => 3, 'option_value' => 'Adidas'],
            ['attribute_id' => 3, 'option_value' => 'Puma'],
            ['attribute_id' => 3, 'option_value' => 'Vintage'],

            // Category Custom Layout options (attribute_id 6)
            ['attribute_id' => 6, 'option_value' => 'Grid View Only'],
            ['attribute_id' => 6, 'option_value' => 'List View Only'],
            ['attribute_id' => 6, 'option_value' => 'Custom Static Block Only'],

            // Gender options (attribute_id 10)
            ['attribute_id' => 10, 'option_value' => 'Male'],
            ['attribute_id' => 10, 'option_value' => 'Female'],
            ['attribute_id' => 10, 'option_value' => 'Unspecified'],

            // Page Layout options for Products (attribute_id 15)
            ['attribute_id' => 15, 'option_value' => '1column'],
            ['attribute_id' => 15, 'option_value' => '2columns-left'],
            ['attribute_id' => 15, 'option_value' => '2columns-right'],
            ['attribute_id' => 15, 'option_value' => '3columns'],

            // Page Layout options for Categories (attribute_id 16)
            ['attribute_id' => 16, 'option_value' => '1column'],
            ['attribute_id' => 16, 'option_value' => '2columns-left'],
            ['attribute_id' => 16, 'option_value' => '2columns-right'],
            ['attribute_id' => 16, 'option_value' => '3columns'],
        ];
        $this->db->table('eav_attribute_options')->insertBatch($eavAttributeOptions);

        // 7. Products (Updated with attribute_set_id)
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
                'attribute_set_id' => 3, // Footwear
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
                'attribute_set_id' => 2, // Apparel
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
                'attribute_set_id' => 2, // Apparel
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
                'attribute_set_id' => 1, // Default
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
                'attribute_set_id' => 1, // Default
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

        // 8. EAV Seed Values
        $eavValues = [
            // Shoes size and brand values (product_id 1, attribute 4 (shoe_size) and attribute 3 (brand))
            ['entity_type' => 'product', 'entity_id' => 1, 'attribute_id' => 4, 'value' => 'US 10.5'],
            ['entity_type' => 'product', 'entity_id' => 1, 'attribute_id' => 3, 'value' => 'Nike'],
            ['entity_type' => 'product', 'entity_id' => 1, 'attribute_id' => 15, 'value' => '1column'], // Page layout for shoes

            // Jacket color, size, material (product_id 2, attributes 1, 2)
            ['entity_type' => 'product', 'entity_id' => 2, 'attribute_id' => 1, 'value' => 'Black'],
            ['entity_type' => 'product', 'entity_id' => 2, 'attribute_id' => 2, 'value' => 'L'],
            ['entity_type' => 'product', 'entity_id' => 2, 'attribute_id' => 15, 'value' => '2columns-right'], // Page layout for jacket

            // Shirt color, size (product_id 3)
            ['entity_type' => 'product', 'entity_id' => 3, 'attribute_id' => 1, 'value' => 'Blue'],
            ['entity_type' => 'product', 'entity_id' => 3, 'attribute_id' => 2, 'value' => 'M'],

            // Watch brand (product_id 4)
            ['entity_type' => 'product', 'entity_id' => 4, 'attribute_id' => 3, 'value' => 'Vintage'],

            // Category Attributes values: Category 2 (Men) layouts/banners (attributes 5, 6, 7)
            ['entity_type' => 'category', 'entity_id' => 2, 'attribute_id' => 5, 'value' => 'assets/images/banner-men.jpg'],
            ['entity_type' => 'category', 'entity_id' => 2, 'attribute_id' => 6, 'value' => 'Grid View Only'],
            ['entity_type' => 'category', 'entity_id' => 2, 'attribute_id' => 7, 'value' => '1'],
            ['entity_type' => 'category', 'entity_id' => 2, 'attribute_id' => 16, 'value' => '2columns-left'], // Category Page layout

            // Category 3 (Women) layout
            ['entity_type' => 'category', 'entity_id' => 3, 'attribute_id' => 16, 'value' => '2columns-left'],
            // Category 4 (Accessories) layout
            ['entity_type' => 'category', 'entity_id' => 4, 'attribute_id' => 16, 'value' => '3columns'], // 3 columns for accessories!

            // Customer Attributes values: Customer 1 (Jane Doe) (attributes 8, 9, 10)
            ['entity_type' => 'customer', 'entity_id' => 1, 'attribute_id' => 8, 'value' => '1992-05-15'],
            ['entity_type' => 'customer', 'entity_id' => 1, 'attribute_id' => 9, 'value' => 'US88371991'],
            ['entity_type' => 'customer', 'entity_id' => 1, 'attribute_id' => 10, 'value' => 'Female'],

            // Address Attributes values: Address 1 (Jane's address) (attributes 11, 12)
            ['entity_type' => 'address', 'entity_id' => 1, 'attribute_id' => 11, 'value' => 'Please leave it at the gate if no one answers.'],
            ['entity_type' => 'address', 'entity_id' => 1, 'attribute_id' => 12, 'value' => '#4819'],

            // Order Attributes values: Order 1 (increment 100000001) (attributes 13, 14)
            ['entity_type' => 'order', 'entity_id' => 1, 'attribute_id' => 13, 'value' => '2026-07-10'],
            ['entity_type' => 'order', 'entity_id' => 1, 'attribute_id' => 14, 'value' => 'Happy birthday! Enjoy your gifts.'],
        ];
        $this->db->table('eav_attribute_values')->insertBatch($eavValues);

        // 9. Customers
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
                'id' => 1,
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
                'id' => 2,
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

        // 10. Orders & Items
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

        // 11. CMS Pages (with page_layout)
        $cmsPages = [
            [
                'title' => 'Home Page',
                'identifier' => 'home',
                'content' => '<h1>Welcome to Magento 2 CodeIgniter Edition</h1><p>We supply high quality apparel, running shoes, and luxury accessories. Control this storefront layout directly from the admin panel!</p>',
                'page_layout' => '1column',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'About Us',
                'identifier' => 'about-us',
                'content' => '<h1>About Our Company</h1><p>We are a modern, high-speed e-commerce solution powered by CodeIgniter 4 and SQLite.</p>',
                'page_layout' => '2columns-left',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ],
            [
                'title' => 'Privacy Policy',
                'identifier' => 'privacy-policy',
                'content' => '<h1>Privacy Policy</h1><p>Your privacy is important to us. Standard data collection terms apply.</p>',
                'page_layout' => '1column',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('cms_pages')->insertBatch($cmsPages);

        // 12. CMS Blocks
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
                'content' => '<div style="background:var(--color-primary); color:white; padding:15px; text-align:center; margin-bottom:20px; font-weight:600; border-radius:4px;">Summer Sale! Get 20% off all sportswear. Use Coupon: SUMMER20</div>',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('cms_blocks')->insertBatch($cmsBlocks);
    }
}
