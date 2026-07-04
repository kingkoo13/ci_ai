<?php

namespace App\Controllers;

class Storefront extends BaseController
{
    // Storefront Homepage
    public function index()
    {
        $db = \Config\Database::connect();
        
        // Load the home CMS page content
        $page = $db->table('cms_pages')
                   ->where('identifier', 'home')
                   ->where('is_active', 1)
                   ->get()
                   ->getRow();

        // Fallback content if home CMS is missing
        $content = $page ? $page->content : '<h1>Welcome to LUMACI</h1><p>Define your homepage layouts in Content CMS panel.</p>';
        $layout = $page ? $page->page_layout : '1column';

        // Load featured items for storefront grid
        $products = $db->table('products')
                       ->where('status', 1)
                       ->limit(4)
                       ->get()
                       ->getResult();

        $data = [
            'pageLayout' => $layout,
            'content'    => $content,
            'products'   => $products,
        ];

        return view('storefront/cms/page', $data);
    }

    // Category Page (PLP with Layered Navigation filters)
    public function category($id)
    {
        $db = \Config\Database::connect();
        
        $category = $db->table('categories')->where('id', $id)->where('is_active', 1)->get()->getRow();
        if (!$category) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Category not found.");
        }

        // 1. Determine EAV page layout for this category
        $layoutValue = $db->table('eav_attribute_values')
                          ->select('value')
                          ->join('eav_attributes', 'eav_attributes.id = eav_attribute_values.attribute_id')
                          ->where('eav_attribute_values.entity_type', 'category')
                          ->where('eav_attribute_values.entity_id', $id)
                          ->where('eav_attributes.attribute_code', 'page_layout')
                          ->get()
                          ->getRow();
        $pageLayout = $layoutValue ? $layoutValue->value : '2columns-left';

        // 2. Fetch category header EAV attributes (e.g. category image banner)
        $bannerValue = $db->table('eav_attribute_values')
                           ->select('value')
                           ->join('eav_attributes', 'eav_attributes.id = eav_attribute_values.attribute_id')
                           ->where('eav_attribute_values.entity_type', 'category')
                           ->where('eav_attribute_values.entity_id', $id)
                           ->where('eav_attributes.attribute_code', 'category_image')
                           ->get()
                           ->getRow();
        $bannerImg = $bannerValue ? $bannerValue->value : '';

        // 3. Fetch filterable EAV Attributes (e.g. Color, Brand)
        $filterableAttributes = $db->table('eav_attributes')
                                   ->where('entity_type', 'product')
                                   ->where('input_type', 'select') // only dropdown types are filterable
                                   ->get()
                                   ->getResult();

        // 4. Build product list query for this category
        $productQuery = $db->table('products')
                           ->select('products.*')
                           ->join('product_categories', 'product_categories.product_id = products.id')
                           ->where('product_categories.category_id', $id)
                           ->where('products.status', 1);

        // Apply active EAV filters from query string
        $activeFilters = [];
        foreach ($filterableAttributes as $attr) {
            $queryVal = $this->request->getGet($attr->attribute_code);
            if ($queryVal !== null && $queryVal !== '') {
                $activeFilters[$attr->attribute_code] = $queryVal;
                
                // Subquery to filter products that have this specific EAV attribute value
                $subQuery = $db->table('eav_attribute_values')
                               ->select('entity_id')
                               ->where('entity_type', 'product')
                               ->where('attribute_id', $attr->id)
                               ->where('value', $queryVal)
                               ->get()
                               ->getResult();
                
                $matchingProductIds = array_column($subQuery, 'entity_id') ?: [0];
                $productQuery->whereIn('products.id', $matchingProductIds);
            }
        }

        $products = $productQuery->get()->getResult();

        // 5. Calculate filters counts for layered navigation sidebar options
        // For each filter option, count how many products match
        $sidebarFilters = [];
        foreach ($filterableAttributes as $attr) {
            // Get all possible options for this dropdown attribute
            $options = $db->table('eav_attribute_options')
                          ->where('attribute_id', $attr->id)
                          ->get()
                          ->getResult();

            $optionsCounts = [];
            foreach ($options as $opt) {
                // Count products in this category that have this EAV option value
                $countQuery = $db->table('products')
                                 ->join('product_categories', 'product_categories.product_id = products.id')
                                 ->join('eav_attribute_values', 'eav_attribute_values.entity_id = products.id')
                                 ->where('product_categories.category_id', $id)
                                 ->where('products.status', 1)
                                 ->where('eav_attribute_values.entity_type', 'product')
                                 ->where('eav_attribute_values.attribute_id', $attr->id)
                                 ->where('eav_attribute_values.value', $opt->option_value);

                $count = $countQuery->countAllResults();
                if ($count > 0) {
                    $optionsCounts[] = [
                        'label' => $opt->option_value,
                        'count' => $count
                    ];
                }
            }

            if (!empty($optionsCounts)) {
                $sidebarFilters[] = [
                    'code'    => $attr->attribute_code,
                    'label'   => $attr->frontend_label,
                    'options' => $optionsCounts
                ];
            }
        }

        $data = [
            'category'        => $category,
            'pageLayout'      => $pageLayout,
            'bannerImg'       => $bannerImg,
            'products'        => $products,
            'sidebarFilters'  => $sidebarFilters,
            'activeFilters'   => $activeFilters
        ];

        return view('storefront/catalog/category', $data);
    }

    // Product Detail Page (PDP)
    public function product($id)
    {
        $db = \Config\Database::connect();
        
        $product = $db->table('products')->where('id', $id)->where('status', 1)->get()->getRow();
        if (!$product) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("Product not found.");
        }

        // 1. Determine EAV page layout for this product
        $layoutValue = $db->table('eav_attribute_values')
                          ->select('value')
                          ->join('eav_attributes', 'eav_attributes.id = eav_attribute_values.attribute_id')
                          ->where('eav_attribute_values.entity_type', 'product')
                          ->where('eav_attribute_values.entity_id', $id)
                          ->where('eav_attributes.attribute_code', 'page_layout')
                          ->get()
                          ->getRow();
        $pageLayout = $layoutValue ? $layoutValue->value : '1column';

        // 2. Fetch custom EAV specifications (exclude page_layout attribute from list)
        $specs = $db->table('eav_attribute_values')
                    ->select('eav_attributes.frontend_label, eav_attribute_values.value, eav_attributes.input_type')
                    ->join('eav_attributes', 'eav_attributes.id = eav_attribute_values.attribute_id')
                    ->where('eav_attribute_values.entity_type', 'product')
                    ->where('eav_attribute_values.entity_id', $id)
                    ->where('eav_attributes.attribute_code !=', 'page_layout')
                    ->get()
                    ->getResult();

        // 3. Fetch related products (e.g. from same category) for sidebars
        $relatedProducts = [];
        $firstCategory = $db->table('product_categories')->where('product_id', $id)->get()->getRow();
        if ($firstCategory) {
            $relatedProducts = $db->table('products')
                                  ->join('product_categories', 'product_categories.product_id = products.id')
                                  ->where('product_categories.category_id', $firstCategory->category_id)
                                  ->where('products.id !=', $id)
                                  ->where('products.status', 1)
                                  ->limit(3)
                                  ->get()
                                  ->getResult();
        }

        $data = [
            'product'         => $product,
            'pageLayout'      => $pageLayout,
            'specs'           => $specs,
            'relatedProducts' => $relatedProducts
        ];

        return view('storefront/catalog/product', $data);
    }

    // CMS Static Page Router
    public function cmsPage($identifier)
    {
        $db = \Config\Database::connect();
        
        $page = $db->table('cms_pages')
                   ->where('identifier', $identifier)
                   ->where('is_active', 1)
                   ->get()
                   ->getRow();

        if (!$page) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound("CMS Page not found.");
        }

        $data = [
            'pageLayout' => $page->page_layout ?: '1column',
            'content'    => $page->content,
            'pageTitle'  => $page->title
        ];

        return view('storefront/cms/page', $data);
    }
}
