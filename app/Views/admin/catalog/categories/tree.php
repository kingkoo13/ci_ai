<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?>Categories Tree<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="page-header-row">
    <div>
        <div class="breadcrumbs">
            Admin <span>/</span> Catalog <span>/</span> Categories
        </div>
        <h1 class="page-title">Categories</h1>
    </div>
</div>

<div class="category-tree-panel">
    <!-- Left Panel: Category Tree -->
    <aside class="tree-container" aria-label="Category hierarchy">
        <div style="margin-bottom: 15px; display: flex; gap: 10px;">
            <button type="button" class="btn btn-secondary" onclick="clearForm(null, 'New Root Category')" style="flex-grow:1; font-size: 11px;">Add Root</button>
            <button type="button" class="btn btn-secondary" onclick="clearForm(<?= $selectedCategory ? $selectedCategory->id : '1' ?>, 'New Subcategory')" style="flex-grow:1; font-size: 11px;">Add Subcategory</button>
        </div>

        <hr style="border: 0; border-top: 1px solid var(--color-border-light); margin-bottom: 15px;">

        <!-- Recursive PHP Category Tree Renderer -->
        <?php
        function renderCategoryTree(array $nodes, $selectedId) {
            echo '<ul style="list-style: none; padding-left: 0;">';
            foreach ($nodes as $node) {
                $hasChildren = !empty($node->children);
                $isActive = ($node->id == $selectedId);
                
                echo '<li class="tree-node ' . ($isActive ? 'active' : '') . '" style="margin-top: 5px;">';
                echo '<div class="tree-node-header">';
                
                // Toggle icon
                echo '<span class="tree-node-toggle" onclick="toggleNode(event)">';
                if ($hasChildren) {
                    echo '<i class="fa-solid fa-chevron-down"></i>';
                } else {
                    echo '<i class="fa-solid fa-circle" style="font-size: 4px;"></i>';
                }
                echo '</span>';
                
                // Label
                echo '<a href="?id=' . $node->id . '" class="tree-node-label">';
                if ($node->id == 1) {
                    echo '<i class="fa-solid fa-folder-tree" style="margin-right: 5px; color: var(--color-primary);"></i> ';
                } else {
                    echo '<i class="fa-regular fa-folder" style="margin-right: 5px;"></i> ';
                }
                echo esc($node->name);
                echo '</a>';
                
                echo '</div>';
                
                if ($hasChildren) {
                    echo '<div class="tree-node-children" style="padding-left: 15px;">';
                    renderCategoryTree($node->children, $selectedId);
                    echo '</div>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }

        renderCategoryTree($tree, $selectedCategory ? $selectedCategory->id : null);
        ?>
    </aside>

    <!-- Right Panel: Editor Form -->
    <main class="dashboard-card">
        <div class="card-header">
            <span id="form-title"><?= $selectedCategory ? 'Edit Category: ' . esc($selectedCategory->name) : 'Category Details' ?></span>
            <div>
                <?php if ($selectedCategory && $selectedCategory->id != 1) : ?>
                    <button type="button" class="btn btn-danger" onclick="confirmDelete(<?= $selectedCategory->id ?>, '<?= esc($selectedCategory->name) ?>')" style="padding: 4px 10px; font-size: 11px; margin-right: 5px;">Delete</button>
                <?php endif; ?>
                <button type="submit" form="category-form" class="btn btn-primary" style="padding: 4px 12px; font-size: 11px;">Save Category</button>
            </div>
        </div>
        <div class="card-body">
            <form id="category-form" action="<?= base_url('admin/catalog/categories/save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="cat-id" value="<?= $selectedCategory ? $selectedCategory->id : '' ?>">

                <div class="form-group">
                    <label for="is_active">Enable Category</label>
                    <div class="form-control-wrapper">
                        <select id="is_active" name="is_active" class="form-control" style="width: 150px;">
                            <option value="1" <?= (!$selectedCategory || $selectedCategory->is_active == 1) ? 'selected' : '' ?>>Yes</option>
                            <option value="0" <?= ($selectedCategory && $selectedCategory->is_active == 0) ? 'selected' : '' ?>>No</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="name">Category Name <span class="required">*</span></label>
                    <div class="form-control-wrapper">
                        <input type="text" id="name" name="name" value="<?= $selectedCategory ? esc($selectedCategory->name) : '' ?>" class="form-control" required placeholder="e.g. Shirts">
                    </div>
                </div>

                <div class="form-group">
                    <label for="parent_id">Parent Category</label>
                    <div class="form-control-wrapper">
                        <select id="parent_id" name="parent_id" class="form-control">
                            <option value="">[No Parent / Root Level]</option>
                            <?php foreach ($categories as $cat) : ?>
                                <?php 
                                // Prevent selecting current category as parent
                                if ($selectedCategory && $cat->id == $selectedCategory->id) continue;
                                ?>
                                <option value="<?= $cat->id ?>" <?= ($selectedCategory && $selectedCategory->parent_id == $cat->id) ? 'selected' : '' ?>>
                                    <?= ($cat->id == 1) ? 'Root: ' : '' ?><?= esc($cat->name) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <div class="form-control-wrapper">
                        <textarea id="description" name="description" rows="5" class="form-control" placeholder="Category descriptions..."><?= $selectedCategory ? esc($selectedCategory->description) : '' ?></textarea>
                    </div>
                </div>

                <div style="margin-top:25px; border-top:1px solid var(--color-border-light); padding-top:15px;">
                    <h3 style="font-size:14px; font-weight:700; color:var(--color-text); margin-bottom:15px;">Category Custom Attributes</h3>
                    <div id="category-attributes-container">
                        <p style="color: var(--color-text-muted); font-style: italic;">Loading custom attributes...</p>
                    </div>
                </div>
            </form>
        </div>
    </main>
</div>

<!-- Category Delete Submission -->
<form id="delete-category-form" method="POST" style="display:none;">
    <?= csrf_field() ?>
</form>

<script>
    // Collapsible node toggle
    function toggleNode(event) {
        event.stopPropagation();
        const header = event.currentTarget.parentElement;
        const node = header.parentElement;
        const children = node.querySelector('.tree-node-children');
        const icon = event.currentTarget.querySelector('i');
        
        if (children) {
            if (children.style.display === 'none') {
                children.style.display = 'block';
                icon.className = 'fa-solid fa-chevron-down';
            } else {
                children.style.display = 'none';
                icon.className = 'fa-solid fa-chevron-right';
            }
        }
    }

    // Load category-specific EAV attributes
    function loadCategoryAttributes() {
        const container = document.getElementById('category-attributes-container');
        if (!container) return;

        const categoryId = document.getElementById('cat-id').value || 0;
        
        fetch('<?= base_url('admin/catalog/categories/getAttributes') ?>?category_id=' + categoryId)
            .then(response => response.text())
            .then(html => {
                container.innerHTML = html;
            })
            .catch(err => {
                container.innerHTML = '<p style="color: var(--color-danger);">Failed to load custom attributes.</p>';
            });
    }

    // Handles resetting form to build a new category (Add root/subcategory)
    function clearForm(parentId, titleText) {
        document.getElementById('form-title').innerText = titleText;
        document.getElementById('cat-id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('is_active').value = '1';
        
        const parentSelect = document.getElementById('parent_id');
        if (parentId) {
            parentSelect.value = parentId;
        } else {
            // For Root Category, set parent to empty
            parentSelect.value = '';
        }
        
        // Reload custom attributes with empty fields
        loadCategoryAttributes();
        
        // Focus the name input
        document.getElementById('name').focus();
    }

    // Delete category confirmation
    function confirmDelete(id, name) {
        if (confirm("Are you sure you want to delete category '" + name + "'?\n\nWarning: Subcategories will be moved to parent level, and product mapping configurations will be removed!")) {
            const form = document.getElementById('delete-category-form');
            form.action = "<?= base_url('admin/catalog/categories/delete/') ?>" + id;
            form.submit();
        }
    }

    // Trigger initial load on page load
    document.addEventListener("DOMContentLoaded", function() {
        loadCategoryAttributes();
    });
</script>
<?= $this->endSection() ?>
