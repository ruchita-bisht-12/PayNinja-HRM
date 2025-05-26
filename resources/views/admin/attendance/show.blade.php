@extends('layouts.app')

@section('title', 'Attendance Settings - View')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Attendance Settings</h5>
                    <a href="{{ route('admin.attendance.settings') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-edit me-1"></i> Edit Settings
                    </a>
                </div>
                <div class="card-body">
                    @if($settings)
                        <div class="row">
                            <!-- Office Timings -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Office Timings</h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Office Start Time:</dt>
                                            <dd class="col-sm-7">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $settings->office_start_time)->format('h:i A') }}
                                            </dd>
                                            
                                            <dt class="col-sm-5">Office End Time:</dt>
                                            <dd class="col-sm-7">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $settings->office_end_time)->format('h:i A') }}
                                            </dd>
                                            
                                            <dt class="col-sm-5">Work Hours (per day):</dt>
                                            <dd class="col-sm-7">{{ $settings->work_hours }} hours</dd>
                                            
                                            <dt class="col-sm-5">Grace Period:</dt>
                                            <dd class="col-sm-7">
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $settings->grace_period)->format('i') }} minutes
                                            </dd>
                                            
                                            <dt class="col-sm-5">Auto Mark Absent After:</dt>
                                            <dd class="col-sm-7">
                                                @if($settings->auto_absent_time)
                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $settings->auto_absent_time)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">Disabled</span>
                                                @endif
                                            </dd>
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Location Settings -->
                            <div class="col-md-6">
                                <div class="card mb-4">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Location Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Geolocation Enabled:</dt>
                                            <dd class="col-sm-7">
                                                @if($settings->enable_geolocation)
                                                    <span class="badge bg-success">Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary">Disabled</span>
                                                @endif
                                            </dd>
                                            
                                            @if($settings->enable_geolocation && $settings->office_latitude && $settings->office_longitude)
                                                <dt class="col-sm-5">Office Location:</dt>
                                                <dd class="col-sm-7">
                                                    {{ $settings->office_latitude }}, {{ $settings->office_longitude }}
                                                </dd>
                                                
                                                <dt class="col-sm-5">Geofence Radius:</dt>
                                                <dd class="col-sm-7">
                                                    {{ $settings->geofence_radius }} meters
                                                </dd>
                                                
                                                <div class="mt-3">
                                                    <div id="map" style="height: 200px; width: 100%;"></div>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Additional Settings -->
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Additional Settings</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" disabled 
                                                           {{ $settings->allow_multiple_check_in ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        Allow Multiple Check-ins
                                                    </label>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-4">
                                                <div class="form-check form-switch mb-3">
                                                    <input class="form-check-input" type="checkbox" disabled
                                                           {{ $settings->track_location ? 'checked' : '' }}>
                                                    <label class="form-check-label">
                                                        Track Location History
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        @if(!empty($settings->weekend_days) && is_array($settings->weekend_days))
                                            <div class="mt-3">
                                                <h6>Weekend Days:</h6>
                                                <div class="d-flex flex-wrap gap-2">
                                                    @php
                                                        $days = [
                                                            'sunday' => 'Sunday',
                                                            'monday' => 'Monday',
                                                            'tuesday' => 'Tuesday',
                                                            'wednesday' => 'Wednesday',
                                                            'thursday' => 'Thursday',
                                                            'friday' => 'Friday',
                                                            'saturday' => 'Saturday'
                                                        ];
                                                    @endphp
                                                    @foreach($days as $key => $day)
                                                        <span class="badge {{ in_array($key, $settings->weekend_days) ? 'bg-secondary' : 'bg-light text-dark' }}">
                                                            {{ $day }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i> No attendance settings found. 
                            <a href="{{ route('admin.attendance.settings') }}" class="alert-link">
                                Click here to configure settings
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($settings && $settings->enable_geolocation && $settings->office_latitude && $settings->office_longitude)
    @push('google-maps')
        <x-google-maps />
    @endpush
    
    @push('scripts')
    <script>
        // This function will be called when Google Maps API is loaded
        function initMap() {
            try {
                const officeLocation = { 
                    lat: parseFloat({{ $settings->office_latitude }}), 
                    lng: parseFloat({{ $settings->office_longitude }})
                };
                
                // Only initialize the map if the element exists
                const mapElement = document.getElementById('map');
                if (!mapElement) return;
                
                const map = new google.maps.Map(mapElement, {
                    zoom: 15,
                    center: officeLocation,
                    mapTypeId: 'roadmap',
                    streetViewControl: false,
                    fullscreenControl: true
                });
                
                // Add a marker at the office location
                new google.maps.Marker({
                    position: officeLocation,
                    map: map,
                    title: 'Office Location'
                });
                
                // Add a circle for the geofence radius
                new google.maps.Circle({
                    strokeColor: '#4285F4',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#4285F4',
                    fillOpacity: 0.2,
                    map: map,
                    center: officeLocation,
                    radius: {{ $settings->geofence_radius ?? 100 }}
                });
                
                // Handle window resize
                google.maps.event.addDomListener(window, 'resize', function() {
                    const center = map.getCenter();
                    google.maps.event.trigger(map, 'resize');
                    map.setCenter(center);
                });
                
            } catch (error) {
                console.error('Error initializing map:', error);
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    mapElement.innerHTML = `
                        <div class="alert alert-danger m-3">
                            Error loading map: ${error.message}
                        </div>`;
                }
            }
        }
        
        // Initialize map when Google Maps API is loaded
        if (window.google && window.google.maps) {
            initMap();
        } else if (window.googleMapsLoaded) {
            initMap();
        } else {
            window.addEventListener('google-maps-loaded', initMap);
            window.addEventListener('google-maps-error', function() {
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    mapElement.innerHTML = `
                        <div class="alert alert-danger m-3">
                            Failed to load Google Maps. Please try again later.
                        </div>`;
                }
            });
        }
    </script>
    @endpush
@endif

@endsection
