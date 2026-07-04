<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Stores extends BaseController
{
    public function configuration()
    {
        $db = \Config\Database::connect();
        
        // Fetch all configurations
        $configs = $db->table('core_config_data')->get()->getResult();
        
        // Map path => value array
        $configMap = [];
        foreach ($configs as $config) {
            $configMap[$config->path] = $config->value;
        }

        $data = [
            'menu'      => 'stores',
            'submenu'   => 'configuration',
            'config'    => $configMap
        ];

        return view('admin/stores/configuration', $data);
    }

    public function save()
    {
        $db = \Config\Database::connect();
        
        // Post fields contain double underscores as replacements for slashes
        // e.g. general__store_information__name -> general/store_information/name
        $postData = $this->request->getPost();
        
        $db->transStart();
        
        foreach ($postData as $htmlName => $value) {
            // Skip CSRF tokens and other helper fields
            if ($htmlName === 'csrf_test_name') continue;

            $path = str_replace('__', '/', $htmlName);

            // Check if key already exists
            $existing = $db->table('core_config_data')->where('path', $path)->get()->getRow();
            if ($existing) {
                $db->table('core_config_data')
                   ->where('path', $path)
                   ->update(['value' => $value]);
            } else {
                $db->table('core_config_data')
                   ->insert([
                       'path'  => $path,
                       'value' => $value
                   ]);
            }
        }

        $db->transComplete();

        if ($db->transStatus() === false) {
            session()->setFlashdata('error', 'Failed to save configuration settings.');
        } else {
            session()->setFlashdata('success', 'The configuration has been saved successfully.');
        }

        return redirect()->to(base_url('admin/stores/configuration'));
    }
}
