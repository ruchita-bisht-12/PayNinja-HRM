@extends('layouts.app')

@section('title', 'Attendance Settings')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">
                            <i class="fas fa-clock me-2"></i>Attendance Settings
                        </h5>
                        <div>
                            <a href="{{ route('admin.attendance.settings.view') }}" class="btn btn-outline-primary btn-sm me-2">
                                <i class="fas fa-eye me-1"></i> View Current Settings
                            </a>
                            <button type="submit" form="attendance-settings-form" class="btn btn-primary btn-sm">
                                <i class="fas fa-save me-1"></i> Save Settings
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.attendance.settings.update') }}" method="POST" id="attendance-settings-form" enctype="multipart/form-data" onsubmit="event.preventDefault(); submitForm(this);">
                        @csrf
                        
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label fw-medium">Company</label>
                                    <input type="text" class="form-control bg-light" value="{{ $company->name }}" readonly>
                                    <input type="hidden" name="company_id" value="{{ $company->id }}">
                                    <small class="text-muted">You can only manage settings for your company</small>
                                </div>
                            </div>
                        </div>

                        <!-- Office Hours Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Office Hours</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="office_start_time" class="form-label">Office Start Time</label>
                                            @php
                                                // Get the saved value from database
                                                $officeStart = old('office_start_time', $settings->office_start_time ?? null);
                                                // Convert to H:i format if value exists
                                                if ($officeStart && strpos($officeStart, ':') !== false) {
                                                    $officeStart = \Carbon\Carbon::createFromFormat('H:i:s', $officeStart)->format('H:i');
                                                }
                                            @endphp
                                            <input type="time" class="form-control" id="office_start_time" 
                                                   name="office_start_time" step="300"
                                                   value="{{ $officeStart }}" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="office_end_time" class="form-label">Office End Time</label>
                                            @php
                                                // Get the saved value from database
                                                $officeEnd = old('office_end_time', $settings->office_end_time ?? null);
                                                // Convert to H:i format if value exists
                                                if ($officeEnd && strpos($officeEnd, ':') !== false) {
                                                    $officeEnd = \Carbon\Carbon::createFromFormat('H:i:s', $officeEnd)->format('H:i');
                                                }
                                            @endphp
                                            <input type="time" class="form-control" id="office_end_time" 
                                                   name="office_end_time" step="300"
                                                   value="{{ $officeEnd }}" required>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="work_hours" class="form-label fw-medium">Work Hours (per day)</label>
                                            <div class="input-group">
                                                <input type="number" class="form-control" id="work_hours" 
                                                       name="work_hours" min="1" max="24" step="0.5"
                                                       value="{{ old('work_hours', $settings ? $settings->work_hours : 8) }}" required>
                                                <span class="input-group-text">hours</span>
                                            </div>
                                            <small class="text-muted">Standard working hours per day</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="grace_period" class="form-label fw-medium">Grace Period</label>
                                            @php
                                                $gracePeriod = old('grace_period', $settings->grace_period ?? '00:15:00');
                                                try {
                                                    if ($gracePeriod) {
                                                        // Handle both 'H:i' and 'H:i:s' formats
                                                        $gracePeriod = \Carbon\Carbon::createFromFormat('H:i:s', $gracePeriod)->format('H:i');
                                                    } else {
                                                        $gracePeriod = '00:15';
                                                    }
                                                } catch (\Exception $e) {
                                                    $gracePeriod = '00:15';
                                                }
                                            @endphp
                                            <input type="time" class="form-control" id="grace_period" 
                                                   name="grace_period" step="300"
                                                   value="{{ $gracePeriod }}" required>
                                            <small class="text-muted">Allowed late arrival time (e.g., 00:15)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="auto_absent_time" class="form-label">Auto Absent Time</label>
                                            @php
                                                // Get the saved value from database
                                                $autoAbsentTime = old('auto_absent_time', $settings->auto_absent_time ?? '18:00');
                                                // Convert to H:i format if value exists
                                                if ($autoAbsentTime && strpos($autoAbsentTime, ':') !== false) {
                                                    $autoAbsentTime = \Carbon\Carbon::createFromFormat('H:i:s', $autoAbsentTime)->format('H:i');
                                                }
                                            @endphp
                                            <input type="time" class="form-control" id="auto_absent_time" 
                                                   name="auto_absent_time" step="300"
                                                   value="{{ $autoAbsentTime }}" required>
                                            <small class="form-text text-muted">Time after which employees will be marked as absent if not checked in (e.g., 11:00)</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Office Hours Section -->

                        <!-- Attendance Settings Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user-clock me-2"></i>Attendance Settings</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="allow_multiple_check_in" 
                                                   name="allow_multiple_check_in" value="1" 
                                                   {{ old('allow_multiple_check_in', $settings->allow_multiple_check_in ?? false) ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="allow_multiple_check_in">
                                                Allow Multiple Check-ins
                                            </label>
                                            <div class="ms-4 mt-1">
                                                <small class="text-muted">Employees can check in/out multiple times per day</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check form-switch mb-3">
                                            <input class="form-check-input" type="checkbox" id="track_location" 
                                                   name="track_location" value="1"
                                                   {{ isset($settings) && $settings->track_location ? 'checked' : '' }}>
                                            <label class="form-check-label fw-medium" for="track_location">
                                                Track Employee Location
                                            </label>
                                            <div class="ms-4 mt-1">
                                                <small class="text-muted">Record GPS coordinates during check-in/out</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Attendance Settings Section -->

                        <!-- Geolocation Settings Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Geolocation Settings</h6>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_geolocation" 
                                           name="enable_geolocation" value="1"
                                           {{ isset($settings) && $settings->enable_geolocation ? 'checked' : '' }}>
                                    <label class="form-check-label fw-medium" for="enable_geolocation">
                                        Enable Location Restriction
                                    </label>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Restrict attendance marking to specific location and radius
                                </div>
                                <div id="geolocation-fields" style="display: {{ isset($settings) && $settings->enable_geolocation ? 'block' : 'none' }};">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="office_latitude" class="form-label">Office Latitude</label>
                                                <input type="number" step="0.000001" class="form-control" id="office_latitude" 
                                                       name="office_latitude" value="{{ $settings->office_latitude ?? '' }}"
                                                       {{ isset($settings) && $settings->enable_geolocation ? 'required' : '' }}>
                                                <small class="text-muted">e.g., 28.6139</small>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label for="office_longitude" class="form-label">Office Longitude</label>
                                                <input type="number" step="0.000001" class="form-control" id="office_longitude" 
                                                       name="office_longitude" value="{{ $settings->office_longitude ?? '' }}"
                                                       {{ isset($settings) && $settings->enable_geolocation ? 'required' : '' }}>
                                                <small class="text-muted">e.g., 77.2090</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="form-group">
                                                <label for="geofence_radius" class="form-label">Allowed Radius (meters)</label>
                                                <input type="range" class="form-range" id="geofence_radius" 
                                                       name="geofence_radius" min="50" max="1000" step="10" 
                                                       value="{{ $settings->geofence_radius ?? 100 }}"
                                                       {{ isset($settings) && $settings->enable_geolocation ? 'required' : '' }}>
                                                <div class="d-flex justify-content-between">
                                                    <small>50m</small>
                                                    <span id="radius-value">{{ $settings->geofence_radius ?? 100 }}m</span>
                                                    <small>1000m</small>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4 d-flex align-items-center">
                                            <button type="button" class="btn btn-outline-primary btn-sm mt-3" id="get-location">
                                                <i class="fas fa-map-marker-alt me-1"></i> Use Current Location
                                            </button>
                                        </div>
                                    </div>
                                   
                                    
                                    <!-- Map Container -->
                                    <div class="mb-3">
                                        <div id="map" style="height: 300px; width: 100%; border: 1px solid #dee2e6; border-radius: 4px;"></div>
                                        <div class="row mt-2">
                                            <div class="col-md-6">
                                                <label class="form-label">Latitude</label>
                                                <input type="text" class="form-control" id="office_latitude" name="office_latitude" 
                                                       value="{{ $settings->office_latitude ?? '' }}" readonly>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Longitude</label>
                                                <input type="text" class="form-control" id="office_longitude" name="office_longitude" 
                                                       value="{{ $settings->office_longitude ?? '' }}" readonly>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End Geolocation Settings Section -->

                        <!-- Weekend Settings Section -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-calendar-week me-2"></i>Weekend Configuration</h6>
                            </div>
                            <div class="card-body">
                                <p class="text-muted mb-3">Select the weekend days for your organization:</p>
                                <div class="row">
                                    @php
                                        $weekendDays = [];
                                        if (isset($settings) && !empty($settings->weekend_days)) {
                                            if (is_string($settings->weekend_days)) {
                                                $weekendDays = json_decode($settings->weekend_days, true) ?: [];
                                            } elseif (is_array($settings->weekend_days)) {
                                                $weekendDays = $settings->weekend_days;
                                            }
                                        } else {
                                            $weekendDays = old('weekend_days', ['Saturday', 'Sunday']);
                                        }
                                        $weekendDays = is_array($weekendDays) ? $weekendDays : [];
                                    @endphp
                                    @foreach(['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'] as $day)
                                        <div class="col-md-3 col-6 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input weekend-day" 
                                                       type="checkbox" 
                                                       name="weekend_days[]" 
                                                       value="{{ $day }}" 
                                                       id="weekend_{{ strtolower($day) }}"
                                                       {{ in_array($day, $weekendDays) ? 'checked' : '' }}>
                                                <label class="form-check-label" for="weekend_{{ strtolower($day) }}">
                                                    {{ $day }}
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- End Weekend Settings Section -->

                        <!-- Form Actions -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="reset" class="btn btn-outline-secondary me-2">
                                <i class="fas fa-undo me-1"></i> Reset
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save All Settings
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    #map {
        border-radius: 4px;
        min-height: 300px;
    }
    .map-container {
        position: relative;
    }
</style>
@endpush

@push('scripts')
<!-- jQuery Validation Plugin -->
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.3/dist/jquery.validate.min.js"></script>

<script>
    let myMap, marker, circle;
    
    const getLocationBtn = document.getElementById('get-location');
    const latitudeInput = document.getElementById('office_latitude');
    const longitudeInput = document.getElementById('office_longitude');

    const olaMaps = new OlaMaps({
        apiKey: "{{ config('services.krutrim.maps_api_key') }}"
    });

    const defaultLocation = [
        parseFloat('{{ $settings->office_longitude ?? 77.22378292484578 }}'),
        parseFloat('{{ $settings->office_latitude ?? 28.63453194502196 }}')
    ];

    function setLocation(defaultLocation){
        myMap = olaMaps.init({
            style: "https://api.olamaps.io/tiles/vector/v1/styles/default-light-standard/style.json",
            container: 'map',
            center: defaultLocation,
            zoom: 15
        });

        // Add marker after map is loaded
        myMap.on('load', () => {
            marker = olaMaps
                .addMarker({ offset: [0, -15], anchor: 'bottom', color: 'red', draggable: true })
                .setLngLat(defaultLocation)
                .addTo(myMap);

            // Attach the drag event
            marker.on('drag', () => {
                const lngLat = marker.getLngLat();
                latitudeInput.value = lngLat.lat;
                longitudeInput.value = lngLat.lng;
            });
        });
    }
    setLocation(defaultLocation);
    
    if (getLocationBtn) {
        getLocationBtn.addEventListener('click', function() {
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }

            const button = this;
            const originalText = button.innerHTML;
            
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Getting location...';

            navigator.geolocation.getCurrentPosition(
                function(position) {
                    // Success callback
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    
                    if (latitudeInput) latitudeInput.value = lat;
                    if (longitudeInput) longitudeInput.value = lng;
                    
                    // Update map if it's already loaded
                    if (marker && myMap) {
                        const newPosition = [parseFloat(lng), parseFloat(lat)];
                        setLocation(newPosition);
                    }
                    
                    button.disabled = false;
                    button.innerHTML = originalText;
                },
                function(error) {
                    // Error callback
                    console.error('Error getting location:', error);
                    alert('Error getting your location. Please make sure location services are enabled.');
                    button.disabled = false;
                    button.innerHTML = originalText;
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }
</script>


<script>
    // // Global variables
    // let map, marker, circle;
    
    // // Make initMap available globally
    // window.initMap = async function() {
    //     try {
    //         // Request needed libraries
    //         const { Map } = await google.maps.importLibrary('maps');
    //         const { AdvancedMarkerElement } = await google.maps.importLibrary('marker');
            
    //         // Default to New Delhi if no location is set
    //         const defaultLocation = { 
    //             lat: parseFloat('{{ $settings->office_latitude ?? 28.6139 }}'), 
    //             lng: parseFloat('{{ $settings->office_longitude ?? 77.2090 }}')
    //         };
            
    //         const geofenceRadius = parseInt('{{ $settings->geofence_radius ?? 100 }}');
            
    //         // Initialize map
    //         map = new Map(document.getElementById('map'), {
    //             zoom: 15,
    //             center: defaultLocation,
    //             mapId: 'attendance_map',
    //             mapTypeId: 'roadmap',
    //             streetViewControl: false,
    //             fullscreenControl: true
    //         });
            
    //         // Add advanced marker
    //         marker = new AdvancedMarkerElement({
    //             map: map,
    //             position: defaultLocation,
    //             gmpDraggable: true,
    //             title: 'Office Location'
    //         });
            
    //         // Initialize map features
    //         initializeMapFeatures(map, marker, defaultLocation, geofenceRadius);
            
    //     } catch (error) {
    //         console.error('Error initializing map:', error);
    //         const mapElement = document.getElementById('map');
    //         if (mapElement) {
    //             mapElement.innerHTML = 
    //                 '<div class="alert alert-danger m-3">Error initializing Google Maps. Please check your API key and console for details.</div>';
    //         }
    //     }
    // };
    
    // // Function to initialize map features
    // function initializeMapFeatures(map, marker, defaultLocation, initialRadius) {
    //     let geofenceRadius = initialRadius;
        
    //     // Create circle for geofence
    //     function updateCircle(center) {
    //         if (circle) {
    //             circle.setMap(null);
    //         }
            
    //         circle = new google.maps.Circle({
    //             strokeColor: '#4285F4',
    //             strokeOpacity: 0.8,
    //             strokeWeight: 2,
    //             fillColor: '#4285F4',
    //             fillOpacity: 0.2,
    //             map: map,
    //             center: center,
    //             radius: geofenceRadius
    //         });
            
    //         // Update the radius display
    //         document.getElementById('radius-value').textContent = geofenceRadius + 'm';
    //     }
        
    //     // Update form fields with current position
    //     function updateFormFields(position) {
    //         const lat = position.lat ? position.lat : (position.latLng ? position.latLng.lat() : null);
    //         const lng = position.lng ? position.lng : (position.latLng ? position.latLng.lng() : null);
            
    //         if (lat !== null && lng !== null) {
    //             document.getElementById('office_latitude').value = parseFloat(lat).toFixed(6);
    //             document.getElementById('office_longitude').value = parseFloat(lng).toFixed(6);
    //         }
    //     }
        
    //     // Update form fields when marker is dragged
    //     marker.addListener('dragend', (event) => {
    //         updateFormFields(event);
    //         updateCircle(marker.position);
    //     });
        
    //     // Update circle when radius changes
    //     const radiusInput = document.getElementById('geofence_radius');
    //     if (radiusInput) {
    //         radiusInput.addEventListener('input', () => {
    //             geofenceRadius = parseInt(radiusInput.value);
    //             updateCircle(marker.position);
    //         });
    //     }
        
    //     // Initialize form fields and circle
    //     updateFormFields(defaultLocation);
    //     updateCircle(defaultLocation);
        
    //     // Handle map click to move marker
    //     map.addListener('click', (event) => {
    //         const position = {
    //             lat: event.latLng.lat(),
    //             lng: event.latLng.lng()
    //         };
    //         marker.position = position;
    //         updateFormFields(position);
    //         updateCircle(position);
    //     });
        
    //     // Handle window resize
    //     window.addEventListener('resize', () => {
    //         google.maps.event.trigger(map, 'resize');
    //         if (marker && marker.position) {
    //             map.setCenter(marker.position);
    //         }
    //     });
    // }
    
    // // Handle Google Maps API errors
    // window.gm_authFailure = function() {
    //     const mapElement = document.getElementById('map');
    //     if (mapElement) {
    //         mapElement.innerHTML = 
    //             '<div class="alert alert-danger m-3">' +
    //             'Error loading Google Maps. Please check your API key in the .env file.' +
    //             '</div>';
    //     }
    // };
    
    // // Load Google Maps API
    // function loadGoogleMaps() {
    //     // Check if Google Maps API is already loaded
    //     if (typeof google === 'object' && typeof google.maps === 'object') {
    //         if (typeof initMap === 'function') {
    //             initMap();
    //         }
    //         return;
    //     }
        
    //     // Load the Google Maps API
    //     const script = document.createElement('script');
    //     script.src = `https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&loading=async&libraries=marker&callback=initMap`;
    //     script.async = true;
    //     script.defer = true;
    //     script.onerror = function() {
    //         window.gm_authFailure();
    //     };
    //     document.head.appendChild(script);
    // }
    
    // // Initialize the map when the page loads
    // document.addEventListener('DOMContentLoaded', function() {
    //     loadGoogleMaps();
        
    //     // Existing geolocation toggle and other event listeners
    //     const enableGeolocation = document.getElementById('enable_geolocation');
    //     const geolocationFields = document.getElementById('geolocation-fields');
    //     const getLocationBtn = document.getElementById('get-location');
    //     const latitudeInput = document.getElementById('office_latitude');
    //     const longitudeInput = document.getElementById('office_longitude');
    //     const radiusInput = document.getElementById('geofence_radius');
    //     const radiusValue = document.getElementById('radius-value');

    //     // Toggle geolocation fields
    //     if (enableGeolocation && geolocationFields) {
    //         enableGeolocation.addEventListener('change', function() {
    //             if (this.checked) {
    //                 geolocationFields.style.display = 'block';
    //                 // Make fields required when enabled
    //                 if (latitudeInput) latitudeInput.required = true;
    //                 if (longitudeInput) longitudeInput.required = true;
    //                 if (radiusInput) radiusInput.required = true;
    //             } else {
    //                 geolocationFields.style.display = 'none';
    //                 // Remove required when disabled
    //                 if (latitudeInput) latitudeInput.required = false;
    //                 if (longitudeInput) longitudeInput.required = false;
    //                 if (radiusInput) radiusInput.required = false;
    //             }
    //         });
    //     }

    //     // Update radius value display
    //     if (radiusInput && radiusValue) {
    //         radiusValue.textContent = radiusInput.value + 'm';
    //         radiusInput.addEventListener('input', function() {
    //             radiusValue.textContent = this.value + 'm';
    //         });
    //     }

    //     // Get current location
    //     if (getLocationBtn) {
    //         getLocationBtn.addEventListener('click', function() {
    //             if (!navigator.geolocation) {
    //                 alert('Geolocation is not supported by your browser');
    //                 return;
    //             }

    //             const button = this;
    //             const originalText = button.innerHTML;
                
    //             button.disabled = true;
    //             button.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Getting location...';

    //             navigator.geolocation.getCurrentPosition(
    //                 function(position) {
    //                     // Success callback
    //                     const lat = position.coords.latitude.toFixed(6);
    //                     const lng = position.coords.longitude.toFixed(6);
                        
    //                     if (latitudeInput) latitudeInput.value = lat;
    //                     if (longitudeInput) longitudeInput.value = lng;
                        
    //                     // Update map if it's already loaded
    //                     if (marker && map) {
    //                         const newPosition = { lat: parseFloat(lat), lng: parseFloat(lng) };
    //                         marker.position = newPosition;
    //                         map.setCenter(newPosition);
    //                         if (circle) {
    //                             circle.setCenter(newPosition);
    //                         }
    //                     }
                        
    //                     button.disabled = false;
    //                     button.innerHTML = originalText;
    //                 },
    //                 function(error) {
    //                     // Error callback
    //                     console.error('Error getting location:', error);
    //                     alert('Error getting your location. Please make sure location services are enabled.');
    //                     button.disabled = false;
    //                     button.innerHTML = originalText;
    //                 },
    //                 {
    //                     enableHighAccuracy: true,
    //                     timeout: 10000,
    //                     maximumAge: 0
    //                 }
    //             );
    //         });
    //     }
    // });
    
    // // Handle form submission
    // function submitForm(form) {
    //     // Your existing form submission logic here
    //     form.submit();
    // }
</script>
    // Geolocation functionality is now handled in the main DOMContentLoaded event
</script>

<script>
    // Handle form submission with AJAX
    // function submitForm(form) {
    //     // Get the form data
    //     const formData = new FormData(form);
        
    //     // Add weekend days as JSON string
    //     const weekendDays = [];
    //     document.querySelectorAll('input[name="weekend_days[]"]:checked').forEach(checkbox => {
    //         weekendDays.push(checkbox.value);
    //     });
    //     formData.append('weekend_days_json', JSON.stringify(weekendDays));
        
    //     // Show loading state
    //     const submitBtn = form.querySelector('button[type="submit"]');
    //     const originalBtnText = submitBtn.innerHTML;
    //     submitBtn.disabled = true;
    //     submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Saving...';
        
    //     // Submit form via AJAX
    //     fetch(form.action, {
    //         method: 'POST',
    //         body: formData,
    //         headers: {
    //             'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    //             'X-Requested-With': 'XMLHttpRequest'
    //         }
    //     })
    //     .then(response => response.json())
    //     .then(data => {
    //         if (data.success) {
    //             // Show success message
    //             Swal.fire({
    //                 icon: 'success',
    //                 title: 'Success!',
    //                 text: data.message || 'Settings saved successfully',
    //                 timer: 2000,
    //                 showConfirmButton: false
    //             });
                
    //             // Reload page to reflect changes
    //             setTimeout(() => {
    //                 window.location.reload();
    //             }, 2000);
    //         } else {
    //             throw new Error(data.message || 'Failed to save settings');
    //         }
    //     })
    //     .catch(error => {
    //         console.error('Error:', error);
    //         Swal.fire({
    //             icon: 'error',
    //             title: 'Error!',
    //             text: error.message || 'Failed to save settings. Please try again.'
    //         });
    //     })
    //     .finally(() => {
    //         submitBtn.disabled = false;
    //         submitBtn.innerHTML = originalBtnText;
    //     });
    // }
    
    // // Initialize time pickers and validation when document is ready
    // document.addEventListener('DOMContentLoaded', function() {
    //     // Initialize time pickers with 5-minute steps
    //     $('input[type="time"]').attr('step', 300);
        
    //     // Helper function to convert time string to minutes
    //     function convertToMinutes(timeString) {
    //         if (!timeString) return 0;
    //         const [hours, minutes] = timeString.split(':').map(Number);
    //         return hours * 60 + minutes;
    //     }
        
    //     // Custom validation rule for end time
    //     $.validator.addMethod('greaterThan', function(value, element, param) {
    //         if (!value) return true;
    //         const startTime = $(param).val();
    //         if (!startTime) return true;
            
    //         // Convert times to minutes for comparison
    //         const startMinutes = convertToMinutes(startTime);
    //         const endMinutes = convertToMinutes(value);
            
    //         return endMinutes > startMinutes;
    //     }, 'End time must be after start time');
        
    //     // Initialize form validation
    //     $('#attendance-settings-form').validate({
    //         rules: {
    //             company_id: 'required',
    //             office_start_time: 'required',
    //             office_end_time: {
    //                 required: true,
    //                 greaterThan: '#office_start_time'
    //             },
    //             work_hours: {
    //                 required: true,
    //                 min: 1,
    //                 max: 24
    //             },
    //             late_minutes: {
    //                 required: true,
    //                 min: 0
    //             },
    //             early_leave_minutes: {
    //                 required: true,
    //                 min: 0
    //             },
    //             half_day_minutes: {
    //                 required: true,
    //                 min: 0
    //             },
    //             auto_absent_time: 'required',
    //             grace_period: 'required',
    //             geofence_radius: {
    //                 required: '#enable_geolocation:checked',
    //                 min: 50,
    //                 max: 5000
    //             }
    //         },
    //         messages: {
    //             office_start_time: 'Please enter office start time',
    //             office_end_time: {
    //                 required: 'Please enter office end time',
    //                 greaterThan: 'End time must be after start time'
    //             },
    //             work_hours: {
    //                 required: 'Please enter work hours',
    //                 min: 'Work hours must be at least 1 hour',
    //                 max: 'Work hours cannot exceed 24 hours'
    //             },
    //             late_minutes: {
    //                 required: 'Please enter late minutes',
    //                 min: 'Late minutes cannot be negative'
    //             },
    //             early_leave_minutes: {
    //                 required: 'Please enter early leave minutes',
    //                 min: 'Early leave minutes cannot be negative'
    //             },
    //             half_day_minutes: {
    //                 required: 'Please enter half day minutes',
    //                 min: 'Half day minutes cannot be negative'
    //             },
    //             auto_absent_time: 'Please enter auto absent time',
    //             grace_period: 'Please enter grace period',
    //             geofence_radius: {
    //                 required: 'Please enter geofence radius',
    //                 min: 'Minimum radius is 50 meters',
    //                 max: 'Maximum radius is 5000 meters'
    //             }
    //         },
    //         errorElement: 'span',
    //         errorPlacement: function (error, element) {
    //             error.addClass('invalid-feedback');
    //             element.closest('.form-group').append(error);
    //         },
    //         highlight: function (element, errorClass, validClass) {
    //             $(element).addClass('is-invalid');
    //         },
    //         unhighlight: function (element, errorClass, validClass) {
    //             $(element).removeClass('is-invalid');
    //         },
    //         submitHandler: function(form) {
    //             // This prevents the default form submission
    //             // The form is submitted via the submitForm function
    //             return false;
    //         }
    //     });
    // });
</script>
@endpush
