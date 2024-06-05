<template>
  <div class="group-parent inter-text">
    <div class="alert-box">
      <p><strong>Attention</strong>, pour créer un sentier, vous devez d'abord créer les différents lieux qui
        composeront le sentier.</p>
      <div class="create-new-endroit-container-intro">
        <a href="/endroits/create" class="create-new-endroit-intro">
          Créer un lieu <span class="underline-intro">ici</span>
        </a>
      </div>
    </div>


    <div class="group-container">
      <div class="group-child"></div>
      <div class="rectangle-wrapper">
        <h2 class="ajouter-un-lieu">Ajouter un sentier</h2>
        <form v-if="isAuthenticated" @submit.prevent="submitForm" enctype="multipart/form-data">
          <div class="input-group">
            <input class="group-item" type="text" v-model="form.nom" id="nom" placeholder="Nom" required>
          </div>
          <div class="input-group image-upload">
            <div class="rectangle-parent">
              <div class="group-child"></div>
              <label class="supporting-text" for="image">{{ truncatedImageLabel }}</label>
              <input class="image-input" type="file" @change="onFileChange" id="image" required>
            </div>
          </div>
          <div class="input-group">
            <textarea class="group-item description-field" v-model="form.description" id="description"
              placeholder="Description" required></textarea>
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
                <div class="create-new-endroit-container">
                  <a href="/endroits/create" class="create-new-endroit">
                    <span class="plus-icon">+</span> Créer un lieu <span class="underline">ici</span>
                  </a>
                </div>



              </div>
            </div>
          </div>

          <!-- Titre conditionnel centré -->
          <div v-if="form.endroits.length > 0" class="order-title">
            <h3>Choisir l'ordre des lieux</h3>
          </div>

          <!-- Ajouter les éléments cochés ici -->
          <draggable v-if="form.endroits.length > 0" v-model="form.endroits" class="draggable-list" item-key="id"
            @start="onDragStart" @end="onDragEnd" @update="onDragUpdate" @change="onDragChange">
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

          <div class="input-group map-container" v-if="form.endroits.length > 0">
            <div id="map" class="rectangle-parent2"></div>
          </div>

          <div class="ajouter-wrapper">
            <button type="submit" class="ajouter">Créer</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <custom-popup :title="popupTitle" :message="popupMessage" :visible="popupVisible" @close="popupVisible = false" />
</template>


<script>
import { ref, onMounted, nextTick, computed, watch } from 'vue';
import axios from 'axios';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import draggable from 'vuedraggable';
import { Inertia } from '@inertiajs/inertia';
import 'leaflet-routing-machine';
import 'leaflet-routing-machine/dist/leaflet-routing-machine.css';
import CustomPopup from '../Components/CustomPopup.vue';

export default {
  name: 'CreateSentier',
  components: {
    draggable,
    CustomPopup
  },
  setup() {
    const themes = ref([]);
    const endroits = ref([]);
    const isAuthenticated = ref(false);
    const form = ref({
      nom: '',
      image: null,
      description: '',
      longueur: '',
      duree: '',
      theme_id: '',
      user_id: null,
      endroits: []
    });

    const dropdownOpen = ref(false);
    const themeDropdownOpen = ref(false);
    const search = ref("");
    const imageLabel = ref('Image');
    let routingControl = ref(null);
    let map = ref(null);

    const fetchThemesAndEndroits = async () => {
      try {
        const themeResponse = await axios.get('/api/themes');
        themes.value = themeResponse.data;

        const endroitResponse = await axios.get('/api/endroits');
        endroits.value = endroitResponse.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des thématiques et des endroits:', error);
      }
    };

    const checkAuthentication = async () => {
      try {
        const response = await axios.get('/api/user');
        isAuthenticated.value = response.data.authenticated;
        form.value.user_id = response.data.user.id;
        if (!isAuthenticated.value) {
          Inertia.visit('/login', { replace: true });
        }
      } catch (error) {
        console.error('Erreur lors de la vérification de l\'authentification:', error);
        isAuthenticated.value = false;
        Inertia.visit('/login', { replace: true });
      }
    };

    const initializeMap = async () => {
      if (map.value) return;

      map.value = L.map('map').setView([46.8182, 8.2275], 8); // Centrer la carte sur la Suisse

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
        createMarker: function (i, waypoint) {
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
        showAlternatives: false,
        router: new L.Routing.OSRMv1({
          serviceUrl: 'http://routing.openstreetmap.de/routed-foot/route/v1'
        })
      }).addTo(map.value);

      routingControl.value.on('routesfound', function (e) {
        const route = e.routes[0];
        form.value.longueur = (route.summary.totalDistance / 1000).toFixed(2); // Convertir en km
        form.value.duree = Math.round(route.summary.totalTime / 60); // Convertir en minutes
      });
    };

    onMounted(async () => {
      await checkAuthentication();
      if (isAuthenticated.value) {
        await fetchThemesAndEndroits();
      }
    });

    watch(() => form.value.endroits, async (newEndroits) => {
      if (newEndroits.length > 0 && !map.value) {
        await nextTick();
        await initializeMap();
      }
      updateMap();
    }, { deep: true });

    const onFileChange = (e) => {
      const file = e.target.files[0];
      form.value.image = file;
      imageLabel.value = file ? file.name : 'Image';
    };

    const truncatedImageLabel = computed(() => {
      const maxLength = 30; // Maximum length of the file name to display
      if (imageLabel.value.length > maxLength) {
        return imageLabel.value.substring(0, maxLength) + '...';
      }
      return imageLabel.value;
    });

    const resetForm = () => {
      form.value = {
        nom: '',
        image: null,
        description: '',
        longueur: '',
        duree: '',
        theme_id: '',
        user_id: form.value.user_id,
        endroits: []
      };
      imageLabel.value = 'Image';
      if (routingControl.value) {
        routingControl.value.setWaypoints([]);
      }
    };

    const submitForm = async () => {
      const formData = new FormData();
      formData.append('nom', form.value.nom);
      formData.append('image', form.value.image);
      formData.append('description', form.value.description);
      formData.append('longueur', form.value.longueur);
      formData.append('duree', form.value.duree);
      formData.append('theme_id', form.value.theme_id);
      formData.append('user_id', form.value.user_id);

      form.value.endroits.forEach((endroit, index) => {
        formData.append(`endroits[${index}]`, endroit);
      });

      // Log formData for debugging
      for (let pair of formData.entries()) {
        console.log(pair[0] + ', ' + pair[1]);
      }

      try {
        const response = await axios.post('/api/sentiers', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        console.log('Sentier créé avec succès:', response.data);
        popupTitle.value = 'Merci!';
        popupMessage.value = 'Votre sentier a été créé avec succès!';
        popupVisible.value = true;
        resetForm();
      } catch (error) {
        console.error('Erreur lors de la création du sentier:', error);
        console.error('Erreur détails:', error.response.data); // Ajoutez ceci pour voir les détails de l'erreur
        popupTitle.value = 'Erreur!';
        popupMessage.value = 'Il y a eu une erreur lors de la création de votre sentier.';
        popupVisible.value = true;
      }
    };

    const updateMap = () => {
      if (!map.value) return;
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
      themes,
      endroits,
      isAuthenticated,
      dropdownOpen,
      themeDropdownOpen,
      imageLabel,
      onFileChange,
      submitForm,
      toggleDropdown,
      toggleThemeDropdown,
      selectedThemeText,
      selectedEndroitsText,
      search,
      filteredEndroits,
      onDragStart,
      onDragEnd,
      onDragUpdate,
      onDragChange,
      handleClickOutside,
      resetForm,
      getEndroitName,
      updateMap,
      truncatedImageLabel,
      popupTitle,
      popupMessage,
      popupVisible
    };
  }
};
</script>


<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
/* Import Font Awesome */

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
  /* Enlever le contour */
}

.rectangle-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  padding: 30px 15px;
  /* Ajouter plus de padding en haut et en bas */
  box-sizing: border-box;
}

.ajouter-un-lieu {
  font-size: 18px;
  font-weight: bold;
  /* Mettre le titre en gras */
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
  /* Augmenter la marge entre les champs */
}

.group-item {
  border-radius: 10px;
  border: 1px solid #7d7d7d;
  box-sizing: border-box;
  width: calc(100% - 32px);
  /* Ajouter de la marge sur les côtés */
  height: 50px;
  padding: 12px;
  /* Ajouter un padding pour un espacement uniforme */
  font-size: 16px;
  font-family: "Inter", sans-serif;
  background-color: transparent;
  /* Enlever le fond blanc */
  margin: 0 16px;
  /* Ajouter de la marge sur les côtés */
}

.group-item[readonly] {
  background-color: #e9e9e9;
  /* Light grey background for readonly inputs */
}

.dropdown-item {
  height: 50px;
  /* Agrandir la hauteur de la liste déroulante */
}

.dropdown-multi {
  position: relative;
  border: 1px solid #7d7d7d;
  border-radius: 10px;
  background-color: transparent;
  padding: 12px;
  font-size: 16px;
  width: calc(100% - 32px);
  margin: 0 16px;
  height: 50px;
  /* Ajuster la hauteur */
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer;
  /* Ajouter le curseur pointeur pour indiquer qu'il est cliquable */
}

.dropdown-header {
  width: calc(100% - 24px);
  /* Laisser de la place pour la flèche */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.dropdown-list {
  position: absolute;
  top: 100%;
  left: 0;
  width: 100%;
  background-color: white;
  border: 1px solid #7d7d7d;
  border-radius: 0 0 10px 10px;
  z-index: 1600;
  /* Modifier la valeur du z-index pour qu'elle soit plus élevée */
  max-height: 200px;
  /* Limiter la hauteur de la liste déroulante */
  overflow-y: auto;
  /* Ajouter un défilement si nécessaire */
}

.theme-dropdown-list {
  z-index: 1700;
  /* Ajouter un z-index plus élevé pour que la liste déroulante des thématiques soit au-dessus des autres éléments */
}

.dropdown-search {
  width: calc(100% - 24px);
  /* Laisser de la place pour la marge */
  margin: 8px 12px;
  padding: 8px 12px;
  border: 1px solid #7d7d7d;
  border-radius: 5px;
  box-sizing: border-box;
  font-family: "Inter", sans-serif;
}

.dropdown-list-item {
  display: flex;
  align-items: center;
  padding: 8px 12px;
}

.dropdown-list-item input {
  margin-right: 8px;
}

.create-new-endroit {
  display: inline-block;
  margin: 10px 12px;
  /* Ajouter de la marge autour du lien */
  color: black;
  /* Change link color to black */
}

.create-new-endroit-intro {
  color: black;
  margin-top: 10px;
  font-size: 14px;
  /* Change link color to black */
}

.arrow-down {
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 6px solid #7d7d7d;
  /* Couleur de la flèche */
}

.description-field {
  height: 100px;
  padding: 12px;
  /* Ajouter un padding pour un espacement uniforme */
  margin: 0 16px;
  /* Ajouter de la marge sur les côtés */
}

.rectangle-parent {
  position: relative;
  height: 40px;
  margin: 0 16px;
  /* Ajouter de la marge sur les côtés */
}

.group-child {
  position: absolute;
  top: 0;
  left: 0;
  border-radius: 10px;
  box-sizing: border-box;
  width: 100%;
  height: 100%;
  border: none;
  /* Enlever le contour */
}

.image-upload .group-child {
  border: 1px solid #7d7d7d;
  /* Ajouter un contour au champ d'insert d'image */
}

.supporting-text {
  position: absolute;
  top: 8px;
  left: 18px;
  /* Ajouter un peu plus de marge à gauche entre l'image et le bord du champ */
  letter-spacing: 0.5px;
  line-height: 24px;
  display: flex;
  align-items: center;
  width: calc(100% - 36px);
  /* Ajuster pour fournir plus d'espace pour le texte */
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  padding-left: 30px;
  /* Ajuster pour fournir plus d'espace pour l'icône */
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
  height: 250px;
  /* Rendre la carte un peu plus petite */
  width: calc(100% - 32px);
  /* Garder la largeur adaptée à l'écran et ajouter de la marge sur les côtés */
  margin: 0 16px;
  /* Ajouter de la marge sur les côtés */
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
  max-width: 700px;
  /* Limiter la largeur maximale */
  margin: 0 auto;
  /* Centrer le formulaire */
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
  cursor: grab;
  /* Change the cursor to a hand when hovering over the item */
}

.draggable-item:active {
  cursor: grabbing;
  /* Change the cursor to a closed hand when dragging the item */
}

.grip-icon {
  margin-right: 10px;
  /* Add some space between the icon and the text */
  font-size: 14px;
  /* Adjust the size of the icon to make it smaller */
  color: #7d7d7d;
  /* Use a color that fits your design */
  line-height: 1;
  /* Ensure the dots are vertically centered */
}

.alert-box {
  background-color: #f5e3cb;
  border: 1px solid #e4c29e;
  border-radius: 10px;
  padding: 15px;
  margin: 20px;
  text-align: center;
  color: black;
}

.alert-box p {
  display: inline;
  margin: 0;
  font-family: "Inter", sans-serif;
  font-size: 14px;
  color: black;
  /* Change text color to black */
}



.create-new-endroit-container {
  margin: 10px 0;
  /* Ajustez la marge selon vos besoins */
}

.create-new-endroit {
  display: flex;
  align-items: center;
  color: gray;
  text-decoration: none;
  /* Enlevez la décoration par défaut du lien */
  font-family: "Inter", sans-serif;
  /* Assurez-vous que la police est cohérente */
  font-size: 16px;
  /* Assurez-vous que la taille de la police est cohérente */
  letter-spacing: 0.5px;
  /* Assurez-vous que l'espacement des lettres est cohérent */
  line-height: 24px;
  /* Assurez-vous que la hauteur de ligne est cohérente */
}

.plus-icon {
  margin-right: 5px;
  /* Espace entre le symbole et le texte */
}

.underline-intro {
  text-decoration: underline;
  /* Souligner uniquement ce texte */
}

.create-new-endroit:hover .underline {
  text-decoration: underline;
}



a {
  color: black;
}

.rectangle-parent2 {
  height: 250px;
  /* Rendre la carte un peu plus petite */
  width: calc(100% - 32px);
  /* Garder la largeur adaptée à l'écran et ajouter de la marge sur les côtés */
  margin: 0 16px;
  /* Ajouter de la marge sur les côtés */
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
