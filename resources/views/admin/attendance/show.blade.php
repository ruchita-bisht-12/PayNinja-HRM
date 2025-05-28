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
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h6 class="mb-0">Office Timings</h6>
                                    </div>
                                    <div class="card-body">
                                        <dl class="row mb-0">
                                            <dt class="col-sm-5">Office Start Time:</dt>
                                            <dd class="col-sm-7">
                                                @if($settings->office_start_time)
                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $settings->office_start_time)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </dd>
                                            
                                            <dt class="col-sm-5">Office End Time:</dt>
                                            <dd class="col-sm-7">
                                                @if($settings->office_end_time)
                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $settings->office_end_time)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </dd>
                                            
                                            <dt class="col-sm-5">Grace Period:</dt>
                                            <dd class="col-sm-7">
                                                @if($settings->grace_period)
                                                    {{ \Carbon\Carbon::createFromFormat('H:i:s', $settings->grace_period)->format('h:i A') }}
                                                @else
                                                    <span class="text-muted">Not set</span>
                                                @endif
                                            </dd>
                                            
                                            <dt class="col-sm-5">Work Hours:</dt>
                                            <dd class="col-sm-7">{{ $settings->work_hours ?? 'Not set' }} hours</dd>
                                            
                                            <dt class="col-sm-5">Auto Mark Absent:</dt>
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
                                <div class="card">
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
                            <div class="col-12 mt-4">
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
    @push('scripts')
    <script>
        try {
            // Initialize Ola Maps
            const olaMaps = new OlaMaps({
                apiKey: "{{ config('services.krutrim.maps_api_key') }}"
            });

            // Office location coordinates
            const officeLocation = [
                parseFloat('{{ $settings->office_longitude }}'),
                parseFloat('{{ $settings->office_latitude }}')
            ];

            // Initialize map
            const myMap = olaMaps.init({
                style: "https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard/style.json",
                container: 'map',
                center: officeLocation,
                zoom: 15
            });

            // Add marker and circle after map is loaded
            myMap.on('load', () => {
                // Add office marker
                olaMaps
                    .addMarker({ offset: [0, -15], anchor: 'bottom', color: 'blue' })
                    .setLngLat(officeLocation)
                    .addTo(myMap);
                
                // Add geofence circle
                olaMaps.addCircle({
                    center: officeLocation,
                    radius: {{ $settings->geofence_radius ?? 100 }},
                    fillColor: '#4285F4',
                    fillOpacity: 0.1,
                    strokeColor: '#4285F4',
                    strokeOpacity: 0.8,
                    strokeWidth: 2
                }).addTo(myMap);
            });
        } catch (error) {
            console.error('Error initializing map:', error);
            document.getElementById('map').innerHTML = `
                <div class="alert alert-danger m-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading map. Please try again later.
                </div>
            `;
        }
    </script>
    @endpush
@endif

@push('styles')
<style>
    #map {
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
</style>
@endpush

@endsection
