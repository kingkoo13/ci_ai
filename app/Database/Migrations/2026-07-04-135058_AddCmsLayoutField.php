<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCmsLayoutField extends Migration
{
    public function up()
    {
        // 1. Add page_layout column to cms_pages table
        $this->forge->addColumn('cms_pages', [
            'page_layout' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => '1column',
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('cms_pages', 'page_layout');
    }
}
