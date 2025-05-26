@if(config('services.google_maps.key'))
    @once
        <script>
            // Global callback function for Google Maps API
            window.initGoogleMaps = function() {
                // This will be called when Google Maps API is loaded
                window.googleMapsLoaded = true;
                
                // Dispatch an event that other scripts can listen for
                const event = new Event('google-maps-loaded');
                window.dispatchEvent(event);
            };
            
            // Load Google Maps API asynchronously with callback
            (function() {
                const script = document.createElement('script');
                const apiKey = '{{ config('services.google_maps.key') }}';
                script.src = `https://maps.googleapis.com/maps/api/js?key=${apiKey}&libraries=places&loading=async&callback=initGoogleMaps`;
                script.async = true;
                script.defer = true;
                script.onerror = function() {
                    console.error('Failed to load Google Maps API');
                    const event = new Event('google-maps-error');
                    window.dispatchEvent(event);
                };
                document.head.appendChild(script);
            })();
        </script>
    @endonce
@else
    <div class="alert alert-warning">
        Google Maps API key is not configured. Please set GOOGLE_MAPS_KEY in your .env file.
    </div>
@endif
