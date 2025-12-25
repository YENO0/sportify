<div class="page-header">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1 class="app-title">TARUMT — Add Venue</h1>
                <p class="text-muted mb-0">Create new venue entries used by the TARUMT system (examples: CA, DK-A, DK-B).</p>
            </div>
            <div>
                <a class="btn btn-outline-secondary" href="/">Back Home</a>
                <a class="btn btn-outline-primary" href="/venues">All Venues</a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="card form-card">
        <div class="card-body">
            <h2 class="section-title">Venue details</h2>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php foreach ($errors as $e): ?><li><?=htmlspecialchars($e)?></li><?php endforeach; ?></ul></div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
            <?php endif; ?>

            <form method="post" action="/venues/store" enctype="multipart/form-data" id="venueForm" novalidate>
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Venue ID <span class="text-danger">*</span></label>
                        <input name="venue_id" type="text" class="form-control" value="<?=htmlspecialchars($old['venue_id'] ?? '')?>" placeholder="e.g. CA, DK-A, DK-B or TAR-001">
                        <div class="text-muted small">Unique ID used by TARUMT system.</div>
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Venue Name <span class="text-danger">*</span></label>
                        <input name="name" type="text" class="form-control" required value="<?=htmlspecialchars($old['name'] ?? '')?>" placeholder="e.g. Main Hall">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">Capacity (min)</label>
                        <input name="capacity_min" type="number" min="0" class="form-control" value="<?=htmlspecialchars($old['capacity_min'] ?? '')?>">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Capacity (max)</label>
                        <input name="capacity_max" type="number" min="0" class="form-control" value="<?=htmlspecialchars($old['capacity_max'] ?? '')?>">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Short description"><?=htmlspecialchars($old['description'] ?? '')?></textarea>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Upload Image</label>
                        <input name="image" id="imageInput" class="form-control" type="file" accept="image/*">
                        <div class="mt-3 d-flex gap-3 align-items-center">
                            <img id="imgPreview" class="img-preview" src="" alt="">
                            <div class="text-muted small">Supported: jpg, png, gif, webp. Recommended ~1200×800.</div>
                        </div>
                    </div>

                    <div class="col-12 d-flex justify-content-end gap-2">
                        <a href="/venues" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Create Venue</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<script>
    document.getElementById('imageInput').addEventListener('change', function (e) {
        const file = e.target.files[0];
        const img = document.getElementById('imgPreview');
        if (!file) { img.src = ''; return; }
        const reader = new FileReader();
        reader.onload = function(ev) { img.src = ev.target.result; };
        reader.readAsDataURL(file);
    });

    document.getElementById('venueForm').addEventListener('submit', function (e) {
        const vid = document.querySelector('input[name="venue_id"]');
        const name = document.querySelector('input[name="name"]');
        const min = parseInt(document.querySelector('input[name="capacity_min"]').value || 0, 10);
        const max = parseInt(document.querySelector('input[name="capacity_max"]').value || 0, 10);
        if (!vid.value.trim() || !name.value.trim()) {
            e.preventDefault();
            alert('Please fill required fields: Venue ID and Name.');
            return;
        }
        if (min > 0 && max > 0 && min > max) {
            e.preventDefault();
            alert('Capacity min cannot be greater than capacity max.');
            return;
        }
    });
</script>

