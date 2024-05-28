<template>
    <div>
      <h2>Créer un nouvel endroit</h2>
      <form v-if="isAuthenticated" @submit.prevent="submitForm" enctype="multipart/form-data">
        <div>
          <label for="nom">Nom:</label>
          <input type="text" v-model="form.nom" id="nom" required>
        </div>
        <div>
          <label for="description">Description:</label>
          <textarea v-model="form.description" id="description" required></textarea>
        </div>
        <div>
          <label for="localite">Localité:</label>
          <input type="text" v-model="form.localite" id="localite" required>
        </div>
        <div>
          <label for="image">Image:</label>
          <input type="file" @change="onFileChange" id="image" required>
        </div>
        <div id="map" style="height: 400px; margin-top: 20px;"></div>
        <button type="submit">Créer</button>
      </form>
      <div v-else>
        <p>Veuillez vous connecter pour créer un endroit.</p>
      </div>
    </div>
  </template>
  
  <script>
  import { ref, onMounted, nextTick } from 'vue';
  import axios from 'axios';
  import L from 'leaflet';
  import 'leaflet/dist/leaflet.css';
  
  export default {
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
  
        // Utiliser nextTick pour s'assurer que le DOM est complètement rendu
        await nextTick();
  
        const map = L.map('map').setView([46.8182, 8.2275], 8); // Centrer la carte sur la Suisse
  
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          maxZoom: 19,
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
  
        let marker;
  
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
        submitForm
      };
    }
  };
  </script>
  
  <style scoped>
  #map {
    width: 100%;
    height: 400px;
  }
  </style>
  