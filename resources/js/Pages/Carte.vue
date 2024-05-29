<script setup>
    import { Head } from '@inertiajs/vue3';
    import { onMounted, ref, watchEffect } from 'vue'
    import leaflet from 'leaflet'
    import { useGeolocation } from '@vueuse/core'
    import { userMarker, nearbyMarkers } from '@/stores/mapStore'
    import 'leaflet/dist/leaflet.css';
    import 'leaflet-routing-machine';
    import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';


    const here = window.location.href;
    const urlArr = here.split(`/`);
    const { coords } = useGeolocation();

    let map;
    let userGeoMarker;

    fetch("/sentiers")
    .then((res) => res.json())
    .then((data) => {
        console.log(data);
    });


    onMounted(() => {
    map = leaflet
        .map("map")
        .setView([userMarker.value.latitude, userMarker.value.longitude], 13);

    leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    nearbyMarkers.value.forEach(({ latitude, longitude }) => {
        leaflet
        .marker([latitude, longitude])
        .addTo(map)
        .bindPopup(`Nouveau lieu de l'utilisateur à ${latitude.toFixed(2)}, ${longitude.toFixed(2)}`);
    });

    map.addEventListener("click", (e) => {
        const latitude = e.latlng.lat;
        const longitude = e.latlng.lng;
        const NewGeoMarker = leaflet.marker([latitude, longitude])
        .addTo(map)
        .bindPopup(`Nouveau lieu de l'utilisateur à ${latitude.toFixed(2)}, ${longitude.toFixed(2)}`);
        nearbyMarkers.value.push({ latitude, longitude });
        console.log(routingControl);
    });

    watchEffect(() => {
        if (coords.value.latitude !== Number.POSITIVE_INFINITY && coords.value.longitude !== Number.POSITIVE_INFINITY) {
            userMarker.value.latitude = coords.value.latitude + 0.014;
            userMarker.value.longitude = coords.value.longitude + 0.014;

            if (userGeoMarker) {
            map.removeLayer(userGeoMarker);
            }

            userGeoMarker = leaflet.marker([userMarker.value.latitude, userMarker.value.longitude])
            .addTo(map)
            .bindPopup("Les informations sur le lieu vont ici");
            map.setView([userMarker.value.latitude, userMarker.value.longitude]);

            const el = userGeoMarker.getElement();
            if (el) {
            el.style.filter = "hue-rotate(140deg)";
            }
        }
    });

    let routingControl = leaflet.Routing.control({
        waypoints: [
            leaflet.latLng([46.783, 6.644]),
            leaflet.latLng([46.781, 6.646])
        ],
        routeWhileDragging: true,
        show: true,
        addWaypoints: false,
        draggableWaypoints: true,
        router: new leaflet.Routing.OSRMv1({
            serviceUrl: "http://routing.openstreetmap.de/routed-foot/route/v1"
        })
    }).addTo(map);

    leaflet.Routing.control({
        waypoints: [
            leaflet.latLng([46.783, 6.650]),
            leaflet.latLng([46.781, 6.651])
        ],
        routeWhileDragging: true,
        show: false,
        addWaypoints: false,
        draggableWaypoints: true,
        router: new leaflet.Routing.OSRMv1({
            serviceUrl: "http://routing.openstreetmap.de/routed-foot/route/v1"
        })
        }).addTo(map);
    });
</script>

<template>
    <Head title="Carte">
        <link 
        rel="stylesheet" 
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>

        <link 
        rel="stylesheet" 
        href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />

        
    </Head>
    <p>Hello world {{ urlArr[urlArr.length-1] == "carte" ? "" : urlArr[urlArr.length-1] }}</p>
    <div id="map"></div>
</template>

<style scoped>
    #map {
        width: 100%;
        height: 100vh;
    }
</style>