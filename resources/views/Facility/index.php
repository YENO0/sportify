<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="app-title">TARUMT — Venues</h1>
            <p class="text-muted mb-0">Manage venue list for TARUMT sport club.</p>
        </div>
        <div>
            <a class="btn btn-outline-secondary" href="/">Home</a>
            <a class="btn btn-primary" href="/venues/create">Add Venue</a>
            <a class="btn btn-outline-info" href="/venues/timetable">Timetable</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <?php $success = $success ?? (isset($_SESSION['success']) ? $_SESSION['success'] : null); ?>
    <?php if(!empty($success)): ?>
        <div class="alert alert-success"><?=htmlspecialchars($success)?></div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Venue ID</th>
                        <th>Name</th>
                        <th>Capacity</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($facilities as $f): ?>
                        <tr>
                            <td><?=htmlspecialchars($f->venue_id)?></td>
                            <td><?=htmlspecialchars($f->name)?></td>
                            <td><?=htmlspecialchars(($f->capacity_min ?: '–') . ( $f->capacity_max ? ' – '.$f->capacity_max : '') )?></td>
                            <td><?=htmlspecialchars($f->status)?></td>
                            <td>
                                <a class="btn btn-sm btn-outline-primary" href="/venues/<?= $f->id ?>">View</a>
                                <a class="btn btn-sm btn-outline-secondary" href="/venues/<?= $f->id ?>/edit">Edit</a>
                                <form method="post" action="/venues/<?= $f->id ?>/delete" style="display:inline" onsubmit="return confirm('Delete this venue?');">
                                    <button class="btn btn-sm btn-danger" type="submit">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
