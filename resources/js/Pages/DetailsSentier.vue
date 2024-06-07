<template>
  <div class="sentier-page">
    <h1>{{ sentier.nom }}</h1>
    <section class="image-section">
      <img :alt="'image ' + sentier.nom" :src="`/${sentier.image_url}`" />
    </section>
    <section class="info-section">
      <div class="info">
        <img src="/storage/images/chemin.svg" alt="List Icon" class="nav-icon" />
        <p>{{ sentier.longueur }}km</p>
      </div>
      <div class="info">
        <img src="/storage/images/theme.svg" alt="List Icon" class="nav-icon" />
        <p>{{ sentier.theme.nom }}</p>
      </div>
      <div class="info">
        <img src="/storage/images/horloge.svg" alt="List Icon" class="nav-icon" />
        <p>{{ formattedDuration }}</p>
      </div>
    </section>
    <div class="map-wrapper">
      <button class="map-button" @click="scrollToMap">Voir sur la carte</button>
    </div>
    <hr class="separator" />
    <section class="description">
      <h2>Description</h2>
      <p>{{ sentier.description }}</p>
    </section>

    <hr class="separator" />
    <section class="points-section">
      <h2>Le parcours</h2>
      <p class= "parcours" >Clique sur un lieu pour plus d'informations</p>
      <section class="map-section" ref="mapContainer">
        <div id="map" class="map-container"></div>
      </section>
    </section>
  </div>
</template>


<script>
import { ref, onMounted, computed } from 'vue';
import L from 'leaflet';
import 'leaflet-routing-machine';
import 'leaflet/dist/leaflet.css';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import 'leaflet.awesome-markers';
import 'leaflet.awesome-markers/dist/leaflet.awesome-markers.css';

export default {
  props: {
    sentier: Object
  },
  setup(props) {
    let map = ref(null);
    const mapContainer = ref(null);

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

    const initializeMap = () => {
      if (map.value) return;
      map.value = L.map('map').setView([46.8182, 8.2275], 8); // Centrer la carte sur la Suisse
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map.value);

      const waypoints = props.sentier.endroits.map(endroit => L.latLng(endroit.coordonneesX, endroit.coordonneesY));

      const control = L.Routing.control({
        waypoints: waypoints,
        routeWhileDragging: true,
        addWaypoints: false,
        draggableWaypoints: false,
        show: false,
        lineOptions: {
          styles: [{ color: returnColor(props.sentier.theme_id), opacity: 1, weight: 5 }]
        },
        fitSelectedRoutes: true,
        showAlternatives: false,
        router: new L.Routing.OSRMv1({
          serviceUrl: "http://routing.openstreetmap.de/routed-foot/route/v1"
        }),
        createMarker: function (i, waypoint) {
          const endroit = props.sentier.endroits[i];
          const customIcon = L.AwesomeMarkers.icon({
            icon: 'info-sign',
            markerColor: returnColor(props.sentier.theme_id),
            prefix: 'glyphicon',
          });

          const marker = L.marker(waypoint.latLng, {
            icon: customIcon,
            draggable: false
          });

          const slug = endroit.nom.toLowerCase().replace(/\s+/g, '-'); // Convertir le nom en un slug URL-friendly
          // Ajouter un pop-up avec le nom de l'endroit et un lien vert pointant vers la page de détail
          marker.bindPopup(`
            <b>${endroit.nom}</b><br>
            <a href="/lieux/${slug}" style="color: #4a8c2a">Plus d'infos</a>
          `);

          return marker;
        }
      }).addTo(map.value);
    };

    const scrollToMap = () => {
      if (mapContainer.value) {
        mapContainer.value.scrollIntoView({ behavior: 'smooth' });
      }
    };

    const formattedDuration = computed(() => {
      const minutes = props.sentier.duree;
      if (minutes >= 60) {
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        return remainingMinutes === 0 ? `${hours}h` : `${hours}h ${remainingMinutes}min`;
      }
      return `${minutes} min`;
    });

    onMounted(() => {
      initializeMap();
    });

    return {
      mapContainer,
      scrollToMap,
      formattedDuration
    };
  },
  methods: {
    onGroupContainerClick(nom) {
      const slug = nom.toLowerCase().replace(/\s+/g, '-'); // Convertir le nom en un slug URL-friendly
      window.location.href = `/lieux/${slug}`;;
    }
  }
}
</script>



<style scoped>
@import url('https://unpkg.com/leaflet/dist/leaflet.css');
@import url('https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css');
@import url('https://unpkg.com/leaflet.awesome-markers/dist/leaflet.awesome-markers.css');

h1 {
  font-family: "Inter", sans-serif;
  font-size: 18px;
  font-weight: bold;
  height: 48px;
}

h2 {
  font-family: "Inter", sans-serif;
  font-size: 16px;
  font-weight: bold;
  height: 48px;
}

h3 {
  font-family: "Inter", sans-serif;
  font-size: 14px;
  height: 48px;
}

.sentier-page {
  font-family: "Inter", sans-serif;
  color: #212121;
  max-width: 600px;
  margin: 0 auto;
  padding: 20px;
}

.image-section img {
  width: 100%;
  height: auto;
  border-radius: 10px;
}

.info-section {
  display: flex;
  justify-content: space-between;
  margin: 20px 0;
}

.info {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.info img {
  margin-right: 10px;
}

.description {
  margin: 20px 0;
}

.map-container {
  width: 100%;
  height: 400px;
  /* Adjust the height as needed */
  border-radius: 10px;
}

.separator {
  border: none;
  border-top: 2px solid #fafafa;
  margin: 20px 0;
}

.map-wrapper {
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
  border: #4a8c2a solid 1px;
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

.parcours {
  margin-bottom: 24px;
}
</style>
