<?= $this->extend('admin/layout') ?>

<?= $this->section('title') ?><?= ($isEdit) ? 'Edit Attribute: ' . esc($attribute->attribute_code) : 'New Attribute' ?><?= $this->endSection() ?>

<?= $this->section('content') ?>
<form action="" method="POST" id="attribute-form">
    <?= csrf_field() ?>

    <div class="page-header-row">
        <div>
            <div class="breadcrumbs">
                Admin <span>/</span> Stores <span>/</span> Attributes <span>/</span> <?= ($isEdit) ? 'Edit' : 'New' ?>
            </div>
            <h1 class="page-title"><?= ($isEdit) ? 'Edit Attribute: ' . esc($attribute->attribute_code) : 'New Attribute' ?></h1>
        </div>
        <div class="page-actions">
            <a href="<?= base_url('admin/stores/attributes') ?>" class="btn btn-secondary">Back</a>
            <button type="submit" class="btn btn-primary">Save Attribute</button>
        </div>
    </div>

    <!-- Forms Panel -->
    <div class="dashboard-card" style="margin-bottom: 25px;">
        <div class="card-header">Attribute Properties</div>
        <div class="card-body">
            
            <div class="form-group">
                <label for="entity_type">Entity Type <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <?php if ($isEdit) : ?>
                        <input type="text" value="<?= esc(ucfirst($attribute->entity_type)) ?>" class="form-control" readonly style="background:#f0f0f0;">
                        <input type="hidden" name="entity_type" value="<?= esc($attribute->entity_type) ?>">
                    <?php else : ?>
                        <select id="entity_type" name="entity_type" class="form-control" onchange="toggleAttributeSet(this.value)" required style="width: 250px;">
                            <option value="product" <?= (old('entity_type') === 'product') ? 'selected' : '' ?>>Product</option>
                            <option value="category" <?= (old('entity_type') === 'category') ? 'selected' : '' ?>>Category</option>
                            <option value="customer" <?= (old('entity_type') === 'customer') ? 'selected' : '' ?>>Customer</option>
                            <option value="address" <?= (old('entity_type') === 'address') ? 'selected' : '' ?>>Customer Address</option>
                            <option value="order" <?= (old('entity_type') === 'order') ? 'selected' : '' ?>>Sales Order</option>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="attribute_code">Attribute Code <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="attribute_code" name="attribute_code" value="<?= esc(old('attribute_code', $attribute->attribute_code ?? '')) ?>" class="form-control" required placeholder="e.g. shoe_size" <?= ($isEdit) ? 'readonly style="background:#f0f0f0;"' : '' ?>>
                    <span class="form-note">Must be alphanumeric snake_case code code (unique for entity).</span>
                </div>
            </div>

            <div class="form-group">
                <label for="frontend_label">Frontend Label <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <input type="text" id="frontend_label" name="frontend_label" value="<?= esc(old('frontend_label', $attribute->frontend_label ?? '')) ?>" class="form-control" required placeholder="e.g. Shoe Size">
                </div>
            </div>

            <div class="form-group">
                <label for="input_type">Catalog Input Type <span class="required">*</span></label>
                <div class="form-control-wrapper">
                    <?php if ($isEdit) : ?>
                        <input type="text" value="<?= esc(ucfirst($attribute->input_type)) ?>" class="form-control" readonly style="background:#f0f0f0;">
                        <input type="hidden" name="input_type" id="input_type" value="<?= esc($attribute->input_type) ?>">
                    <?php else : ?>
                        <select id="input_type" name="input_type" class="form-control" onchange="toggleOptionsPanel(this.value)" required style="width: 250px;">
                            <option value="text" <?= (old('input_type') === 'text') ? 'selected' : '' ?>>Text Field</option>
                            <option value="textarea" <?= (old('input_type') === 'textarea') ? 'selected' : '' ?>>Text Area</option>
                            <option value="select" <?= (old('input_type') === 'select') ? 'selected' : '' ?>>Dropdown Select</option>
                            <option value="boolean" <?= (old('input_type') === 'boolean') ? 'selected' : '' ?>>Yes/No Toggle</option>
                        </select>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Attribute set dropdown (Visible only for Product Entity) -->
            <div class="form-group" id="attribute-set-group" style="display: <?= (!$isEdit || $attribute->entity_type === 'product') ? 'grid' : 'none' ?>;">
                <label for="attribute_set_id">Assign to Attribute Set</label>
                <div class="form-control-wrapper">
                    <select id="attribute_set_id" name="attribute_set_id" class="form-control" style="width: 250px;">
                        <option value="">[None / Floating Attribute]</option>
                        <?php foreach ($sets as $set) : ?>
                            <option value="<?= $set->id ?>" <?= ((old('attribute_set_id', $attribute->attribute_set_id ?? '') == $set->id)) ? 'selected' : '' ?>>
                                <?= esc($set->name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <span class="form-note">Grouping sets. Used on product edits.</span>
                </div>
            </div>

            <div class="form-group">
                <label for="is_required">Values Required</label>
                <div class="form-control-wrapper">
                    <select id="is_required" name="is_required" class="form-control" style="width: 150px;">
                        <option value="0" <?= (old('is_required', $attribute->is_required ?? 0) == 0) ? 'selected' : '' ?>>No</option>
                        <option value="1" <?= (old('is_required', $attribute->is_required ?? 0) == 1) ? 'selected' : '' ?>>Yes</option>
                    </select>
                </div>
            </div>

        </div>
    </div>

    <!-- Dropdown Options manager (Visible only if input_type is 'select') -->
    <div class="dashboard-card" id="options-panel" style="display: <?= (($isEdit && $attribute->input_type === 'select') || old('input_type') === 'select') ? 'block' : 'none' ?>; margin-bottom: 25px;">
        <div class="card-header">
            <span>Manage Dropdown Options</span>
            <button type="button" class="btn" onclick="addOptionRow('')"><i class="fa-solid fa-plus"></i> Add Option</button>
        </div>
        <div class="card-body">
            <div id="options-container" style="display:flex; flex-direction:column; gap:10px; max-width: 500px;">
                <!-- Prepopulate options in Edit Mode -->
                <?php if ($isEdit && !empty($options)) : ?>
                    <?php foreach ($options as $opt) : ?>
                        <div class="option-row" style="display:flex; gap:10px; align-items:center;">
                            <input type="text" name="options[]" value="<?= esc($opt->option_value) ?>" class="form-control" placeholder="Option label" required>
                            <button type="button" class="btn" onclick="this.parentElement.remove()" style="color:var(--color-danger); border-color:var(--color-danger); padding:6px 10px;"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <span class="form-note" style="margin-top:10px; display:inline-block;">Provide at least one option label. Empty fields are skipped.</span>
        </div>
    </div>
</form>

<script>
    // Toggle options panel visibility
    function toggleOptionsPanel(val) {
        const panel = document.getElementById('options-panel');
        if (val === 'select') {
            panel.style.display = 'block';
            // Add initial option row if empty
            const container = document.getElementById('options-container');
            if (container.children.length === 0) {
                addOptionRow('');
            }
        } else {
            panel.style.display = 'none';
        }
    }

    // Toggle Attribute Set dropdown visibility (Applicable only to Product entity)
    function toggleAttributeSet(entityType) {
        const group = document.getElementById('attribute-set-group');
        if (entityType === 'product') {
            group.style.display = 'grid';
        } else {
            group.style.display = 'none';
        }
    }

    // Add Option Input fields
    function addOptionRow(valueText) {
        const container = document.getElementById('options-container');
        const row = document.createElement('div');
        row.className = 'option-row';
        row.style.display = 'flex';
        row.style.gap = '10px';
        row.style.alignItems = 'center';
        
        row.innerHTML = `
            <input type="text" name="options[]" value="${valueText}" class="form-control" placeholder="Option label" required>
            <button type="button" class="btn" onclick="this.parentElement.remove()" style="color:var(--color-danger); border-color:var(--color-danger); padding:6px 10px;"><i class="fa-solid fa-trash-can"></i></button>
        `;
        container.appendChild(row);
        
        // Auto-focus new field
        row.querySelector('input').focus();
    }
</script>
<?= $this->endSection() ?>
