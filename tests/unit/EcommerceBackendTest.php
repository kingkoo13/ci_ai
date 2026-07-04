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
}
