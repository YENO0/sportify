<div class="page-header">
    <div class="container d-flex justify-content-between align-items-center">
        <div>
            <h1 class="app-title">Check Venue Availability</h1>
            <p class="text-muted mb-0">Quick check to prevent double-booking.</p>
        </div>
        <div>
            <a class="btn btn-outline-secondary" href="/venues">Back</a>
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="card">
        <div class="card-body">
            <form id="availForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Venue</label>
                        <select id="facilitySelect" class="form-select">
                            <?php foreach($facilities as $f): ?>
                                <option value="<?= $f->id ?>"><?=htmlspecialchars($f->name)?> (<?=htmlspecialchars($f->venue_id)?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start</label>
                        <input id="start" type="datetime-local" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End</label>
                        <input id="end" type="datetime-local" class="form-control">
                    </div>
                    <div class="col-md-2">
                        <button id="checkBtn" class="btn btn-primary">Check</button>
                    </div>
                </div>
            </form>

            <div id="availResult" class="mt-3"></div>
        </div>
    </div>
    <div class="mt-4">
        <h5>Timetable (approved bookings)</h5>
        <div id="calendar"></div>
    </div>
</div>

<!-- FullCalendar CDN -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.css' rel='stylesheet' />
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/main.min.js'></script>

<script>
document.getElementById('availForm').addEventListener('submit', function(e){
    e.preventDefault();
    const facility_id = document.getElementById('facilitySelect').value;
    const start = document.getElementById('start').value;
    const end = document.getElementById('end').value;
    if (!start || !end) { alert('Pick start and end'); return; }
    fetch('/venues/availability/check', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-Requested-With':'XMLHttpRequest'},
        body: JSON.stringify({facility_id, start, end})
    }).then(r=>r.json()).then(data=>{
        const el = document.getElementById('availResult');
        if (data.available) el.innerHTML = '<div class="alert alert-success">Available</div>';
        else el.innerHTML = '<div class="alert alert-danger">Not available — conflicts with existing approved booking.</div>';
    }).catch(err=>{ alert('Error checking availability'); console.error(err); });
});

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        events: '/venues/events.json'
    });
    calendar.render();
});
</script>
