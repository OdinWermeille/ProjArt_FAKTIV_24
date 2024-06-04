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
          <div class="input-group image-upload">
            <div class="rectangle-parent">
              <div class="group-child"></div>
              <label class="supporting-text" :for="image">{{ truncatedFileName || 'Image' }}</label>
              <input class="image-input" type="file" @change="onFileChange" id="image" required>
            </div>
          </div>
          <div class="input-group">
            <textarea class="group-item description-field" v-model="form.description" id="description" placeholder="Description" required></textarea>
          </div>
          <div class="input-group">
            <input class="group-item" type="number" v-model="form.longueur" id="longueur" placeholder="Longueur (en km)" required>
          </div>
          <div class="input-group">
            <input class="group-item" type="number" v-model="form.duree" id="duree" placeholder="Durée (en minutes)" required>
          </div>
          <div class="input-group">
            <div class="group-item dropdown-multi">
              <div class="dropdown-header" @click="toggleThemeDropdown">
                {{ selectedThemeText }}
              </div>
              <span class="arrow-down" @click="toggleThemeDropdown"></span>
              <div v-if="themeDropdownOpen" class="dropdown-list theme-dropdown-list">
                <label v-for="theme in themes" :key="theme.id" class="dropdown-list-item">
                  <input type="radio" :value="theme.id" v-model="form.theme_id" @click.stop />
                  {{ theme.nom }}
                </label>
              </div>
            </div>
          </div>
          <div class="input-group">
            <div class="group-item dropdown-multi">
              <div class="dropdown-header" @click="toggleDropdown">
                {{ selectedEndroitsText }}
              </div>
              <span class="arrow-down" @click="toggleDropdown"></span>
              <div v-if="dropdownOpen" class="dropdown-list">
                <input type="text" v-model="search" class="dropdown-search" placeholder="Rechercher..." @click.stop />
                <label v-for="endroit in filteredEndroits" :key="endroit.id" class="dropdown-list-item">
                  <input type="checkbox" :value="endroit.id" v-model="form.endroits" @change="updateMap" @click.stop />
                  {{ endroit.nom }}
                </label>
                <a href="/endroits/create" class="create-new-endroit">Créer un lieu ici</a>
              </div>
            </div>
          </div>

          <!-- Titre conditionnel centré -->
          <div v-if="form.endroits.length > 0" class="order-title">
            <h3>Choisir l'ordre des lieux</h3>
          </div>

          <!-- Ajouter les éléments cochés ici -->
          <draggable
            v-if="form.endroits.length > 0"
            v-model="form.endroits"
            class="draggable-list"
            item-key="id"
            @start="onDragStart"
            @end="onDragEnd"
            @update="onDragUpdate"
            @change="onDragChange"
          >
            <template #item="{ element }">
              <div class="draggable-item">
                <i class="fas fa-grip-vertical grip-icon"></i> <!-- Icône de grip -->
                {{ getEndroitName(element) }}
              </div>
            </template>
          </draggable>

          <div v-if="form.endroits.length > 0" class="order-title">
            <h3>Aperçu du sentier</h3>
          </div>

          <div class="input-group map-container">
            <div id="map" class="rectangle-parent2"></div>
          </div>
          <div class="ajouter-wrapper">
            <button type="submit" class="ajouter">Créer</button>
          </div>
        </form>
      </div>
    </div>
    <custom-popup
      :title="popupTitle"
      :message="popupMessage"
      :visible="popupVisible"
      @close="popupVisible = false"
    />
  </div>
</template>

<script>
import { ref, onMounted, nextTick, computed, watch } from 'vue';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import draggable from 'vuedraggable';
import { Inertia } from '@inertiajs/inertia';
import 'leaflet-routing-machine';
import CustomPopup from '../Components/CustomPopup.vue';

export default {
  name: 'CreatePlace',
  components: {
    CustomPopup
  },
  setup() {
    const isAuthenticated = ref(false);
    const form = ref({
      nom: '',
      description: '',
      coordonneesX: null,
      coordonneesY: null,
      image: null,
      user_id: null,
    });

    const fileName = ref('');
    const userCoords = ref({ latitude: Number.POSITIVE_INFINITY, longitude: Number.POSITIVE_INFINITY });
    let marker = ref(null);

    const popupTitle = ref('');
    const popupMessage = ref('');
    const popupVisible = ref(false);

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
      if (isAuthenticated.value) {
        await fetchThemesAndEndroits();
      }
      await nextTick();

      const map = L.map('map').setView([46.8182, 8.2275], 8); // Centrer la carte sur la Suisse

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map.value);

      routingControl.value = L.Routing.control({
        waypoints: [],
        routeWhileDragging: true,
        show: false,
        addWaypoints: false,
        draggableWaypoints: false,
        createMarker: function(i, waypoint) {
          const marker = L.marker(waypoint.latLng, {
            draggable: true
          });

          return marker;
        },
        lineOptions: {
          styles: [{ color: 'blue', opacity: 1, weight: 5 }]
        },
        fitSelectedRoutes: true,
        routeWhileDragging: true,
        showAlternatives: false
      }).addTo(map.value);

      // Obtenir la géolocalisation de l'utilisateur
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition((position) => {
          const { latitude, longitude } = position.coords;
          map.value.setView([latitude, longitude], 13);
        });
      }
    });

    const onFileChange = (e) => {
      const file = e.target.files[0];
      form.value.image = file;
      fileName.value = file ? file.name : '';
      console.log('Fichier sélectionné :', fileName.value); // Debug
    };

    const truncatedFileName = computed(() => {
      const maxLength = 20; // Maximum length of the file name to display
      if (fileName.value.length > maxLength) {
        return fileName.value.substring(0, maxLength) + '...';
      }
      return fileName.value;
    });

    const resetForm = () => {
      form.value = {
        nom: '',
        description: '',
        coordonneesX: null,
        coordonneesY: null,
        image: null,
        user_id: form.value.user_id, // Préserver user_id
      };
      imageLabel.value = 'Image';
      routingControl.value.setWaypoints([]);
    };

    const submitForm = async () => {
      const formData = new FormData();
      formData.append('nom', form.value.nom);
      formData.append('description', form.value.description);
      formData.append('coordonneesX', form.value.coordonneesX);
      formData.append('coordonneesY', form.value.coordonneesY);
      formData.append('image', form.value.image);
      formData.append('user_id', form.value.user_id);
      formData.append('localite', form.value.localite); // Ajouter localité ici

      try {
        const response = await axios.post('/api/endroits', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        console.log('Endroit créé avec succès:', response.data);
        popupTitle.value = 'Merci!';
        popupMessage.value = 'Votre endroit a été créé avec succès!';
        popupVisible.value = true;
        resetForm();
      } catch (error) {
        console.error('Erreur lors de la création de l\'endroit:', error);
        popupTitle.value = 'Erreur!';
        popupMessage.value = 'Il y a eu une erreur lors de la création de votre endroit.';
        popupVisible.value = true;
      }
    };

    const updateMap = () => {
      const selectedEndroits = form.value.endroits.map(endroitId => {
        return endroits.value.find(endroit => endroit.id === endroitId);
      });

      routingControl.value.setWaypoints(
        selectedEndroits.map(endroit => L.latLng(endroit.coordonneesX, endroit.coordonneesY))
      );
    };

    const toggleDropdown = () => {
      if (!dropdownOpen.value) {
        themeDropdownOpen.value = false; // Fermer l'autre liste déroulante
      }
      dropdownOpen.value = !dropdownOpen.value;
    };

    const toggleThemeDropdown = () => {
      if (!themeDropdownOpen.value) {
        dropdownOpen.value = false; // Fermer l'autre liste déroulante
      }
      themeDropdownOpen.value = !themeDropdownOpen.value;
    };

    const selectedThemeText = computed(() => {
      const selectedTheme = themes.value.find(theme => theme.id === form.value.theme_id);
      return selectedTheme ? selectedTheme.nom : "Thématique";
    });

    const selectedEndroitsText = computed(() => {
      if (form.value.endroits.length === 0) return "Endroits";
      const selectedNames = endroits.value
        .filter(endroit => form.value.endroits.includes(endroit.id))
        .map(endroit => endroit.nom)
        .join(", ");
      return selectedNames;
    });

    const filteredEndroits = computed(() => {
      if (!search.value) {
        return endroits.value;
      }
      return endroits.value.filter(endroit => endroit.nom.toLowerCase().includes(search.value.toLowerCase()));
    });

    const onDragStart = (event) => {
      console.log('Drag start:', event);
    };

    const onDragEnd = (event) => {
      console.log('Drag end:', event);
    };

    const onDragUpdate = (event) => {
      console.log('Drag update:', event);
      updateMap(); // Mettre à jour la carte lorsque l'ordre des endroits est modifié
    };

    const onDragChange = (event) => {
      console.log('Drag change:', event);
      const { oldIndex, newIndex } = event;
      if (oldIndex !== undefined && newIndex !== undefined) {
        const movedItem = form.value.endroits.splice(oldIndex, 1)[0];
        form.value.endroits.splice(newIndex, 0, movedItem);
        console.log(`Moved item from index ${oldIndex} to ${newIndex}`);
        console.log('New order:', form.value.endroits);
        updateMap(); // Mettre à jour la carte lorsque l'ordre des endroits est modifié
      }
    };

    const handleClickOutside = (event) => {
      if (!event.target.closest('.dropdown-multi')) {
        dropdownOpen.value = false;
        themeDropdownOpen.value = false;
      }
    };

    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
    });

    const getEndroitName = (id) => {
      const endroit = endroits.value.find(e => e.id === id);
      return endroit ? endroit.nom : '';
    };

    const popupTitle = ref('');
    const popupMessage = ref('');
    const popupVisible = ref(false);

    return {
      form,
      isAuthenticated,
      onFileChange,
      submitForm,
      userCoords,
      popupTitle,
      popupMessage,
      popupVisible,
      fileName,
      truncatedFileName
    };
  }
};
</script>

<style scoped>@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'); /* Import Font Awesome */

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

.file-name-input {
  margin-top: 5px;
  margin-left: 16px;
  font-size: 14px;
  color: #7d7d7d;
  font-family: "Inter", sans-serif;
  width: calc(100% - 32px);
  border: none;
  background-color: transparent;
  cursor: default;
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
  width: calc(100% - 36px); /* Ajuster pour fournir plus d'espace pour le texte */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding-left: 30px; /* Ajuster pour fournir plus d'espace pour l'icône */
  background: url('/images/icon_televerser_img.svg') no-repeat left center;
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
  max-width: 700px; /* Limiter la largeur maximale */
  margin: 0 auto; /* Centrer le formulaire */
}

body {
  margin: 0;
  line-height: normal;
  font-family: "Inter", sans-serif;
}

.radio-group {
  margin: 0 16px;
}

.checkbox-group {
  margin: 0 16px;
}

.order-title {
  display: flex;
  justify-content: center;
  margin: 20px 0 10px;
  font-size: 18px;
  font-weight: bold;
  color: #212121;
}

.draggable-list {
  margin: 20px 0;
  padding: 0;
  list-style-type: none;
}

.draggable-item {
  display: flex;
  align-items: center;
  padding: 10px;
  margin: 5px 0;
  border: 1px solid #ddd;
  border-radius: 4px;
  background-color: #f9f9f9;
  cursor: grab; /* Change the cursor to a hand when hovering over the item */
}

.draggable-item:active {
  cursor: grabbing; /* Change the cursor to a closed hand when dragging the item */
}

.grip-icon {
  margin-right: 10px; /* Add some space between the icon and the text */
  font-size: 14px; /* Adjust the size of the icon to make it smaller */
  color: #7d7d7d; /* Use a color that fits your design */
  line-height: 1; /* Ensure the dots are vertically centered */
}

.alert-box {
  background-color: #f5e3cb;
  border: 1px solid #e4c29e;
  border-radius: 10px;
  padding: 15px;
  margin: 20px;
  text-align: center;
}

.alert-box p {
  display: inline;
  margin: 0;
  font-family: "Inter", sans-serif;
  font-size: 14px;
  color: black; /* Change text color to black */
}

.alert-box a {
  color: black; /* Change link color to black */
  text-decoration: underline;
}

.alert-box a:hover {
  text-decoration: underline;
}

.create-new-endroit {
  display: inline;
  text-align: center;
  color: black; /* Change link color to black */
  text-decoration: underline;
  margin-left: 10px;
}

.create-new-endroit:hover {
  text-decoration: underline;
}

a {
  color: black;
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
  cursor: pointer;
}

</style>
