<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEavAttributeSystem extends Migration
{
    public function up()
    {
        // 1. Attribute Sets
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'unique'     => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('attribute_sets', true);

        // 2. EAV Attributes
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'entity_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // product, category, customer, address, order
            ],
            'attribute_code' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
            ],
            'frontend_label' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'input_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // text, textarea, select, boolean
            ],
            'attribute_set_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'is_required' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('attribute_set_id', 'attribute_sets', 'id', 'SET NULL', 'CASCADE');
        $this->forge->createTable('eav_attributes', true);

        // 3. EAV Attribute Options (for dropdowns)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'attribute_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'option_value' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('attribute_id', 'eav_attributes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('eav_attribute_options', true);

        // 4. EAV Attribute Values
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'entity_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50', // product, category, customer, address, order
            ],
            'entity_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'attribute_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'value' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('attribute_id', 'eav_attributes', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('eav_attribute_values', true);

        // 5. Add attribute_set_id column to products table
        $this->forge->addColumn('products', [
            'attribute_set_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'attribute_set_id');
        $this->forge->dropTable('eav_attribute_values', true);
        $this->forge->dropTable('eav_attribute_options', true);
        $this->forge->dropTable('eav_attributes', true);
        $this->forge->dropTable('attribute_sets', true);
    }
}
