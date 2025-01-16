// Map Display Area
document.addEventListener("DOMContentLoaded", function () {
    mapboxgl.accessToken = 'pk.eyJ1IjoicmF2aXNoa2ExMDMwIiwiYSI6ImNtMXpiZ29reTA1aG4ybm9vZm80dng2OTQifQ.NCgi3Dwrm4hD43SxL_GKMQ';
    
    // Initialize map
    const map = new mapboxgl.Map({
        container: 'map',
        style: 'mapbox://styles/mapbox/streets-v11',
        center: [80.7718, 7.8731],
        zoom: 8
    });

    // Variables for markers and route layer
    let pickupMarker, dropoffMarker, routeLayer;

    // Function to update markers and route on map
    function updateMarkers() {
        const pickupLocation = document.getElementById("pickupLocation").value;
        const dropoffLocation = document.getElementById("dropoffLocation").value;

        // Geocode locations if both pickup and dropoff are entered
        if (pickupLocation && dropoffLocation) {
            getCoordinates(pickupLocation, pickupCoordinates => {
                if (pickupMarker) pickupMarker.remove();
                pickupMarker = new mapboxgl.Marker({ color: "blue" })
                    .setLngLat(pickupCoordinates)
                    .setPopup(new mapboxgl.Popup().setText("Pickup Location"))
                    .addTo(map);

                getCoordinates(dropoffLocation, dropoffCoordinates => {
                    if (dropoffMarker) dropoffMarker.remove();
                    dropoffMarker = new mapboxgl.Marker({ color: "orange" })
                        .setLngLat(dropoffCoordinates)
                        .setPopup(new mapboxgl.Popup().setText("Drop-off Location"))
                        .addTo(map);

                    // Center the map between the two markers
                    const bounds = new mapboxgl.LngLatBounds();
                    bounds.extend(pickupCoordinates).extend(dropoffCoordinates);
                    map.fitBounds(bounds, { padding: 50 });

                    // Draw route and calculate fare
                    calculateRoute(pickupCoordinates, dropoffCoordinates);
                });
            });
        }
    }

    // Geocode function to get coordinates
    function getCoordinates(location, callback) {
        const url = `https://api.mapbox.com/geocoding/v5/mapbox.places/${encodeURIComponent(location)}.json?access_token=${mapboxgl.accessToken}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                const coordinates = data.features[0].geometry.coordinates;
                callback(coordinates);
            });
    }

    // Function to calculate and draw the route
    function calculateRoute(start, end) {
        const url = `https://api.mapbox.com/directions/v5/mapbox/driving/${start[0]},${start[1]};${end[0]},${end[1]}?geometries=geojson&access_token=${mapboxgl.accessToken}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                const route = data.routes[0].geometry;
                const distance = data.routes[0].distance / 1000; // Convert to km

                // Remove previous route if it exists
                if (routeLayer) map.removeLayer('route');
                if (map.getSource('route')) map.removeSource('route');

                // Add new route to map
                map.addSource('route', { type: 'geojson', data: { type: 'Feature', geometry: route } });
                routeLayer = map.addLayer({
                    id: 'route',
                    type: 'line',
                    source: 'route',
                    layout: { 'line-join': 'round', 'line-cap': 'round' },
                    paint: { 'line-color': '#007cbf', 'line-width': 5 }
                });

                // Display distance and calculate fare
                document.getElementById("Mileage").value = `${distance.toFixed(2)} km`;
                document.getElementById("Charging").value = `Rs. ${(distance * 250).toFixed(2)}`;
            });
    }

    // Add event listeners to input fields
    document.getElementById("pickupLocation").addEventListener("change", updateMarkers);
    document.getElementById("dropoffLocation").addEventListener("change", updateMarkers);
});


