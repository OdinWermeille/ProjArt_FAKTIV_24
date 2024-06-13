<template>
  <div class="sentier-page">
    <!-- Affiche le nom du sentier -->
    <h1>{{ sentier.nom }}</h1>
    <section class="image-section">
      <!-- Affiche l'image du sentier -->
      <img :alt="'image ' + sentier.nom" :src="`/${sentier.image_url}`" />
    </section>
    <section class="info-section">
      <!-- Affiche la longueur du sentier -->
      <div class="info">
        <img src="/storage/images/chemin.svg" alt="List Icon" class="nav-icon" />
        <p>{{ sentier.longueur }}km</p>
      </div>
      <!-- Affiche la thématique du sentier -->
      <div class="info">
        <img src="/storage/images/theme.svg" alt="List Icon" class="nav-icon" />
        <p>{{ sentier.theme.nom }}</p>
      </div>
      <!-- Affiche la durée formatée du sentier -->
      <div class="info">
        <img src="/storage/images/horloge.svg" alt="List Icon" class="nav-icon" />
        <p>{{ formattedDuration }}</p>
      </div>
    </section>
    <!-- Bouton pour faire défiler jusqu'à la carte -->
    <div class="map-wrapper">
      <button class="map-button" @click="scrollToMap">Voir sur la carte</button>
    </div>
    <hr class="separator" />
    <!-- Section de description du sentier -->
    <section class="description">
      <h2>Description</h2>
      <p>{{ sentier.description }}</p>
    </section>

    <hr class="separator" />
    <!-- Section des points du parcours du sentier -->
    <section class="points-section">
      <h2>Le parcours</h2>
      <div class="info">
        <img src="https://upload.wikimedia.org/wikipedia/commons/e/e4/Infobox_info_icon.svg" alt="Information Icon"
          class="nav-icon" />
        <p class="parcours">Cliquez sur un point de la carte pour plus d'informations</p>
      </div>
      <section class="map-section" ref="mapContainer">
        <div id="map" class="map-container"></div>
      </section>
    </section>
    <!-- Boutons pour modifier ou supprimer le sentier, affichés uniquement si l'utilisateur est authentifié -->
    <div v-if="isAuthenticated" class="button-wrapper">
      <button class="edit-button" @click="editSentier">Modifier</button>
      <button class="delete-button" @click="deleteSentier">Supprimer</button>
    </div>
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
import axios from 'axios';

export default {
  props: {
    sentier: Object
  },
  setup(props) {
    let map = ref(null);
    const mapContainer = ref(null);
    const isAuthenticated = ref(false);

    // Fonction pour retourner la couleur en fonction de l'ID du thème
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

    // Initialise la carte
    const initializeMap = () => {
      if (map.value) return;
      map.value = L.map('map').setView([46.8182, 8.2275], 8);
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
          serviceUrl: "https://routing.openstreetmap.de/routed-foot/route/v1"
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

          const slug = endroit.nom.toLowerCase().replace(/\s+/g, '-');
          marker.bindPopup(`
            <b>${endroit.nom}</b><br>
            <a href="/lieux/${slug}" style="color: #4a8c2a">Plus d'infos</a>
          `);

          return marker;
        }
      }).addTo(map.value);
    };

    // Fonction pour faire défiler jusqu'à la carte
    const scrollToMap = () => {
      if (mapContainer.value) {
        mapContainer.value.scrollIntoView({ behavior: 'smooth' });
      }
    };

    // Calcule la durée formatée
    const formattedDuration = computed(() => {
      const minutes = props.sentier.duree;
      if (minutes >= 60) {
        const hours = Math.floor(minutes / 60);
        const remainingMinutes = minutes % 60;
        return remainingMinutes === 0 ? `${hours}h` : `${hours}h ${remainingMinutes}min`;
      }
      return `${minutes} min`;
    });

    // Supprime le sentier après confirmation de l'utilisateur
    const deleteSentier = async () => {
      try {
        if (isAuthenticated.value === false) return window.location.href = '/login';
        else if (!confirm('Êtes-vous sûr de vouloir supprimer ce sentier ?')) return;
        const response = await axios.delete(`/api/sentiers/${props.sentier.id}`);
        if (response.status === 200) {
          window.location.href = '/sentiers';
        }
      } catch (error) {
        console.error('Erreur lors de la suppression du sentier:', error);
      }
    };

    // Vérifie l'authentification de l'utilisateur
    const checkAuthentication = async () => {
      try {
        const response = await axios.get('/api/user');
        isAuthenticated.value = response.data.authenticated;
      } catch (error) {
        console.error('Erreur lors de la vérification de l\'authentification:', error);
        isAuthenticated.value = false;
      }
    };

    // Redirige vers la page de modification du sentier
    const editSentier = () => {
      window.location.href = `/sentiers/${props.sentier.nom}/edit`;
    };

    // Initialise la carte et vérifie l'authentification lorsque le composant est monté
    onMounted(async () => {
      await checkAuthentication();
      initializeMap();
    });

    return {
      mapContainer,
      scrollToMap,
      formattedDuration,
      isAuthenticated,
      editSentier,
      deleteSentier
    };
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
  margin-bottom: 24px;
}

.info .nav-icon {
  margin-right: 10px;
  width: 24px;
  height: 24px;
}

.parcours {
  margin: 0;
}


.description {
  margin: 20px 0;
}

.map-container {
  width: 100%;
  height: 400px;
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
}

.map-button:hover {
  background-color: #fafafa;
  color: #4a8c2a;
  border: #4a8c2a solid 1px;
  cursor: pointer;
}

.edit-button-wrapper {
  display: flex;
  justify-content: center;
  margin: 20px 0;
}

.edit-button {
  background-color: #4a8c2a;
  color: #fafafa;
  text-transform: uppercase;
  font-weight: 500;
  padding: 10px 20px;
  border-radius: 20px;
  border: #4a8c2a solid 1px;
  cursor: pointer;
}

.edit-button:hover {
  background-color: #fafafa;
  color: #4a8c2a;
  border: #4a8c2a solid 1px;
  cursor: pointer;
}

.button-wrapper {
  display: flex;
  justify-content: center;
  gap: 10px; /* Ajouter un espace entre les boutons */
  margin: 20px 0;
}

.delete-button {
  background-color: #ff4d4d;
  color: #fafafa;
  text-transform: uppercase;
  font-weight: 500;
  padding: 10px 20px;
  border-radius: 20px;
  border: #ff4d4d solid 1px;
  cursor: pointer;
  /* Supprimer la marge gauche ici */
}

.delete-button:hover {
  background-color: #fafafa;
  color: #ff4d4d;
  border: #ff4d4d solid 1px;
  cursor: pointer;
}
</style>
