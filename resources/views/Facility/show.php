<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="app-title"><?=htmlspecialchars($facility->name)?> (<?=htmlspecialchars($facility->venue_id)?>)</h1>
        </div>
        <div>
            <a class="btn btn-outline-secondary" href="/venues">Back</a>
            <a class="btn btn-outline-secondary" href="/venues/<?= $facility->id ?>/edit">Edit</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <?php if ($facility->image_path): ?>
                        <img src="/storage/<?=htmlspecialchars($facility->image_path)?>" alt="" class="img-fluid">
                    <?php endif; ?>
                </div>
                <div class="col-md-8">
                    <h4>Details</h4>
                    <p><strong>Venue ID:</strong> <?=htmlspecialchars($facility->venue_id)?></p>
                    <p><strong>Capacity:</strong> <?=htmlspecialchars(($facility->capacity_min ?: '–') . ( $facility->capacity_max ? ' – '.$facility->capacity_max : '') )?></p>
                    <p><strong>Status:</strong> <?=htmlspecialchars($facility->status)?></p>
                    <p><strong>Description:</strong><br><?=nl2br(htmlspecialchars($facility->description))?></p>
                </div>
            </div>
        </div>
    </div>
</div>
