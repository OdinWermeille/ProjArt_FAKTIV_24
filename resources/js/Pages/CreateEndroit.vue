<template>
  <div class="group-parent inter-text">
    <div class="group-container">
      <div class="group-child"></div>
      <div class="rectangle-wrapper">
        <h2 class="ajouter-un-lieu">Ajouter un lieu</h2>
        <form v-if="isAuthenticated" @submit.prevent="submitForm" enctype="multipart/form-data">
          <div class="input-group">
            <input class="group-item" type="text" v-model="form.nom" id="nom" placeholder="Nom" required>
          </div>
          <div class="input-group">
            <textarea class="group-item description-field" v-model="form.description" id="description" placeholder="Description" required></textarea>
          </div>
          <div class="input-group">
            <input class="group-item" type="text" v-model="form.localite" id="localite" placeholder="Localité" required>
          </div>
          <div class="input-group image-upload">
            <div class="rectangle-parent">
              <div class="group-child"></div>
              <label class="supporting-text" for="image">Image</label>
              <input class="image-input" type="file" @change="onFileChange" id="image" required>
            </div>
          </div>
          <div class="input-group map-container">
            <div id="map" class="rectangle-parent2"></div>
          </div>
          <div class="ajouter-wrapper">
            <button type="submit" class="ajouter">Créer</button>
          </div>
        </form>
        <div v-else>
          <p class="ajouter-un-lieu">Veuillez vous connecter pour créer un endroit.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, nextTick } from 'vue';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

export default {
  name: 'CreatePlace',
  setup() {
    const isAuthenticated = ref(false);
    const form = ref({
      nom: '',
      description: '',
      localite: '',
      coordonneesX: null,
      coordonneesY: null,
      image: null,
      user_id: null,
    });

    const userCoords = ref({ latitude: Number.POSITIVE_INFINITY, longitude: Number.POSITIVE_INFINITY });

    const checkAuthentication = async () => {
      try {
        const response = await axios.get('/api/user'); // Assurez-vous que cette route existe
        isAuthenticated.value = response.data.authenticated;
        form.value.user_id = response.data.user.id;
      } catch (error) {
        console.error('Erreur lors de la vérification de l\'authentification:', error);
        isAuthenticated.value = false;
      }
    };

    onMounted(async () => {
      await checkAuthentication();
      await nextTick();

      const map = L.map('map').setView([46.8182, 8.2275], 8); // Centrer la carte sur la Suisse

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      let marker;

      // Obtenir la géolocalisation de l'utilisateur
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          userCoords.value.latitude = position.coords.latitude;
          userCoords.value.longitude = position.coords.longitude;

          map.setView([userCoords.value.latitude, userCoords.value.longitude], 13);
        });
      }

      map.on('click', function(e) {
        const { lat, lng } = e.latlng;

        if (marker) {
          map.removeLayer(marker);
        }

        marker = L.marker([lat, lng]).addTo(map)
          .bindPopup(`Lieu sélectionné: ${lat}, ${lng}`)
          .openPopup();

        form.value.coordonneesX = lng;
        form.value.coordonneesY = lat;
      });
    });

    const onFileChange = (e) => {
      form.value.image = e.target.files[0];
    };

    const submitForm = async () => {
      const formData = new FormData();
      formData.append('nom', form.value.nom);
      formData.append('description', form.value.description);
      formData.append('localite', form.value.localite);
      formData.append('coordonneesX', form.value.coordonneesX);
      formData.append('coordonneesY', form.value.coordonneesY);
      formData.append('image', form.value.image);
      formData.append('user_id', form.value.user_id);

      try {
        const response = await axios.post('/api/endroits', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        console.log('Endroit créé avec succès:', response.data);
      } catch (error) {
        console.error('Erreur lors de la création de l\'endroit:', error);
      }
    };

    return {
      form,
      isAuthenticated,
      onFileChange,
      submitForm,
      userCoords
    };
  }
};
</script>

<style scoped>
.inter-text {
  font-family: "Inter", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-variation-settings: "slnt" 0;
}

.group-child {
  position: absolute;
  top: 0px;
  left: 0px;
  border-radius: 11px;
  background-color: #f0f0f0;
  width: 100%;
  height: 100%;
  border: none; /* Enlever le contour */
}
.rectangle-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  padding: 30px 15px; /* Ajouter plus de padding en haut et en bas */
  box-sizing: border-box;
}
.ajouter-un-lieu {
  font-size: 18px;
  font-weight: bold; /* Mettre le titre en gras */
  letter-spacing: 0.5px;
  line-height: 24px;
  display: flex;
  font-family: "Inter", sans-serif;
  color: #212121;
  text-align: center;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}
.input-group {
  margin-bottom: 20px; /* Augmenter la marge entre les champs */
}
.group-item {
  border-radius: 10px;
  border: 1px solid #7d7d7d;
  box-sizing: border-box;
  width: calc(100% - 32px); /* Ajouter de la marge sur les côtés */
  height: 40px;
  padding: 12px; /* Ajouter un padding pour un espacement uniforme */
  font-size: 16px;
  font-family: "Inter", sans-serif;
  background-color: transparent; /* Enlever le fond blanc */
  margin: 0 16px; /* Ajouter de la marge sur les côtés */
}
.description-field {
  height: 100px;
  padding: 12px; /* Ajouter un padding pour un espacement uniforme */
  margin: 0 16px; /* Ajouter de la marge sur les côtés */
}
.rectangle-parent {
  position: relative;
  height: 40px;
  margin: 0 16px; /* Ajouter de la marge sur les côtés */
}
.group-child {
  position: absolute;
  top: 0;
  left: 0;
  border-radius: 10px;
  box-sizing: border-box;
  width: 100%;
  height: 100%;
  border: none; /* Enlever le contour */
}
.image-upload .group-child {
  border: 1px solid #7d7d7d; /* Ajouter un contour au champ d'insert d'image */
}
.supporting-text {
  position: absolute;
  top: 8px;
  left: 18px; /* Ajouter un peu plus de marge à gauche entre l'image et le bord du champ */
  letter-spacing: 0.5px;
  line-height: 24px;
  display: flex;
  align-items: center;
  width: 170px;
  height: 25px;
  background: url('/images/IconeImage.png') no-repeat left center;
  padding-left: 30px; /* Ajuster pour fournir plus d'espace pour l'icône */
}
.image-input {
  opacity: 0;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  cursor: pointer;
}
.rectangle-parent2 {
  height: 250px; /* Rendre la carte un peu plus petite */
  width: calc(100% - 32px); /* Garder la largeur adaptée à l'écran et ajouter de la marge sur les côtés */
  margin: 0 16px; /* Ajouter de la marge sur les côtés */
}
.ajouter {
  position: relative;
  text-transform: uppercase;
  font-weight: 500;
}
.ajouter-wrapper {
  border-radius: 20px;
  background-color: #4a8c2a;
  border: 1px solid #bfd2a6;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  text-align: center;
  font-size: 14px;
  color: #fafafa;
  margin: 30px auto 0 auto;
  width: fit-content;
}
.group-container {
  position: relative;
  top: 0px;
  left: 0px;
  width: 100%;
  height: 100%;
}
.group-parent {
  width: 100%;
  position: relative;
  height: auto;
  text-align: left;
  font-size: 16px;
  color: #7d7d7d;
  font-family: "Inter", sans-serif;
  padding: 20px;
  box-sizing: border-box;
}
body {
  margin: 0;
  line-height: normal;
  font-family: "Inter", sans-serif;
}
</style>
