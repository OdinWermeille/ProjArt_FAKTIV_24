<template>
    <div class="container">
        <div class="content">
            <h1 class="titre">{{ endroit.nom }}</h1>
            <img class="image-endroit" :alt="endroit.nom" :src="`/${endroit.image_url}`" />
            <div class="localit-wrapper">
                <div class="localit">Localité</div>
                <b class="lausanne">{{ endroit.localite }}</b>
            </div>
            <div class="voir-sur-la-carte-wrapper">
                <button class="voir-sur-la" @click="scrollToMap">Voir sur la carte</button>
            </div>
            <hr class="separator" />
            <div class="description-wrapper">
                <div class="description">DESCRIPTION</div>
                <div class="la-cathdrale-de">
                    {{ endroit.description }}
                </div>
            </div>
            <hr class="separator" />
            <div class="input-group map-container" ref="mapContainer">
                <div id="map" class="rectangle-parent2"></div>
            </div>
        </div>
    </div>
</template>


<script>
import { onMounted, ref } from 'vue';
import leaflet from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default {
    name: 'DetailEndroit',
    props: {
        endroit: Object,
    },
    setup(props) {
        const map = ref(null);
        const mapContainer = ref(null);

        const scrollToMap = () => {
            if (mapContainer.value) {
                mapContainer.value.scrollIntoView({ behavior: 'smooth' });
            }
        };

        onMounted(() => {
            const latitude = props.endroit.coordonneesY;
            const longitude = props.endroit.coordonneesX;

            console.log(props.endroit);

            map.value = leaflet
                .map("map")
                .setView([latitude, longitude], 13);

            leaflet.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            }).addTo(map.value);

            leaflet
                .marker([latitude, longitude])
                .addTo(map.value);
        });

        return {
            map,
            mapContainer,
            scrollToMap
        };
    }
};
</script>



<style scoped>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Inter', sans-serif;
    color: #212121;
}

.content {
    padding: 20px 0;
}

.titre {
    font-size: 24px;
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

.localit-wrapper {
    margin-bottom: 20px;
}

.localit {
    font-size: 14px;
    color: #666;
}

.lausanne {
    font-size: 16px;
    font-weight: bold;
}

.voir-sur-la-carte-wrapper {
    display: flex;
    justify-content: center;
    margin: 20px 0; /* Ajoute de l'espacement avant et après le bouton */
}

.voir-sur-la {
    background-color: #4a8c2a;
    color: #fafafa;
    text-transform: uppercase;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
    margin: 20px 0; /* Ajoute de l'espacement avant et après le bouton */
}

.separator {
    border: none;
    border-top: 2px solid #ddd;
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

.la-cathdrale-de {
    font-size: 14px;
    line-height: 1.6;
    color: #666;
}

.map-container {
    width: 100%;
    height: 400px; /* Assurez-vous que la hauteur est définie */
    margin-bottom: 20px;
}

.rectangle-parent2 {
    width: 100%;
    height: 100%; /* Assurez-vous que la hauteur est définie */
}
</style>
