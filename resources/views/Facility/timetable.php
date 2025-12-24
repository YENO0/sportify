<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="app-title">Venue Timetable</h1>
            <p class="text-muted mb-0">Overview of upcoming approved bookings (if any).</p>
        </div>
        <div>
            <a class="btn btn-outline-secondary" href="/venues">Back</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <?php if (empty($events)): ?>
                <div class="alert alert-info">No approved bookings found or no events table exists.</div>
            <?php else: ?>
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Facility</th>
                            <th>Event</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($events as $e): ?>
                            <?php
                                $f = null;
                                foreach($facilities as $ff) { if ($ff->id == $e->facility_id) { $f = $ff; break; } }
                            ?>
                            <tr>
                                <td><?=htmlspecialchars($f ? $f->name : 'Facility #'.$e->facility_id)?></td>
                                <td><?=htmlspecialchars($e->title ?? 'Booking')?></td>
                                <td><?=htmlspecialchars($e->start_time)?></td>
                                <td><?=htmlspecialchars($e->end_time)?></td>
                                <td><?=htmlspecialchars($e->status)?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>
