@extends('layouts.app')

@section('title', 'Apply Event')
@section('page-title', '')

@section('nav-links')
@endsection

@push('styles')
<style>
    /* Override layout background and container */
    body {
        background: #ffffff !important;
        padding: 0 !important;
    }

    .container {
        max-width: 100% !important;
        margin: 0 !important;
        background: transparent !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        padding: 0 !important;
    }

    .header {
        display: none !important;
    }

    .create-event-page {
        max-width: 1000px;
        margin: 0 auto;
    }

    .image-upload-section {
        margin-bottom: 40px;
    }

    .image-upload-area {
        position: relative;
        width: 100%;
        min-height: 300px;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        background: #f9fafb;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .image-upload-area:hover {
        border-color: #667eea;
        background: #f3f4f6;
    }

    .image-upload-area.dragover {
        border-color: #667eea;
        background: #eef2ff;
    }

    .image-upload-icon {
        width: 64px;
        height: 64px;
        color: #9ca3af;
        margin-bottom: 16px;
    }

    .image-upload-text {
        font-size: 16px;
        color: #6b7280;
        margin-bottom: 8px;
        text-align: center;
    }

    .image-upload-button {
        padding: 10px 20px;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .image-upload-button:hover {
        background: #f9fafb;
    }

    .image-upload-specs {
        margin-top: 16px;
        font-size: 12px;
        color: #9ca3af;
        text-align: center;
    }

    .image-preview-container {
        position: relative;
        width: 100%;
        min-height: 300px;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 16px;
    }

    .image-preview-container img {
        width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: cover;
        display: block;
    }

    .image-preview-actions {
        display: flex;
        gap: 12px;
        justify-content: center;
    }

    .btn-change-image {
        padding: 8px 16px;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        color: #374151;
        cursor: pointer;
    }


    .form-section {
        background: white;
        border-radius: 8px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid #e5e7eb;
    }

    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-bottom: 24px;
    }

    .subsection {
        margin-bottom: 30px;
    }

    .subsection-title {
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .subsection-description {
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 12px;
        line-height: 1.5;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        font-size: 14px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }

    .form-input {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        transition: border-color 0.2s ease;
    }

    .form-input:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .form-textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        min-height: 100px;
        resize: vertical;
        font-family: inherit;
    }

    .form-textarea:focus {
        outline: none;
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    }

    .textarea-wrapper {
        position: relative;
    }

    .char-counter {
        position: absolute;
        bottom: 12px;
        right: 12px;
        font-size: 12px;
        color: #6b7280;
    }

    .suggest-link {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 8px;
        color: #667eea;
        text-decoration: none;
        font-size: 13px;
    }

    .suggest-link:hover {
        text-decoration: underline;
    }

    .event-type-cards {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 30px;
    }

    .event-type-card {
        position: relative;
        padding: 20px;
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s ease;
        background: white;
    }

    .event-type-card:hover {
        border-color: #d1d5db;
    }

    .event-type-card.selected {
        border-color: #667eea;
        background: #f0f4ff;
    }

    .event-type-card input[type="radio"] {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .event-type-icon {
        width: 24px;
        height: 24px;
        margin-bottom: 12px;
        color: #667eea;
    }

    .event-type-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .event-type-description {
        font-size: 13px;
        color: #6b7280;
    }

    .new-badge {
        position: absolute;
        top: 12px;
        right: 40px;
        background: #667eea;
        color: white;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 600;
    }

    .date-time-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
        margin-bottom: 16px;
    }

    .date-time-grid.has-end-date {
        grid-template-columns: repeat(4, 1fr);
    }

    .date-time-input {
        position: relative;
    }

    .date-time-input input {
        padding-left: 40px;
    }

    .input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #9ca3af;
    }

    .more-options-link {
        color: #667eea;
        text-decoration: none;
        font-size: 13px;
        margin-bottom: 8px;
        display: inline-block;
    }

    .more-options-link:hover {
        text-decoration: underline;
    }

    .timezone-info {
        font-size: 12px;
        color: #6b7280;
    }

    .location-tabs {
        display: flex;
        gap: 0;
        margin-bottom: 20px;
        border-bottom: 1px solid #e5e7eb;
    }

    .location-tab {
        padding: 12px 20px;
        background: none;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        font-size: 14px;
        font-weight: 500;
        color: #6b7280;
        display: flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s ease;
        margin-bottom: -1px;
    }

    .location-tab:hover {
        color: #374151;
    }

    .location-tab.active {
        color: #667eea;
        border-bottom-color: #667eea;
        background: #f0f4ff;
    }

    .location-tab svg {
        width: 18px;
        height: 18px;
    }

    .location-input-wrapper {
        position: relative;
        margin-bottom: 12px;
    }

    .location-input {
        padding-left: 40px;
        padding-right: 40px;
    }

    .location-input-icon {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #9ca3af;
    }

    .error-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #ef4444;
    }

    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-bottom: 12px;
    }

    .save-location-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #667eea;
        text-decoration: none;
        font-size: 13px;
        margin-bottom: 20px;
    }

    .save-location-link:hover {
        text-decoration: underline;
    }

    .map-container {
        width: 100%;
        height: 300px;
        background: #f3f4f6;
        border-radius: 8px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
    }

    .seating-toggle {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px;
        background: #f9fafb;
        border-radius: 8px;
    }

    .seating-toggle-label {
        flex: 1;
    }

    .seating-toggle-title {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 4px;
    }

    .seating-toggle-description {
        font-size: 13px;
        color: #6b7280;
    }

    .toggle-switch {
        position: relative;
        width: 48px;
        height: 24px;
    }

    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #d1d5db;
        transition: 0.3s;
        border-radius: 24px;
    }

    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }

    .toggle-switch input:checked + .toggle-slider {
        background-color: #667eea;
    }

    .toggle-switch input:checked + .toggle-slider:before {
        transform: translateX(24px);
    }

    .form-actions {
        display: flex;
        gap: 12px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn-primary {
        padding: 12px 24px;
        background: #667eea;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }

    .btn-primary:hover {
        background: #5568d3;
    }

    .btn-secondary {
        padding: 12px 24px;
        background: white;
        color: #374151;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        text-decoration: none;
        display: inline-block;
    }

    .btn-secondary:hover {
        background: #f9fafb;
    }
</style>
@endpush

@section('content')
<div class="create-event-page">
    <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Image Upload Section -->
        <div class="image-upload-section">
            <div class="image-upload-area" id="imageUploadArea">
                <svg class="image-upload-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <div class="image-upload-text">Drag and drop an image or</div>
                <button type="button" class="image-upload-button" onclick="event.stopPropagation(); document.getElementById('event_poster_input').click();">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Upload Image
                </button>
                <div class="image-upload-specs">
                    <div>Recommended image size: 2160 x 1080px</div>
                    <div>Maximum file size: 10MB</div>
                    <div>Supported image files: .JPEG, .PNG</div>
                </div>
            </div>
            <div id="imagePreviewContainer" style="display: none;">
                <div class="image-preview-container">
                    <img id="imagePreview" src="" alt="Event poster preview">
                </div>
                <div class="image-preview-actions">
                    <button type="button" class="btn-change-image" onclick="changeImage()">Change Image</button>
                </div>
            </div>
            <input type="file" id="event_poster_input" name="event_poster" accept="image/*" style="display: none;">
        </div>

        <!-- Event Overview Section -->
        <div class="form-section">
            <h2 class="section-title">Event Overview</h2>

            <!-- Event Title -->
            <div class="subsection">
                <div class="subsection-description">
                    Receive and description with a title that tells people what your event is about.
                </div>
                <div class="form-group">
                    <label for="event_name" class="form-label">Event title</label>
                    <input type="text" id="event_name" name="event_name" class="form-input" value="{{ old('event_name') }}" placeholder="Enter event title" required>
                </div>
            </div>

            <!-- Summary -->
            <div class="subsection">
                <div class="subsection-description">
                    Catch people's attention with a short description about your event. Attendees will use this at the top of your event page. [1-60 characters max] See examples
                </div>
                <div class="form-group">
                    <label for="event_description" class="form-label">Summary</label>
                    <div class="textarea-wrapper">
                        <textarea id="event_description" name="event_description" class="form-textarea" maxlength="500" 
                        onkeyup="updateCharCounter(this)" placeholder="Enter description">{{ old('event_description') }}</textarea>
                        <span class="char-counter" id="char-counter">0 / 500</span>
                    </div>
                    <a href="#" class="suggest-link">
                        <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                        </svg>
                        Suggest summary
                    </a>
                </div>
            </div>
        </div>

        <!-- Date Section -->
        <div class="form-section">
            <h2 class="section-title">Date</h2>

            <!-- Type of Event -->
            <div class="subsection">
                <div class="event-type-cards">
                    <label class="event-type-card selected">
                        <input type="radio" name="event_type" value="single" checked onchange="updateEventType(this)">
                        <svg class="event-type-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div class="event-type-title">Single event</div>
                        <div class="event-type-description">For events that happen once</div>
                    </label>
                    <label class="event-type-card">
                        <input type="radio" name="event_type" value="recurring" onchange="updateEventType(this)">
                        <span class="new-badge">New</span>
                        <svg class="event-type-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <div class="event-type-title">Recurring event</div>
                        <div class="event-type-description">For events that happen multiple days</div>
                    </label>
                </div>
            </div>

            <!-- Date and Time -->
            <div class="subsection">
                <div class="date-time-grid" id="date-time-grid">
                    <div class="date-time-input">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <input type="date" id="event_start_date" name="event_start_date" class="form-input" value="{{ old('event_start_date') }}" min="{{ now()->addWeek()->format('Y-m-d') }}" required>
                    </div>
                    <div class="date-time-input" id="end-date-input" style="display: none;">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <input type="date" id="event_end_date" name="event_end_date" class="form-input" value="{{ old('event_end_date') }}" min="{{ now()->addWeek()->format('Y-m-d') }}">
                    </div>
                    <div class="date-time-input">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <input type="time" id="event_start_time" name="event_start_time" class="form-input" value="{{ old('event_start_time', '10:00') }}" required>
                    </div>
                    <div class="date-time-input">
                        <svg class="input-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <input type="time" id="event_end_time" name="event_end_time" class="form-input" value="{{ old('event_end_time', '12:00') }}" required>
                    </div>
                </div>
                <a href="#" class="more-options-link">More options</a>
                <div class="timezone-info">EMT-8, Display start and end times, English (US)</div>
            </div>

            <!-- Registration Due Date -->
            <div class="subsection">
                <div class="form-group">
                    <label for="registration_due_date" class="form-label">Registration due date</label>
                    <input
                        type="date"
                        id="registration_due_date"
                        name="registration_due_date"
                        class="form-input"
                        value="{{ old('registration_due_date') }}"
                        min="{{ now()->format('Y-m-d') }}"
                        placeholder="Select registration due date"
                    >
                    <div class="subsection-description" style="margin-top: 8px;">
                        Must be at least 1 day before the event start date.
                    </div>
                </div>
            </div>

            <!-- Price -->
            <div class="subsection">
                <div class="form-group">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" id="price" name="price" class="form-input" value="{{ old('price', '0') }}" min="0" step="0.01" placeholder="0.00" required>
                    <div class="subsection-description" style="margin-top: 8px;">Enter 0 for free events</div>
                </div>
            </div>

            <!-- Capacity -->
            <div class="subsection">
                <div class="form-group">
                    <label for="max_capacity" class="form-label">Capacity</label>
                    <input type="number" id="max_capacity" name="max_capacity" class="form-input" value="{{ old('max_capacity', '100') }}" min="1" step="1" placeholder="Enter maximum capacity" required>
                    <div class="subsection-description" style="margin-top: 8px;">Maximum number of attendees for this event</div>
                </div>
            </div>

            <!-- Facility Booking -->
            <div class="subsection">
                <div class="form-group">
                    <label for="book_facility_id" class="form-label">Book Facility (Optional)</label>
                    <select id="book_facility_id" name="book_facility_id" class="form-input">
                        <option value="">-- Select a facility (optional) --</option>
                        @foreach($facilities as $facility)
                            <option value="{{ $facility->id }}" {{ old('book_facility_id') == $facility->id ? 'selected' : '' }}>
                                {{ $facility->name }} ({{ $facility->type }})
                            </option>
                        @endforeach
                    </select>
                    <div class="subsection-description" style="margin-top: 8px;">
                        Select a facility to book for this event. The booking will use the event's date and time.
                        <br>
                        <strong>Single Event:</strong> Booking will be on the event date, from start time to end time.
                        <br>
                        <strong>Recurring Event:</strong> Booking will be from event start date/time to event end date/time.
                    </div>
                </div>
            </div>
        </div>

        <!-- Hidden fields for form submission -->
        <input type="hidden" name="committee_id" value="1">

        <div class="form-actions">
            <a href="{{ route('committee.events.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" name="save_as_draft" class="btn-secondary" style="background: #6b7280; color: white;">Save as Draft</button>
            <button type="submit" class="btn-primary">Apply</button>
        </div>
    </form>
</div>


@push('scripts')
<script>
    let selectedFile = null;
    let imageUrl = null;

    function updateCharCounter(textarea) {
        const counter = document.getElementById('char-counter');
        const length = textarea.value.length;
        counter.textContent = length + ' / 500';
    }

    function updateEventType(radio) {
        document.querySelectorAll('.event-type-card').forEach(card => {
            card.classList.remove('selected');
        });
        radio.closest('.event-type-card').classList.add('selected');
        
        // Show/hide end date based on event type
        const endDateInput = document.getElementById('end-date-input');
        const endDateField = document.getElementById('event_end_date');
        const startDateField = document.getElementById('event_start_date');
        const dateTimeGrid = document.getElementById('date-time-grid');
        
        if (radio.value === 'recurring') {
            // Show end date for recurring events
            if (endDateInput) {
                endDateInput.style.display = 'block';
                // Update grid to 4 columns
                dateTimeGrid.classList.add('has-end-date');
                // Set min date to be at least 1 day after start date
                if (startDateField && endDateField) {
                    updateEndDateMin();
                }
            }
        } else {
            // Hide end date for single events
            if (endDateInput) {
                endDateInput.style.display = 'none';
                // Update grid to 3 columns
                dateTimeGrid.classList.remove('has-end-date');
            }
        }
    }
    
    function updateEndDateMin() {
        const startDateField = document.getElementById('event_start_date');
        const endDateField = document.getElementById('event_end_date');
        const eventType = document.querySelector('input[name="event_type"]:checked')?.value;
        
        if (startDateField && endDateField && eventType === 'recurring') {
            const startDate = new Date(startDateField.value);
            if (startDate && !isNaN(startDate.getTime())) {
                // Calculate minimum date: max of (1 week from today, 1 day after start date)
                const oneWeekFromToday = new Date();
                oneWeekFromToday.setDate(oneWeekFromToday.getDate() + 7);
                
                const oneDayAfterStart = new Date(startDate);
                oneDayAfterStart.setDate(oneDayAfterStart.getDate() + 1);
                
                // Use the later date
                const minDate = oneDayAfterStart > oneWeekFromToday ? oneDayAfterStart : oneWeekFromToday;
                endDateField.min = minDate.toISOString().split('T')[0];
                
                // If current end date is before the new min, clear it
                if (endDateField.value && new Date(endDateField.value) < minDate) {
                    endDateField.value = '';
                }
            }
        }
    }
    
    // Update end date min when start date changes (for recurring events)
    document.addEventListener('DOMContentLoaded', function() {
        const startDateField = document.getElementById('event_start_date');
        if (startDateField) {
            startDateField.addEventListener('change', updateEndDateMin);
        }
    });
    
    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        const selectedType = document.querySelector('input[name="event_type"]:checked');
        if (selectedType) {
            updateEventType(selectedType);
        }
    });

    function switchLocationTab(tab) {
        document.querySelectorAll('.location-tab').forEach(btn => {
            btn.classList.remove('active');
        });
        event.target.classList.add('active');
    }

    // Image upload functionality
    const imageUploadArea = document.getElementById('imageUploadArea');
    const fileInput = document.getElementById('event_poster_input');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    // Click to upload - only trigger if clicking directly on the area, not on buttons
    imageUploadArea.addEventListener('click', (e) => {
        // Don't trigger if clicking on the button (button has its own handler)
        if (e.target.closest('.image-upload-button')) {
            return;
        }
        fileInput.click();
    });

    // File input change
    fileInput.addEventListener('change', (e) => {
        if (e.target.files && e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Drag and drop
    imageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUploadArea.classList.add('dragover');
    });

    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('dragover');
    });

    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('dragover');
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('image/')) {
            handleFileSelect(file);
        }
    });

    function handleFileSelect(file) {
        if (!file) return;

        // Validate file type
        if (!file.type.match('image/jpeg') && !file.type.match('image/png')) {
            alert('Please select a JPEG or PNG image.');
            fileInput.value = ''; // Reset input
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('Image size must be less than 10MB.');
            fileInput.value = ''; // Reset input
            return;
        }

        selectedFile = file;
        const reader = new FileReader();

        reader.onload = (e) => {
            imageUrl = e.target.result;
            imagePreview.src = imageUrl;
            imageUploadArea.style.display = 'none';
            imagePreviewContainer.style.display = 'block';
        };

        reader.onerror = () => {
            alert('Error reading file. Please try again.');
            fileInput.value = ''; // Reset input
        };

        reader.readAsDataURL(file);
    }

    function changeImage() {
        // Directly open file explorer instead of removing image first
        fileInput.click();
    }

</script>
@endpush
@endsection
