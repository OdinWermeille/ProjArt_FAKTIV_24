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
    <div>
        <!-- Barre de recherche et boutons -->
        <div class="searchContainer">
            <div class="searchWrapper">
                <input type="text" class="searchInput" placeholder="Rechercher" />
                <img class="searchIcon" alt="Search Icon" src="/images/icon.svg" />
            </div>
            <div class="buttonsWrapper">    
                <button class="button" @click="showSortModal = true">Trier par</button>
                <button class="button" @click="showFilterModal = true">Filtrer</button>
                <img class="listIcon" alt="List Icon" src="/images/list.svg" @click="redirectToList" />
            </div>
        </div>
    </div>
    <div id="map"></div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watchEffect } from 'vue'
import leaflet from 'leaflet'
import { useGeolocation } from '@vueuse/core'
import { userMarker, nearbyMarkers } from '@/stores/mapStore'
import 'leaflet/dist/leaflet.css';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import 'leaflet.awesome-markers';
import 'leaflet.awesome-markers/dist/leaflet.awesome-markers.css';

const { coords } = useGeolocation();
let map;
let userGeoMarker;

const emit = defineEmits(['marker-click']);

const sentiers = [];

const redirectToList = () => {
    window.location.href = '/sentiers';
};

const returnColor = (theme_id) => {
        switch (theme_id) {
            case 1:
                return 'orange';
                break;
            case 2:
                return 'darkred';
                break;
            case 3:
                return 'darkgreen';
                break;
            case 4:
                return 'cadetblue';
                break;
            case 5:
                return 'purple';
                break;
            case 6:
                return 'red';
                break;
            case 7:
                return 'green';
                break;
            case 8:
                return 'darkpurple';
                break;
            default:
                return 'blue';
                break;
        }
    }

onMounted(() => {
    map = leaflet
        .map("map")
        .setView([`${userMarker.value.latitude ? userMarker.value.latitude : 46.519962}`, `${userMarker.value.longitude ? userMarker.value.longitude : 6.633597}`], 13);

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

    let routingControl;

    const here = window.location.href;
    const urlArr = here.split(`/`);

    if (urlArr[urlArr.length-1] !== "carte") {
            fetch(`/carteFetch/sentier/${urlArr[urlArr.length-1]}`)
            .then((res) => res.json())
            .then((sentier) => {
                sentiers.push(sentier);

                const lineOptions = {
                    styles : [
                        {
                            color: 'blue',
                            weight: 5,
                            opacity: 0.7
                        }
                    ]
                };
                lineOptions.styles[0].color = returnColor(sentier.theme_id);

                const customIcon = leaflet.AwesomeMarkers.icon({
                    icon: 'info-sign', // Nom de l'icône (par exemple, 'info-sign')
                    markerColor: 'blue', // Couleur du marqueur
                    prefix: 'glyphicon', // Préfixe pour l'icône (par exemple, 'fa' pour FontAwesome, 'glyphicon' pour Bootstrap)
                });
                customIcon.options.markerColor = returnColor(sentier.theme_id);

                routingControl = leaflet.Routing.control({
                    waypoints: [],
                    routeWhileDragging: true,
                    show: false,
                    addWaypoints: false,
                    draggableWaypoints: true,
                    lineOptions: lineOptions,
                    fitSelectedRoutes: false,
                    createMarker: function(i, waypoint) {
                        const marker = leaflet.marker(waypoint.latLng, {
                            icon: customIcon,
                            customProperties: {
                                endroit: sentier.endroits[i]
                            }
                        });

                        marker.on('click', function(e) {
                            emit('marker-click',{param : e});
                            //window.location.href = `/carte/${sentier.id}`;
                        });
                        
                        return marker;
                    },
                    // router: new leaflet.Routing.OSRMv1({
                    //     serviceUrl: "http://routing.openstreetmap.de/routed-foot/route/v1"
                    // })    
                }).addTo(map);

                    
                sentier.endroits.forEach((endroit) => {
                    routingControl.options.waypoints.push(leaflet.latLng([endroit.coordonneesX, endroit.coordonneesY]));
                })
                routingControl.setWaypoints(routingControl.options.waypoints);
            })
            .catch((err) => {
                console.log(err);
            });
        }else{
            fetch("/carteFetch/sentiers")
            .then((res) => res.json())
            .then((data) => {

                data.forEach((sentier) => {
                    sentiers.push(sentier);

                    const lineOptions = {
                        styles : [
                            {
                                color: 'blue',
                                weight: 5,
                                opacity: 0.4
                            }
                        ]
                    };
                    lineOptions.styles[0].color = returnColor(sentier.theme_id);
                    console.log(leaflet);
                    const customIcon = leaflet.AwesomeMarkers.icon({
                        icon: 'info-sign', // Nom de l'icône (par exemple, 'info-sign')
                        markerColor: 'blue', // Couleur du marqueur
                        prefix: 'glyphicon', // Préfixe pour l'icône (par exemple, 'fa' pour FontAwesome, 'glyphicon' pour Bootstrap)
                    });
                    customIcon.options.markerColor = returnColor(sentier.theme_id);

                    routingControl = leaflet.Routing.control({
                        waypoints: [],
                        routeWhileDragging: true,
                        show: false,
                        addWaypoints: false,
                        draggableWaypoints: true,
                        lineOptions: lineOptions,
                        fitSelectedRoutes: false,
                        createMarker: function(i, waypoint) {
                            const marker = leaflet.marker(waypoint.latLng, {
                                icon: customIcon,
                                customProperties: {
                                    endroit: sentier.endroits[i]
                                }
                            });

                            marker.on('click', function(e) {
                                console.log(this);
                                //window.location.href = `/carte/${sentier.id}`;
                            });
                            
                            return marker;
                        },
                        // router: new leaflet.Routing.OSRMv1({
                        //     serviceUrl: "http://routing.openstreetmap.de/routed-foot/route/v1"
                        // })    
                    }).addTo(map);

                    
                    sentier.endroits.forEach((endroit) => {
                        routingControl.options.waypoints.push(leaflet.latLng([endroit.coordonneesX, endroit.coordonneesY]));
                    })
                    console.log(routingControl);
                    routingControl.setWaypoints(routingControl.options.waypoints);
                })
            });
        }
});
</script>

<style scoped>
    #map {
        width: 100%;
        height: calc(100vh - 390px);
        position: relative;
        z-index: 10;
    }

    .searchContainer {
        display: flex;
        flex-direction: column;
        align-items: center;
        padding: 16px;
        background-color: #f5f5f5;
        border-bottom: 1px solid #ddd;
        margin-bottom: 16px;
    }

    .searchWrapper {
        display: flex;
        align-items: center;
        width: 100%;
        max-width: 600px;
        margin-bottom: 16px;
        position: relative;
    }

    .searchInput {
        flex: 1;
        padding: 8px 16px;
        border: 1px solid #ccc;
        border-radius: 24px;
        padding-right: 40px;
    }

    .searchIcon {
        position: absolute;
        right: 12px;
        width: 20px;
        height: 20px;
        cursor: pointer;
    }

    .buttonsWrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .button {
        padding: 8px 16px;
        border: 1px solid #ccc;
        border-radius: 24px;
        background-color: #fff;
        cursor: pointer;
        display: flex;
        align-items: center;
    }

    .mapIcon {
        width: 24px;
        height: 24px;
        cursor: pointer;
    }

    .groupParent {
        display: flex;
        flex-wrap: wrap;
        justify-content: space-around;
        gap: 16px;
        padding: 16px;
    }

    .card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        cursor: pointer;
        transition: transform 0.2s;
        width: 300px;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .image {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }

    .content {
        padding: 16px;
    }

    .title {
        font-size: 1.25rem;
        margin-bottom: 8px;
    }

    .description {
        font-size: 1rem;
        color: #555;
        margin-bottom: 16px;
    }

    .info {
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        color: #777;
    }

    .length {
        margin-right: 8px;
    }

    .separator {
        margin: 0 8px;
    }

    .duration {
        margin-left: 8px;
    }

    .modalBackdrop {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: flex-end;
        z-index: 1000;
    }

    .modal {
        background: white;
        width: 100%;
        max-width: 400px;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 16px;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.2);
    }

    .modalHeader {
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid #ddd;
        padding-bottom: 8px;
        margin-bottom: 16px;
    }

    .closeButton {
        background: none;
        border: none;
        font-size: 1.5rem;
        cursor: pointer;
        color: #555;
    }

    .modalContent {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .modalContent h4 {
        margin: 0;
        margin-bottom: 8px;
        font-size: 1rem;
    }

    .modalFooter {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #ddd;
        margin-top: 16px;
    }

    .resetButton {
        background: none;
        border: 1px solid green;
        border-radius: 24px;
        padding: 8px 16px;
        color: green;
        cursor: pointer;
    }

    .validateButton {
        background: green;
        border: none;
        border-radius: 24px;
        padding: 8px 16px;
        color: white;
        cursor: pointer;
    }
</style>
