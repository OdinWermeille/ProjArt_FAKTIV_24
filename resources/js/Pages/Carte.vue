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
                <button class="button" @click="showFilterModal = true">Filtrer</button>
                <img class="listIcon" alt="List Icon" src="/images/list.svg" @click="redirectToList" />
            </div>
        </div>
    </div>
    <div id="map"></div>
    <SwipeModal v-model="isOpen" id="modal">
      <div>test</div>
    </SwipeModal>
    
    <!-- Filter Modal -->
    <div v-if="showFilterModal" class="modalBackdrop" @click="closeModal">
        <div class="modal" @click.stop>
            <div class="modalHeader">
                <button @click="closeModal" class="closeButton">&times;</button>
                <h3>Filtrer par</h3>
            </div>
            <div class="modalContent">
                <div>
                    <h4><strong>Activité</strong></h4>
                    <div class="radioGroup">
                        <label>
                            <input type="radio" name="activity" value="tout" v-model="filterActivity" />
                            Tout
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Historique" v-model="filterActivity" />
                            Historique
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Arts & Culture" v-model="filterActivity" />
                            Arts & Culture
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Nature" v-model="filterActivity" />
                            Nature
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Architecture" v-model="filterActivity" />
                            Architecture
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Street Art" v-model="filterActivity" />
                            Street Art
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Sportif" v-model="filterActivity" />
                            Sportif
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Gastronomie" v-model="filterActivity" />
                            Gastronomie
                        </label>
                        <label>
                            <input type="radio" name="activity" value="Éphémère" v-model="filterActivity" />
                            Éphémère
                        </label>
                    </div>
                </div>
                <div>
                    <h4><strong>Distance</strong></h4>
                    <div class="radioGroupHorizontal">
                        <label>
                            <input type="radio" name="distance" value="tout" v-model="filterDistance" />
                            Tout
                        </label>
                        <label>
                            <input type="radio" name="distance" value="0-5" v-model="filterDistance" />
                            0-5 km
                        </label>
                        <label>
                            <input type="radio" name="distance" value="6-10" v-model="filterDistance" />
                            6-10 km
                        </label>
                        <label>
                            <input type="radio" name="distance" value="11+" v-model="filterDistance" />
                            11+ km
                        </label>
                    </div>
                </div>
            </div>
            <div class="modalFooter">
                <button class="resetButton" @click="resetFilters">RÉINITIALISER</button>
                <button class="validateButton" @click="applyFilters">VALIDER</button>
            </div>
        </div>
    </div>
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
import { SwipeModal } from "@takuma-ru/vue-swipe-modal"

const { coords } = useGeolocation();
let map;
let userGeoMarker;
let routingControl;

const sentiers = [];

const redirectToList = () => {
    window.location.href = '/sentiers';
};

const returnColor = (theme_id) => {
        switch (theme_id) {
            case 1:
                return 'orange';
            case 2:
                return 'darkred';
            case 3:
                return 'darkgreen';
            case 4:
                return 'cadetblue';
            case 5:
                return 'purple';
            case 6:
                return 'red';
            case 7:
                return 'green';
            case 8:
                return 'darkpurple';
            default:
                return 'blue';
        }
    }

const isOpen = ref(false);
const showFilterModal = ref(false);
const filterActivity = ref('tout');
const filterDistance = ref('tout');
const filteredSentiers = ref([]);

const closeModal = () => {
    showFilterModal.value = false;
};

const resetFilters = () => {
    filterActivity.value = 'tout';
    filterDistance.value = 'tout';
    applyFilters();
    closeModal();
};

const applyFilters = () => {
    // Filtrer les sentiers selon les critères sélectionnés
    filteredSentiers.value = sentiers.filter(sentier => {
        const matchActivity = filterActivity.value === 'tout' || sentier.theme.nom.trim().toLowerCase() === filterActivity.value.trim().toLowerCase();
        const matchDistance = filterDistance.value === 'tout' || (
            filterDistance.value === '0-5' && sentier.longueur <= 5 ||
            filterDistance.value === '6-10' && sentier.longueur > 5 && sentier.longueur <= 10 ||
            filterDistance.value === '11+' && sentier.longueur > 10
        );
        closeModal();
        return matchActivity && matchDistance;
    });
    showFilterModal.value = false;
};

const openDescription = (e) => {
    const modalHandleWrapper = document.querySelector("#modal .swipe-modal-content .swipe-modal-drag-handle-wrapper");
    modalHandleWrapper.addEventListener("click", function(){
        isOpen.value = false;
    })
    const modalContent = document.querySelector("#modal .swipe-modal-content .panel");
    let html = `
    <div class="modalHeader">
        <h2>${e.target.options.customProperties.endroit.nom}</h2>
    </div>
    <div class="modalContent">
        <h3>Sentiers passant par cet endroit</h3>`
    sentiers.forEach((sentier) => {
        sentier.endroits.forEach((endroit) => {
            if (endroit.id == e.target.options.customProperties.endroit.id) {
                html += `
                <h4>Sentier : ${sentier.nom}</h4>
                <a href="/sentiers/${sentier.nom.toLowerCase().replace(/\s+/g, '-')}">
                    <img src="${sentier.image_url}"/>
                </a>`
            }
        })
    })
    html += `</div>`
    modalContent.innerHTML = html;
    isOpen.value = true
}

const showSentier = (sentier) => {
    console.log(sentier);
    sentiers.push(sentier);
    const lineOptions = {
        styles : [{
            color: 'blue',
            weight: 5,
            opacity: 0.4
        }]
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

                            
            marker.bindPopup("lieu séléctionné")

            marker.on('click', function(e) {
                openDescription(e);
                //window.location.href = `/sentiers/${sentier.id}`;
            });
            return marker;
        },
        router: new leaflet.Routing.OSRMv1({
            serviceUrl: "http://routing.openstreetmap.de/routed-foot/route/v1"
        })    
    }).addTo(map);

                    
    sentier.endroits.forEach((endroit) => {
        routingControl.options.waypoints.push(leaflet.latLng([endroit.coordonneesX, endroit.coordonneesY]));
    })
    routingControl.setWaypoints(routingControl.options.waypoints);
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
            userMarker.value.latitude = coords.value.latitude;
            userMarker.value.longitude = coords.value.longitude;

            if (!userGeoMarker) {
                map.setView([userMarker.value.latitude, userMarker.value.longitude], 13);
            }

            userGeoMarker = leaflet.marker([userMarker.value.latitude, userMarker.value.longitude])
            .addTo(map)
            .bindPopup("Les informations sur le lieu vont ici");

            const el = userGeoMarker.getElement();
            if (el) {
            el.style.filter = "hue-rotate(140deg)";
            }
        }
    });


    const here = window.location.href;
    const urlArr = here.split(`/`);

    if (urlArr[urlArr.length-1] !== "carte") {
        fetch(`/carteFetch/sentiers`)
        .then((res) => res.json())
        .then((data) => {
            data.forEach((sentier) => {
                if (sentier.id == urlArr[urlArr.length-1]) {
                    showSentier(sentier);
                }
            })
        })
        .catch((err) => {
            console.log(err);
        });
    }else{
        fetch("/carteFetch/sentiers")
        .then((res) => res.json())
        .then((data) => {
            data.forEach((sentier) => {
                showSentier(sentier);
            })
        });
    }
});
</script>

<style>
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

    #modal {
        background: white;
        width: 100%;
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
        align-items: center;
        gap: 16px;
    }

    .modalHeader h2{
        margin: 0;
        margin-bottom: 8px;
        font-size: 2rem;
    }

    .modalContent h3 {
        margin: 0;
        margin-bottom: 8px;
        font-size: 1.5rem;
    }

    .modalContent h4 {
        margin: 0;
        margin-bottom: 8px;
        font-size: 1rem;
    }

    .modalContent img {
        width: 100%;
        height: auto;
        border-radius: 10px;
    }

    .modalFooter {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #ddd;
        margin-top: 16px;
    }

    .modal {
        font-family: "Inter", sans-serif;
        background: #FAFAFA;
        width: 100%;
        max-width: 400px;
        border-top-left-radius: 16px;
        border-top-right-radius: 16px;
        padding: 16px;
        box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.2);
    }

    .radioGroup {
        font-family: "Inter", sans-serif;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .radioGroupHorizontal {
        font-family: "Inter", sans-serif;
        display: flex;
        flex-direction: row;
        gap: 16px;
    }

    .radioGroup label {
        font-family: "Inter", sans-serif;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .modalFooter {
        font-family: "Inter", sans-serif;
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 16px;
        border-top: 1px solid #F0F0F0;
        margin-top: 16px;
    }

    .resetButton {
        font-family: "Inter", sans-serif;
        background: none;
        border: 1px solid #4A8C2A;
        border-radius: 24px;
        padding: 8px 16px;
        color: #4A8C2A;
        cursor: pointer;
    }

    .validateButton {
        font-family: "Inter", sans-serif;
        background: #4A8C2A;
        border: none;
        border-radius: 24px;
        padding: 8px 16px;
        color: #FAFAFA;
        cursor: pointer;
    }
</style>