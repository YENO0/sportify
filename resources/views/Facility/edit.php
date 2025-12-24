<?php $old = $old ?? []; $errors = $errors ?? []; ?>
<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="app-title">Edit Venue — <?=htmlspecialchars($facility->name)?></h1>
        </div>
        <div>
            <a class="btn btn-outline-secondary" href="/venues">Back</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <form method="post" action="/venues/<?= $facility->id ?>/update" enctype="multipart/form-data">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Venue ID</label>
                        <input name="venue_id" type="text" class="form-control" value="<?=htmlspecialchars($old['venue_id'] ?? $facility->venue_id)?>">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label">Name</label>
                        <input name="name" type="text" class="form-control" value="<?=htmlspecialchars($old['name'] ?? $facility->name)?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Capacity (min)</label>
                        <input name="capacity_min" type="number" class="form-control" value="<?=htmlspecialchars($old['capacity_min'] ?? $facility->capacity_min)?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Capacity (max)</label>
                        <input name="capacity_max" type="number" class="form-control" value="<?=htmlspecialchars($old['capacity_max'] ?? $facility->capacity_max)?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"><?=htmlspecialchars($old['description'] ?? $facility->description)?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Upload Image</label>
                        <input name="image" class="form-control" type="file" accept="image/*">
                        <?php if ($facility->image_path): ?>
                            <div class="mt-2"><img src="/storage/<?=htmlspecialchars($facility->image_path)?>" style="max-width:220px" alt=""></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active"<?= $facility->status === 'active' ? ' selected' : '' ?>>Active</option>
                            <option value="inactive"<?= $facility->status === 'inactive' ? ' selected' : '' ?>>Inactive</option>
                        </select>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="/venues" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
