<template>
  <div class="group-parent inter-text">
    <div class="group-container">
      <div class="group-child"></div>
      <div class="rectangle-wrapper">
        <h2 class="ajouter-un-lieu">Ajouter un lieu</h2>
        <!-- Formulaire pour ajouter un lieu, affiché uniquement si l'utilisateur est authentifié -->
        <form v-if="isAuthenticated" @submit.prevent="submitForm" enctype="multipart/form-data">
          <!-- Champ pour le nom du lieu avec validation -->
          <div class="input-group">
            <input :class="{ 'input-error': errors.nom }" class="group-item" type="text" v-model="form.nom" @input="validateNom" id="nom" placeholder="Nom">
            <span v-if="errors.nom" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.nom }}</span>
          </div>
          <!-- Champ pour la description du lieu -->
          <div class="input-group">
            <textarea :class="{ 'input-error': errors.description }" class="group-item description-field" v-model="form.description" id="description" placeholder="Description"></textarea>
            <span v-if="errors.description" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.description }}</span>
          </div>
          <!-- Champ pour télécharger une image avec validation de la taille du fichier -->
          <div class="input-group image-upload">
            <div :class="{ 'input-error': errors.image || isFileTooLarge }" class="rectangle-parent">
              <div class="group-child"></div>
              <label :class="{ 'label-error': isFileTooLarge }" class="supporting-text" for="image">
                {{ truncatedFileName }} ({{ imageSize }} / {{ maxFileSize }} KB max)
              </label>
              <input class="image-input" type="file" @change="onFileChange" id="image" accept="image/jpeg, image/png, image/jpg, image/gif, image/svg+xml">
            </div>
            <span v-if="errors.image" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.image }}</span>
            <span v-if="isFileTooLarge" class="error-message"><i class="fas fa-exclamation-circle"></i>Le fichier est trop lourd.</span>
          </div>
          <!-- Section pour placer le lieu sur la carte -->
          <div class="input-group map-container">
            <h3 class="placer-le-lieu">Placer le lieu sur la carte</h3>
            <div id="map" class="rectangle-parent2"></div>
            <span v-if="errors.coordonneesX || errors.coordonneesY" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.coordonneesX || errors.coordonneesY }}</span>
          </div>
          <!-- Bouton pour soumettre le formulaire -->
          <div class="ajouter-wrapper">
            <button type="submit" class="ajouter">Créer</button>
          </div>
        </form>
      </div>
    </div>
    <!-- Composant personnalisé pour afficher des popups -->
    <custom-popup :title="popupTitle" :message="popupMessage" :visible="popupVisible" @close="popupVisible = false" :actions="popupActions" />
  </div>
</template>

<script>
import { ref, onMounted, nextTick, computed } from 'vue';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.awesome-markers';
import 'leaflet.awesome-markers/dist/leaflet.awesome-markers.css';
import CustomPopup from '../Components/CustomPopup.vue';

export default {
  name: 'CreatePlace',
  components: {
    CustomPopup
  },
  setup() {
    // Références réactives et variables
    const isAuthenticated = ref(false);
    const form = ref({
      nom: '',
      description: '',
      coordonneesX: null,
      coordonneesY: null,
      image: null,
      user_id: null,
      localite: '',
    });
    const errors = ref({});
    const maxFileSize = 2048;
    const isFileTooLarge = ref(false);
    const imageSize = ref(0);
    const fileName = ref('');
    const userCoords = ref({ latitude: Number.POSITIVE_INFINITY, longitude: Number.POSITIVE_INFINITY });
    let marker = ref(null);

    const popupTitle = ref('');
    const popupMessage = ref('');
    const popupVisible = ref(false);
    const popupActions = ref([]);

    // Vérifie si l'utilisateur est authentifié
    const checkAuthentication = async () => {
      try {
        const response = await axios.get('/api/user');
        isAuthenticated.value = response.data.authenticated;
        form.value.user_id = response.data.user.id;
      } catch (error) {
        console.error('Erreur lors de la vérification de l\'authentification:', error);
        isAuthenticated.value = false;
      }
    };

    // Initialisation de la carte et récupération des coordonnées utilisateur
    onMounted(async () => {
      await checkAuthentication();
      await nextTick();

      const map = L.map('map').setView([46.8182, 8.2275], 8);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map);

      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          userCoords.value.latitude = position.coords.latitude;
          userCoords.value.longitude = position.coords.longitude;

          map.setView([userCoords.value.latitude, userCoords.value.longitude], 13);
        });
      }

      // Gestion des clics sur la carte pour placer le marqueur
      map.on('click', async function (e) {
        const { lat, lng } = e.latlng;

        const customIcon = L.AwesomeMarkers.icon({
          icon: 'info-sign',
          markerColor: 'black',
          prefix: 'glyphicon'
        });

        if (marker.value) {
          map.removeLayer(marker.value);
        }

        marker.value = L.marker([lat, lng], { icon: customIcon });
        marker.value.addTo(map);

        form.value.coordonneesX = lat;
        form.value.coordonneesY = lng;

        // Obtenir la localité en utilisant les coordonnées
        try {
          const response = await axios.get(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`);
          form.value.localite = response.data.address.city || response.data.address.town || response.data.address.village || response.data.address.hamlet || 'Localité inconnue';
        } catch (error) {
          console.error('Erreur lors de l\'obtention de la localité:', error);
          form.value.localite = 'Localité inconnue';
        }
      });
    });

    // Gestion du changement de fichier
    const onFileChange = (e) => {
      const file = e.target.files[0];
      form.value.image = file;
      imageSize.value = (file.size / 1024).toFixed(2);
      isFileTooLarge.value = file.size / 1024 > maxFileSize;
      fileName.value = file ? file.name : '';
    };

    // Troncature du nom du fichier si trop long
    const truncatedFileName = computed(() => {
      const maxLength = 30;
      if (fileName.value.length > maxLength) {
        return fileName.value.substring(0, maxLength) + '...';
      }
      return fileName.value;
    });

    // Réinitialisation du formulaire
    const resetForm = () => {
      form.value = {
        nom: '',
        description: '',
        coordonneesX: null,
        coordonneesY: null,
        image: null,
        user_id: form.value.user_id,
        localite: '',
      };
      fileName.value = '';
      isFileTooLarge.value = false;
      imageSize.value = 0;
      if (marker.value) {
        marker.value.remove();
        marker.value = null;
      }
    };

    // Nettoyage des messages d'erreur
    const cleanErrors = (errors) => {
      const cleanedErrors = {};
      for (const key in errors) {
        cleanedErrors[key] = errors[key].join(' ');
      }
      return cleanedErrors;
    };

    // Validation du formulaire
    const validateForm = () => {
      const validationErrors = {};
      if (!form.value.coordonneesX || !form.value.coordonneesY) {
        validationErrors.coordonneesX = 'Veuillez placer un point sur la carte.';
      }
      if (!form.value.nom) {
        validationErrors.nom = 'Le nom est requis.';
      } else if (form.value.nom.includes('-')) {
        validationErrors.nom = 'Le nom ne doit pas contenir de tirets (-).';
      }
      if (!form.value.description) {
        validationErrors.description = 'La description est requise.';
      }
      if (!form.value.image) {
        validationErrors.image = 'L\'image est requise.';
      } else if (isFileTooLarge.value) {
        validationErrors.image = 'Le fichier est trop lourd.';
      }
      return validationErrors;
    };

    // Validation du nom en temps réel
    const validateNom = () => {
      if (form.value.nom.includes('-')) {
        errors.value.nom = 'Le nom ne doit pas contenir de tirets (-).';
      } else {
        errors.value.nom = '';
      }
    };

    // Soumission du formulaire
    const submitForm = async () => {
      errors.value = validateForm();
      if (Object.keys(errors.value).length > 0) {
        return;
      }

      const formData = new FormData();
      formData.append('nom', form.value.nom);
      formData.append('description', form.value.description);
      formData.append('coordonneesX', form.value.coordonneesX);
      formData.append('coordonneesY', form.value.coordonneesY);
      formData.append('image', form.value.image);
      formData.append('user_id', form.value.user_id);
      formData.append('localite', form.value.localite);

      // Envoi des données au serveur
      try {
        const response = await axios.post('/api/endroits', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        popupTitle.value = 'Merci!';
        popupMessage.value = 'Votre lieu a été créé avec succès!';
        popupActions.value = [
          {
            text: 'Créer un sentier',
            handler: () => window.location.href = '/sentiers/create'
          }
        ];
        popupVisible.value = true;
        resetForm();
      } catch (error) {
        console.error('Erreur lors de la création du lieu:', error);
        if (error.response && error.response.data.errors) {
          errors.value = cleanErrors(error.response.data.errors);
        } else {
          popupTitle.value = 'Erreur!';
          popupMessage.value = 'Il y a eu une erreur lors de la création de votre lieu.';
          popupActions.value = [];
          popupVisible.value = true;
        }
      }
    };

    return {
      form,
      isAuthenticated,
      onFileChange,
      submitForm,
      validateNom,
      userCoords,
      popupTitle,
      popupMessage,
      popupVisible,
      fileName,
      truncatedFileName,
      errors,
      isFileTooLarge,
      imageSize,
      maxFileSize,
      popupActions
    };
  }
};
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
@import url('https://unpkg.com/leaflet/dist/leaflet.css');
@import url('https://unpkg.com/leaflet.awesome-markers/dist/leaflet.awesome-markers.css');

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
  border: none;
}

.rectangle-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  padding: 30px 15px;
  box-sizing: border-box;
}

.ajouter-un-lieu {
  font-size: 18px;
  font-weight: bold;
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
  margin-bottom: 20px;
}

.input-error .group-item {
  border-color: red;
}

.input-error .rectangle-parent {
  border-color: red;
}

.group-item {
  border-radius: 10px;
  border: 1px solid #7d7d7d;
  box-sizing: border-box;
  width: calc(100% - 32px);
  height: 50px;
  padding: 12px;
  font-size: 16px;
  font-family: "Inter", sans-serif;
  background-color: transparent;
  margin: 0 16px;
}

.description-field {
  height: 100px;
  padding: 12px;
  margin: 0 16px;
}

.rectangle-parent {
  position: relative;
  height: 40px;
  margin: 0 16px;
}

.image-upload .group-child {
  border: 1px solid #7d7d7d;
}

.supporting-text {
  position: absolute;
  top: 8px;
  left: 18px;
  letter-spacing: 0.5px;
  line-height: 24px;
  display: flex;
  align-items: center;
  width: calc(100% - 36px);
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding-left: 30px;
  background: url('/storage/images/icon_televerser_img.svg') no-repeat left center;
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
  height: 250px;
  width: calc(100% - 32px);
  margin: 0 16px;
}

.placer-le-lieu {
  font-size: 16px;
  font-weight: bold;
  letter-spacing: 0.5px;
  line-height: 24px;
  display: flex;
  font-family: "Inter", sans-serif;
  color: #212121;
  text-align: center;
  align-items: center;
  justify-content: center;
  margin: 0 16px;
  margin-bottom: 12px;
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
  cursor: pointer;
}

.ajouter-wrapper:hover {
    background-color: #fafafa;
    color: #4a8c2a;
    border: #4a8c2a solid 1px;
    cursor: pointer;
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
  max-width: 700px;
  margin: 0 auto;
}

body {
  margin: 0;
  line-height: normal;
  font-family: "Inter", sans-serif;
}

.error-message {
  color: red;
  font-size: 12px;
  margin: 0 16px;
  margin-top: 5px;
  display: flex;
  align-items: center;
}

.error-message i {
  margin-right: 5px;
}

.label-error {
  color: red;
}
</style>
