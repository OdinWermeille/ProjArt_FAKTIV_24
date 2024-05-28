<script setup>
    import { Head } from '@inertiajs/vue3';
    import { onMounted, ref, watchEffect } from 'vue'
    import leaflet from 'leaflet'
    import { useGeolocation } from '@vueuse/core'
    import { userMarker, nearbyMarkers } from '@/stores/mapStore'
    import icon from 'leaflet/dist/images/marker-icon.png';
    import iconShadow from 'leaflet/dist/images/marker-shadow.png';
    const here = window.location.href
    const urlArr = here.split(`/`)
    const { coords } = useGeolocation();
    let map; leaflet.Map;
    let userGeoMarker; leaflet.Marker

    console.log(leaflet.Icon.Default.prototype._getIconUrl())
    onMounted(()=>{
        map = leaflet
            .map("map")
            .setView([userMarker.value.latitude, userMarker.value.longitude], 13)
        leaflet
        .tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        })
        .addTo(map)

        map.addEventListener("click", (e)=> {
            const NewGeoMarker = leaflet.marker([e.latlng.lat, e.latlng.lng])
            .addTo(map)
            .bindPopup(`Nouveau lieu de l'utilisateur à ${e.latlng.lat}, ${e.latlng.lng}`);
        })
    })
    
    watchEffect(() => {
        if (coords.value.latitude !== Number.POSITIVE_INFINITY && coords.value.longitude !== Number.POSITIVE_INFINITY) {
            userMarker.value.latitude = coords.value.latitude+0.014;
            userMarker.value.longitude = coords.value.longitude+0.014;

            
            if (userGeoMarker) {
                map.removeLayer(userGeoMarker);
            }

            userGeoMarker = leaflet.marker([userMarker.value.latitude, userMarker.value.longitude])
            .addTo(map)
            .bindPopup("Les informations sur le lieu vont ici");
            map.setView([userMarker.value.latitude, userMarker.value.longitude])
        }



    });
</script>

<template>
    <Head title="Carte">
        <link 
        rel="stylesheet" 
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY="
        crossorigin=""/>
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