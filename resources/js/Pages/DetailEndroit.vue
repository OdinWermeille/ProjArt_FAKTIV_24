<template>
    <div class="container">
        <div class="content">
            <h1 class="titre">{{ endroit.nom }}</h1>
            <img class="image-endroit" :alt="endroit.nom" :src="`/${endroit.image_url}`" />
            <div class="localite-wrapper">
                <img src="/storage/images/map-ping.svg" alt="List Icon" class="nav-icon" />
                <p class="localite-name">{{ endroit.localite }}</p>
            </div>
            <div class="button-wrapper">
                <button class="map-button" @click="scrollToMap">Voir sur la carte</button>
            </div>
            <hr class="separator" />
            <div class="description-wrapper">
                <h2>Description</h2>
                <div class="description-text">
                    {{ endroit.description }}
                </div>
            </div>
            <hr class="separator" />
            <div class="nearest-stop-wrapper" v-if="nearestStop">
                <h2>Arrêt de transport public le plus proche</h2>
                <div class="nearest-stop">
                    <img :src="`/storage/images/${nearestStop.icon}.svg`" alt="icon {{ nearestStop.icon }}" class="nav-icon" />
                    <p>{{ nearestStop.name }} ({{ nearestStop.distance }} mètres)</p>
                </div>
            </div>
            <hr class="separator" />
            <div class="input-group map-container" ref="mapContainer">
                <h2>Emplacement</h2>
                <div id="map" class="rectangle-parent2"></div>
            </div>
        </div>
    </div>
</template>

<script>
import { onMounted, ref } from 'vue';
import leaflet from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.awesome-markers';
import 'leaflet.awesome-markers/dist/leaflet.awesome-markers.css';

export default {
    name: 'DetailEndroit',
    props: {
        endroit: Object,
    },
    setup(props) {
        const map = ref(null);
        const mapContainer = ref(null);
        const nearestStop = ref(null);

        const scrollToMap = () => {
            if (mapContainer.value) {
                mapContainer.value.scrollIntoView({ behavior: 'smooth' });
            }
        };

        const fetchNearbyStops = (latitude, longitude) => {
            const url = `http://transport.opendata.ch/v1/locations?x=${longitude}&y=${latitude}&type=station`;
            
            return fetch(url)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => data.stations)
                .catch(error => {
                    console.error('Erreur lors de la récupération des arrêts :', error);
                    return [];
                });
        };

        onMounted(() => {
            const latitude = props.endroit.coordonneesX;
            const longitude = props.endroit.coordonneesY;

            map.value = leaflet
                .map("map")
                .setView([latitude, longitude], 13);

            leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map.value);

            const customIcon = leaflet.AwesomeMarkers.icon({
                icon: 'info-sign',
                markerColor: 'black',
                prefix: 'glyphicon'
            });

            leaflet
                .marker([latitude, longitude], { icon: customIcon })
                .addTo(map.value)
                .bindPopup(`<b>${props.endroit.nom}</b><br>${props.endroit.localite}`);

            // Fetch nearby public transport stops
            fetchNearbyStops(latitude, longitude).then(stops => {
                if (stops.length > 0) {
                    nearestStop.value = stops[1];
                }
            });
        });

        return {
            map,
            mapContainer,
            nearestStop,
            scrollToMap
        };
    }
};
</script>

<style scoped>
@import url('https://unpkg.com/leaflet/dist/leaflet.css');
@import url('https://unpkg.com/leaflet.awesome-markers/dist/leaflet.awesome-markers.css');

h2 {
    font-family: "Inter", sans-serif;
    font-size: 16px;
    font-weight: bold;
    height: 48px;
}

.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Inter', sans-serif;
    color: #212121;
}

.content {
    font-family: "Inter", sans-serif;
    color: #212121;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
}

.titre {
    font-size: 24px;
    margin-bottom: 24px;
    font-weight: bold;
    height: 48px;
}

.image-endroit {
    width: 100%;
    height: auto;
    object-fit: cover;
    margin-bottom: 20px;
    border-radius: 10px;
}

.localite-wrapper {
    display: flex;
    align-items: center;
    margin-bottom: 20px;
}

.nav-icon {
    margin-right: 10px;
    color: #7D7D7D;
}

.localite-name {
    font-size: 16px;
    color: #212121;
}

.button-wrapper {
    display: flex;
    justify-content: center;
    margin: 20px 0;
    /* Ajoute de l'espacement avant et après le bouton */
}

.map-button {
    background-color: #4a8c2a;
    color: #fafafa;
    text-transform: uppercase;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    margin: 20px 0;
    /* Ajoute de l'espacement avant et après le bouton */
}

.map-button:hover {
    background-color: #fafafa;
    color: #4a8c2a;
    border: #4a8c2a solid 1px;
    cursor: pointer;
}

.separator {
    border: none;
    border-top: 2px solid #F0F0F0;
    margin: 20px 0;
}

.description-wrapper {
    margin-bottom: 20px;
}

.description {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 10px;
}

.description-text {
    font-size: 14px;
    line-height: 1.6;
    color: #7D7D7D;
}

.map-container {
    width: 100%;
    height: 400px;
    margin-bottom: 20px;
}

.rectangle-parent2 {
    width: 100%;
    height: 100%;
}

.nearest-stop-wrapper {
    display: flex;
    flex-direction: column;
    margin-bottom: 20px;
}

.nearest-stop {
    display: flex;
    align-items: center;
    flex-direction: row;
}

.nearest-stop img.nav-icon {
    margin-right: 10px;
}

.nearest-stop p {
    margin: 0;
}
</style>