<template>

    <!-- Inclusion de styles pour la carte et la machine de routage Leaflet -->
    <Head title="Carte">
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />

        <link rel="stylesheet"
            href="https://unpkg.com/leaflet-routing-machine@latest/dist/leaflet-routing-machine.css" />
    </Head>
    
    <!-- Conteneur pour les boutons de filtrage et de redirection vers la liste -->
    <div>
        <div class="searchContainer">
            <div class="buttonsWrapper">
                <button class="button" @click="showFilterModal = true">Filtrer</button>
                <img class="listIcon" alt="List Icon" src="/storage/images/list.svg" @click="redirectToList" />
            </div>
        </div>
    </div>
    
    <!-- Conteneur pour la carte Leaflet -->
    <div id="map"></div>

    <!-- Modal de filtrage utilisant SwipeModal -->
    <SwipeModal v-model="isOpen" id="modal">
    </SwipeModal>

    <!-- Modal de filtrage -->
    <div v-if="showFilterModal" class="modalBackdrop" @click="closeModal">
        <div class="modal" @click.stop>
            <div class="modalHeader">
                <h3>Filtrer par</h3>
            </div>
            <div class="modalContent">
                <!-- Filtres par activité -->
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
                <!-- Filtres par distance -->
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
"use strict";
import { Head } from '@inertiajs/vue3';
import { onMounted, ref, watchEffect } from 'vue';
import leaflet from 'leaflet';
import { useGeolocation } from '@vueuse/core';
import { userMarker, nearbyMarkers } from '@/stores/mapStore';
import 'leaflet/dist/leaflet.css';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import 'leaflet.awesome-markers';
import 'leaflet.awesome-markers/dist/leaflet.awesome-markers.css';
import { SwipeModal } from "@takuma-ru/vue-swipe-modal";

const { coords } = useGeolocation();
let map;
let userGeoMarker;
let routingControl;

const sentiers = [];

const redirectToList = () => {
    window.location.href = '/sentiers';
};

// Fonction pour déterminer la couleur en fonction de l'identifiant du thème
const returnColor = (theme_id) => {
    switch (theme_id) {
        case 2:
            return 'darkred';
        case 3:
            return 'blue';
        case 4:
            return 'green';
        case 5:
            return 'purple';
        case 6:
            return 'darkgreen';
        case 7:
            return 'red';
        case 8:
            return 'orange';
        case 9:
            return 'darkblue';
        default:
            return 'black';
    }
}

const isOpen = ref(false);
const showFilterModal = ref(false);
const filterActivity = ref('tout');
const filterDistance = ref('tout');
const filteredSentiers = ref([]);
const routingControls = [];

const closeModal = () => {
    showFilterModal.value = false;
};

// Réinitialise les filtres et applique les modifications
const resetFilters = () => {
    filterActivity.value = 'tout';
    filterDistance.value = 'tout';
    applyFilters();
    closeModal();
};

// Applique les filtres et met à jour la liste des sentiers filtrés
const applyFilters = () => {
    filteredSentiers.value = sentiers.filter(sentier => {
        const matchActivity = filterActivity.value === 'tout' || sentier.theme.nom.trim().toLowerCase() === filterActivity.value.trim().toLowerCase();
        const matchDistance = filterDistance.value === 'tout' || (
            filterDistance.value === '0-5' && sentier.longueur <= 5 ||
            filterDistance.value === '6-10' && sentier.longueur > 5 && sentier.longueur <= 10 ||
            filterDistance.value === '11+' && sentier.longueur > 10
        );
        return matchActivity && matchDistance;
    });
    hideAllSentiers();
    filteredSentiers.value.forEach((sentier) => {
        showSentier(sentier);
    });
    showFilterModal.value = false;
};

// Ouvre la description du sentier dans un modal
const openDescription = (e) => {
    const modalHandleWrapper = document.querySelector("#modal .swipe-modal-content .swipe-modal-drag-handle-wrapper");
    modalHandleWrapper.addEventListener("click", () => {
        isOpen.value = false;
    });

    const truncateDescription = (description) => {
        if (description.length > 200) {
            let truncated = description.substring(0, 200);
            const lastSpace = truncated.lastIndexOf(' ');
            truncated = truncated.substring(0, lastSpace) + '...';
            return truncated;
        }
        return description;
    };

    // Fonction pour formater la durée en heures et minutes
    const formatDuration = (minutes) => {
        if (minutes >= 60) {
            const hours = Math.floor(minutes / 60);
            const remainingMinutes = minutes % 60;
            return remainingMinutes === 0 ? `${hours}h` : `${hours}h ${remainingMinutes}min`;
        }
        return `${minutes} min`;
    };

    const modalContent = document.querySelector("#modal .swipe-modal-content .panel");
    let html = `
    <div class="modalHeader enhanced-header">
        <h2 class="header-title">Sentiers passant par ${e.target.options.customProperties.endroit.nom} </h2>
        <button class="closeButton enhanced-close" id="closeModalButton">&times;</button>
    </div>
    <div class="modalCards">`;

    filteredSentiers.value.forEach((sentier) => {
        sentier.endroits.forEach((endroit) => {
            if (endroit.id == e.target.options.customProperties.endroit.id) {
                html += `
                <a class="groupLienCard" href="/sentiers/${sentier.nom.toLowerCase().replace(/\s+/g, '-')}">
                    <div class="groupParent">
                    <div class="sentierCard">
                        <img class="sentierImage" src="${sentier.image_url}" alt="Sentier Image">
                        <div class="sentierContent">
                            <h2 class="sentierTitle">${sentier.nom}</h2>
                            <p class="sentierDescription">${truncateDescription(sentier.description)}</p>
                            <div class="sentierInfo">
                                <span class="sentierLength">${sentier.longueur}km</span>
                                <span class="sentierSeparator">•</span>
                                <span class="sentierDuration">${sentier.theme.nom}</span>
                                <span class="sentierSeparator">•</span>
                                <span class="sentierDuration">${formatDuration(sentier.duree)}</span>
                            </div>
                        </div>
                    </div>
                    </div>
                </a>`;
            }
        });
    });

    html += `</div>`;
    modalContent.innerHTML = html;

    const closeModalButton = document.getElementById("closeModalButton");
    closeModalButton.addEventListener("click", function () {
        isOpen.value = false;
    });

    isOpen.value = true;
};

// Cache tous les sentiers de la carte
const hideAllSentiers = () => {
    routingControls.forEach((routingControl) => {
        // Supprimer les marqueurs de la carte
        routingControl.getWaypoints().forEach(waypoint => {
            if (waypoint.marker) {
                map.removeLayer(waypoint.marker);
            }
        });
        // Supprimer les contrôles de routage de la carte
        map.removeControl(routingControl);
    });

    // Vider le tableau de contrôles de routage
    routingControls.length = 0;
};

// Affiche un sentier sur la carte
const showSentier = (sentier) => {
    const lineOptions = {
        styles: [{
            color: 'blue',
            weight: 5,
            opacity: 0.5
        }]
    };
    lineOptions.styles[0].color = returnColor(sentier.theme_id);
    const customIcon = leaflet.AwesomeMarkers.icon({
        icon: 'info-sign',
        markerColor: 'blue',
        prefix: 'glyphicon',
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
        createMarker: function (i, waypoint) {
            const marker = leaflet.marker(waypoint.latLng, {
                icon: customIcon,
                customProperties: {
                    endroit: sentier.endroits[i]
                }
            });

            marker.bindPopup("lieu sélectionné")

            marker.on('click', function (e) {
                openDescription(e);
            });
            return marker;
        },
        router: new leaflet.Routing.OSRMv1({
            serviceUrl: "https://routing.openstreetmap.de/routed-foot/route/v1"
        })
    }).addTo(map);
    routingControl.on('routesfound', function () {
        document.querySelectorAll('.leaflet-routing-container').forEach((el) => {
            el.style.display = 'none';
        });
    });

    sentier.endroits.forEach((endroit) => {
        routingControl.options.waypoints.push(leaflet.latLng([endroit.coordonneesX, endroit.coordonneesY]));
    });
    routingControl.setWaypoints(routingControl.options.waypoints);

    routingControls.push(routingControl);
}

onMounted(() => {
    // Initialisation de la carte
    map = leaflet
        .map("map")
        .setView([`${userMarker.value.latitude ? userMarker.value.latitude : 46.519962}`, `${userMarker.value.longitude ? userMarker.value.longitude : 6.633597}`], 13);

    leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(map);

    // Ajout des marqueurs à proximité de l'utilisateur
    nearbyMarkers.value.forEach(({ latitude, longitude }) => {
        leaflet
            .marker([latitude, longitude])
            .addTo(map)
            .bindPopup(`Nouveau lieu de l'utilisateur à ${latitude.toFixed(2)}, ${longitude.toFixed(2)}`);
    });

    // Suivi des coordonnées de l'utilisateur
    watchEffect(() => {
        if (coords.value.latitude !== Number.POSITIVE_INFINITY && coords.value.longitude !== Number.POSITIVE_INFINITY) {
            userMarker.value.latitude = coords.value.latitude;
            userMarker.value.longitude = coords.value.longitude;

            if (!userGeoMarker) {
                map.setView([userMarker.value.latitude, userMarker.value.longitude], 13);
            } else{
                map.removeLayer(userGeoMarker);
            }

            userGeoMarker = leaflet.marker([userMarker.value.latitude, userMarker.value.longitude], {
                    icon : leaflet.AwesomeMarkers.icon({
                        icon: 'round', 
                        markerColor: 'green', 
                        prefix: 'glyphicon', 
                    }),
                })
                .addTo(map)
                .bindPopup("Vous êtes ici");

            const el = userGeoMarker.getElement();
            if (el) {
                el.style.filter = "hue-rotate(275deg)";
            }
        }
    });

    // Récupération des sentiers depuis l'API et initialisation des filtres
    fetch("/carteFetch/sentiers")
    .then((res) => res.json())
    .then((data) => {
        data.forEach((sentier) => {
            sentiers.push(sentier);
            showSentier(sentier);
        })
        resetFilters();
    });

});
</script>

<style>
#map {
    width: 100%;
    height: calc(100vh - 190px);
    position: relative;
    z-index: 10;
}

.searchContainer {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 16px;
    padding-top: 20px;
    background-color: #F5F5F5;
    border-bottom: 1px solid #7d7d7d;
}

.buttonsWrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.button {
  font-family: "Inter", sans-serif;
  padding: 8px 16px;
  border: 1px solid #F0F0F0;
  border-radius: 24px;
  background-color: #FAFAFA;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.button:hover {
  background-color: #F0F0F0;
  color: #BFD2A6;
}

.listIcon {
  width: 24px;
  height: 24px;
  cursor: pointer;
}

.listIcon:hover {
  transform: translateY(-5px);
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
    background: #FAFAFA;
    width: 100%;
    max-width: 400px;
    border-radius: 16px;
    padding: 16px;
    box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.2);
}

.modalHeader {
    display: flex;
    align-items: flex-end;
    justify-content: flex-end;
    border-bottom: 1px solid #7d7d7d;
    padding-bottom: 8px;
    margin-bottom: 16px;
}

.modalHeader h3 {
    margin: 0;
    font-size: 1.1rem;
    color: #212121;
}

.modalContent {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.modalContent h4{
    display: flex;
    font-size: 1.1rem;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 12px;
}

.radioGroup,
.radioGroupHorizontal {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.radioGroupHorizontal {
    flex-direction: row;
}

.radioGroup label,
.radioGroupHorizontal label {
    display: flex;
    align-items: center;
    gap: 8px;
}

.modalFooter {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid #F0F0F0;
    margin-top: 16px;
}

.resetButton {
    background: none;
    border: 1px solid #4A8C2A;
    border-radius: 24px;
    padding: 8px 16px;
    color: #4A8C2A;
    cursor: pointer;
}

.validateButton {
    background: #4A8C2A;
    border: none;
    border-radius: 24px;
    padding: 8px 16px;
    color: #FAFAFA;
    cursor: pointer;
}

.modalCards {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
    justify-content: center;
}

.groupLienCard {
    display: inline-block;
    text-decoration: none;
}

.groupParent {
    display: inline-flex;
    justify-content: center;
    padding: 16px;
}

.sentierCard {
    background: #FAFAFA;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    overflow: hidden;
    cursor: pointer;
    transition: transform 0.2s;
    margin-bottom: 16px;
    max-width: 300px;
}

.sentierCard:hover {
    transform: translateY(-5px);
}

.sentierImage {
    width: 100%;
    height: 180px;
    object-fit: cover;
}

.sentierContent {
    padding: 16px;
    color: #212121;
}

.sentierTitle {
    font-size: 1.25rem;
    margin-bottom: 8px;
    color: #212121;
    font-weight: bold;
}

.sentierDescription {
    font-size: 1rem;
    color: #7d7d7d;
    margin-bottom: 16px;
}

.sentierInfo {
    display: flex;
    align-items: center;
    font-size: 0.875rem;
    color: #4A8C2A;
}

.sentierLength {
    margin-right: 8px;
}

.sentierSeparator {
    margin: 0 8px;
}

.sentierDuration {
    margin-left: 8px;
}

.closeButton {
    background: none;
    border: none;
    font-size: 3.5rem;
    cursor: pointer;
    color: #7d7d7d;
    position: absolute;
    right: 48px;
    top: 24px;
}

.swipe-modal-drag-handle-wrapper {
    display: none !important;
}

.enhanced-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    color: #212121;
    padding: 16px;
    border-bottom: 1px solid #BFD2A6;
    border-radius: 8px 8px 0 0;
}
 
.header-title {
    font-size: 1.5rem;
    font-weight: bold;
    margin: 0;
}
 
.enhanced-close {
    background: none;
    border: none;
    font-size: 3rem;
    cursor: pointer;
    color: #212121;
}
</style>