<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;

class Content extends BaseController
{
    // CMS Pages Grid
    public function pages()
    {
        $db = \Config\Database::connect();
        $pages = $db->table('cms_pages')->orderBy('id', 'DESC')->get()->getResult();

        $data = [
            'menu'    => 'content',
            'submenu' => 'pages',
            'pages'   => $pages
        ];

        return view('admin/content/pages_grid', $data);
    }

    // Edit CMS Page
    public function editPage($id)
    {
        $db = \Config\Database::connect();
        $page = $db->table('cms_pages')->where('id', $id)->get()->getRow();
        
        if (!$page) {
            session()->setFlashdata('error', 'CMS Page not found.');
            return redirect()->to(base_url('admin/content/pages'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'title'      => 'required',
                'identifier' => "required|is_unique[cms_pages.identifier,id,{$id}]",
                'content'    => 'required',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $pageData = [
                'title'       => $this->request->getPost('title'),
                'identifier'  => $this->request->getPost('identifier'),
                'content'     => $this->request->getPost('content'),
                'page_layout' => $this->request->getPost('page_layout') ?: '1column',
                'is_active'   => $this->request->getPost('is_active') ?? 1,
                'updated_at'  => date('Y-m-d H:i:s')
            ];

            $db->table('cms_pages')->where('id', $id)->update($pageData);

            session()->setFlashdata('success', 'The page has been saved successfully.');
            return redirect()->to(base_url('admin/content/pages'));
        }

        $data = [
            'menu'    => 'content',
            'submenu' => 'pages',
            'page'    => $page
        ];

        return view('admin/content/page_form', $data);
    }

    // CMS Blocks Grid
    public function blocks()
    {
        $db = \Config\Database::connect();
        $blocks = $db->table('cms_blocks')->orderBy('id', 'DESC')->get()->getResult();

        $data = [
            'menu'    => 'content',
            'submenu' => 'blocks',
            'blocks'  => $blocks
        ];

        return view('admin/content/blocks_grid', $data);
    }

    // Edit CMS Block
    public function editBlock($id)
    {
        $db = \Config\Database::connect();
        $block = $db->table('cms_blocks')->where('id', $id)->get()->getRow();
        
        if (!$block) {
            session()->setFlashdata('error', 'CMS Block not found.');
            return redirect()->to(base_url('admin/content/blocks'));
        }

        if ($this->request->getMethod() === 'POST' || $this->request->is('post')) {
            $validation = \Config\Services::validation();
            
            $validation->setRules([
                'title'      => 'required',
                'identifier' => "required|is_unique[cms_blocks.identifier,id,{$id}]",
                'content'    => 'required',
            ]);

            if (!$validation->withRequest($this->request)->run()) {
                session()->setFlashdata('error', implode('<br>', $validation->getErrors()));
                return redirect()->back()->withInput();
            }

            $blockData = [
                'title'      => $this->request->getPost('title'),
                'identifier' => $this->request->getPost('identifier'),
                'content'    => $this->request->getPost('content'),
                'is_active'  => $this->request->getPost('is_active') ?? 1,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            $db->table('cms_blocks')->where('id', $id)->update($blockData);

            session()->setFlashdata('success', 'The block has been saved successfully.');
            return redirect()->to(base_url('admin/content/blocks'));
        }

        $data = [
            'menu'    => 'content',
            'submenu' => 'blocks',
            'block'   => $block
        ];

        return view('admin/content/block_form', $data);
    }
}
