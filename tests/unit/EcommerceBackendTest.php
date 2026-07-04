<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;

class EcommerceBackendTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = false;
    protected $seed = '';

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Run migrations manually using the migrations service
        $migrations = \Config\Services::migrations();
        $migrations->setNamespace('App');
        $migrations->latest();

        // 2. Clear all existing data from tables to ensure clean state for seeding
        $this->db->disableForeignKeyChecks();
        $tables = $this->db->listTables();
        foreach ($tables as $table) {
            // Ignore migrations control tables
            if (str_contains($table, 'migrations')) {
                continue;
            }
            // Truncate table
            $this->db->table($table)->truncate();
        }
        $this->db->enableForeignKeyChecks();

        // 3. Run seeder manually
        $seeder = \Config\Database::seeder();
        $seeder->call('App\Database\Seeds\AdminPanelSeeder');
    }

    public function testDatabaseTablesExist()
    {
        $this->assertTrue($this->db->tableExists('products'));
        $this->assertTrue($this->db->tableExists('categories'));
        $this->assertTrue($this->db->tableExists('orders'));
        $this->assertTrue($this->db->tableExists('admin_users'));
        $this->assertTrue($this->db->tableExists('core_config_data'));
    }

    public function testDefaultAdminSeeded()
    {
        $admin = $this->db->table('admin_users')->where('username', 'admin')->get()->getRow();
        
        $this->assertNotNull($admin);
        $this->assertEquals('admin@example.com', $admin->email);
        $this->assertTrue(password_verify('admin123', $admin->password));
    }

    public function testSampleProductsExist()
    {
        $count = $this->db->table('products')->countAll();
        
        $this->assertGreaterThan(0, $count);
        
        $shoes = $this->db->table('products')->where('sku', 'shoes-01')->get()->getRow();
        $this->assertNotNull($shoes);
        $this->assertEquals('Sleek Athletic Running Shoes', $shoes->name);
    }

    public function testSampleOrdersExist()
    {
        $ordersCount = $this->db->table('orders')->countAll();
        
        $this->assertGreaterThan(0, $ordersCount);
    }

    public function testEavAttributeSystem()
    {
        // 1. Assert EAV tables exist
        $this->assertTrue($this->db->tableExists('attribute_sets'));
        $this->assertTrue($this->db->tableExists('eav_attributes'));
        $this->assertTrue($this->db->tableExists('eav_attribute_options'));
        $this->assertTrue($this->db->tableExists('eav_attribute_values'));

        // 2. Assert seeder correctly seeded attribute sets
        $setsCount = $this->db->table('attribute_sets')->countAll();
        $this->assertEquals(3, $setsCount); // Default, Apparel, Footwear

        // 3. Assert seeder seeded attributes
        $attributesCount = $this->db->table('eav_attributes')->countAll();
        $this->assertGreaterThan(10, $attributesCount);

        // 4. Assert product EAV values are linked and correct
        // Product 2 (Vintage Leather Jacket) color is Black
        $jacketColor = $this->db->table('eav_attribute_values')
                                ->where('entity_type', 'product')
                                ->where('entity_id', 2)
                                ->where('attribute_id', 1) // color attribute
                                ->get()
                                ->getRow();
        $this->assertNotNull($jacketColor);
        $this->assertEquals('Black', $jacketColor->value);

        // 5. Assert Category EAV values: Category 2 (Men) layout style
        $catLayout = $this->db->table('eav_attribute_values')
                              ->where('entity_type', 'category')
                              ->where('entity_id', 2)
                              ->where('attribute_id', 6) // custom_layout attribute
                              ->get()
                              ->getRow();
        $this->assertNotNull($catLayout);
        $this->assertEquals('Grid View Only', $catLayout->value);

        // 6. Assert Customer EAV values: Customer 1 gender
        $custGender = $this->db->table('eav_attribute_values')
                               ->where('entity_type', 'customer')
                               ->where('entity_id', 1)
                               ->where('attribute_id', 10) // gender attribute
                               ->get()
                               ->getRow();
        $this->assertNotNull($custGender);
        $this->assertEquals('Female', $custGender->value);

        // 7. Assert Address EAV values: Address 1 gate code
        $addrGate = $this->db->table('eav_attribute_values')
                             ->where('entity_type', 'address')
                             ->where('entity_id', 1)
                             ->where('attribute_id', 12) // gate_code attribute
                             ->get()
                             ->getRow();
        $this->assertNotNull($addrGate);
        $this->assertEquals('#4819', $addrGate->value);

        // 8. Assert Order EAV values: Order 1 gift message
        $orderGift = $this->db->table('eav_attribute_values')
                              ->where('entity_type', 'order')
                              ->where('entity_id', 1)
                              ->where('attribute_id', 14) // gift_message attribute
                              ->get()
                              ->getRow();
        $this->assertNotNull($orderGift);
        $this->assertEquals('Happy birthday! Enjoy your gifts.', $orderGift->value);
    }
}
