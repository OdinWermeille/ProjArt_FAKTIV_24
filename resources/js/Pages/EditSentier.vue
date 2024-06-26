<template>
  <div class="group-parent inter-text">
    <!-- Boîte d'alerte pour informer l'utilisateur de redéfinir les lieux lors de la modification d'un sentier -->
    <div class="alert-box">
      <p><strong>Attention</strong>, pour modifier un sentier, vous devez redéfinir les lieux qui le composent ainsi que leur ordre.</p>
    </div>

    <div class="group-container">
      <div class="group-child"></div>
      <div class="rectangle-wrapper">
        <h2 class="ajouter-un-lieu">Modifier le sentier</h2>
        <!-- Formulaire pour modifier le sentier, affiché uniquement si l'utilisateur est authentifié -->
        <form v-if="isAuthenticated" @submit.prevent="submitForm" enctype="multipart/form-data">
          <!-- Champ pour le nom du sentier avec validation -->
          <div class="input-group">
            <input :class="{ 'input-error': errors.nom }" class="group-item" type="text" v-model="form.nom" @input="validateNom" id="nom" placeholder="Nom">
            <span v-if="errors.nom" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.nom }}</span>
          </div>
          <!-- Champ pour la description du sentier -->
          <div class="input-group">
            <textarea :class="{ 'input-error': errors.description }" class="group-item description-field" v-model="form.description" id="description" placeholder="Description"></textarea>
            <span v-if="errors.description" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.description }}</span>
          </div>
          <!-- Champ pour télécharger une nouvelle image avec validation de la taille du fichier -->
          <div class="input-group image-upload">
            <div :class="{ 'input-error': errors.image || isFileTooLarge }" class="rectangle-parent">
              <div class="group-child"></div>
              <label :class="{ 'label-error': isFileTooLarge }" class="supporting-text" for="image">
                {{ truncatedImageLabel }} ({{ imageSize }} / {{ maxFileSize }} KB max)
              </label>
              <input class="image-input" type="file" @change="onFileChange" id="image"
                accept="image/jpeg, image/png, image/jpg, image/gif, image/svg+xml">
            </div>
            <span v-if="errors.image" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.image }}</span>
            <span v-if="isFileTooLarge" class="error-message"><i class="fas fa-exclamation-circle"></i>Le fichier est trop lourd.</span>
            <!-- Affiche l'image existante du sentier -->
            <div v-if="existingImageUrl" class="existing-image">
              <p>Image actuelle :</p>
              <img :src="`/${existingImageUrl}`" alt="Image actuelle" class="image-preview">
            </div>
          </div>
          <!-- Champ pour sélectionner la thématique du sentier -->
          <div class="input-group">
            <div :class="{ 'input-error': errors.theme_id }" class="group-item dropdown-multi">
              <div class="dropdown-header" @click="toggleThemeDropdown">
                {{ selectedThemeText }}
              </div>
              <span class="arrow-down" @click="toggleThemeDropdown"></span>
              <div v-if="themeDropdownOpen" class="dropdown-list theme-dropdown-list">
                <label v-for="theme in filteredThemes" :key="theme.id" class="dropdown-list-item">
                  <input type="radio" :value="theme.id" v-model="form.theme_id" @click.stop />
                  {{ theme.nom }}
                </label>
              </div>
            </div>
            <span v-if="errors.theme_id" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.theme_id }}</span>
          </div>
          <!-- Champ pour sélectionner les lieux qui composent le sentier -->
          <div class="input-group">
            <div :class="{ 'input-error': errors.endroits }" class="group-item dropdown-multi">
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
                  <a href="/lieux/create" class="create-new-endroit">
                    <span class="plus-icon">+</span> Créer un lieu <span class="underline">ici</span>
                  </a>
                </div>
              </div>
            </div>
            <span v-if="errors.endroits" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ errors.endroits }}</span>
          </div>

          <!-- Section pour choisir l'ordre des lieux -->
          <div v-if="form.endroits.length > 0" class="order-title">
            <h3>Choisir l'ordre des lieux</h3>
          </div>

          <!-- Liste des lieux avec possibilité de les réordonner par glisser-déposer -->
          <draggable v-if="form.endroits.length > 0" v-model="form.endroits" class="draggable-list" item-key="id"
            @start="onDragStart" @end="onDragEnd" @update="onDragUpdate" @change="onDragChange">
            <template #item="{ element }">
              <div class="draggable-item">
                <i class="fas fa-grip-vertical grip-icon"></i>
                {{ getEndroitName(element) }}
              </div>
            </template>
          </draggable>

          <!-- Aperçu de la carte du sentier -->
          <div v-if="form.endroits.length > 0" class="order-title">
            <h3>Aperçu du sentier</h3>
          </div>

          <div class="input-group map-container" v-if="form.endroits.length > 0">
            <div id="map" class="rectangle-parent2"></div>
          </div>

          <!-- Bouton pour soumettre le formulaire -->
          <div class="ajouter-wrapper">
            <button type="submit" class="ajouter">Modifier</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- Composant personnalisé pour afficher des popups -->
  <custom-popup :title="popupTitle" :message="popupMessage" :visible="popupVisible" @close="popupVisible = false" :actions="popupActions" />
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
import 'leaflet.awesome-markers';
import 'leaflet.awesome-markers/dist/leaflet.awesome-markers.css';
import CustomPopup from '../Components/CustomPopup.vue';

export default {
  name: 'EditSentier',
  components: {
    draggable,
    CustomPopup
  },
  props: {
    sentier: Object,
    themes: Array,
    endroits: Array
  },
  setup(props) {
    const form = ref({
      id: props.sentier.id,
      nom: props.sentier.nom,
      image: null,
      description: props.sentier.description,
      longueur: props.sentier.longueur,
      duree: props.sentier.duree,
      theme_id: props.sentier.theme.id,
      user_id: props.sentier.user_id,
      endroits: []  // Ne pas présélectionner les endroits
    });

    const existingImageUrl = ref(props.sentier.image_url); // URL de l'image existante
    const isAuthenticated = ref(false);
    const errors = ref({});
    const maxFileSize = 2048;
    const isFileTooLarge = ref(false);
    const imageSize = ref(0);

    const dropdownOpen = ref(false);
    const themeDropdownOpen = ref(false);
    const search = ref("");
    const imageLabel = ref(props.sentier.image ? props.sentier.image.split('/').pop() : 'Image');
    let routingControl = ref(null);
    let map = ref(null);

    // Vérifie si l'utilisateur est authentifié
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

    // Retourne la couleur en fonction du thème
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
    };

    // Initialise la carte
    const initializeMap = async () => {
      if (map.value) return;

      map.value = L.map('map').setView([46.8182, 8.2275], 8);

      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(map.value);

      createRoutingControl();
    };

    // Crée le contrôle de routage pour afficher le sentier sur la carte
    const createRoutingControl = () => {
      if (routingControl.value) {
        routingControl.value.remove();
      }

      routingControl.value = L.Routing.control({
        waypoints: props.sentier.endroits.map(endroit => L.latLng(endroit.coordonneesX, endroit.coordonneesY)),
        routeWhileDragging: true,
        show: false,
        addWaypoints: false,
        draggableWaypoints: false,
        createMarker: function (i, waypoint) {
          const customIcon = L.AwesomeMarkers.icon({
            icon: 'info-sign',
            markerColor: returnColor(form.value.theme_id),
            prefix: 'glyphicon',
          });

          const marker = L.marker(waypoint.latLng, {
            icon: customIcon,
            draggable: true
          });
          return marker;
        },
        lineOptions: {
          styles: [{ color: returnColor(form.value.theme_id), opacity: 1, weight: 5 }]
        },
        fitSelectedRoutes: true,
        routeWhileDragging: true,
        showAlternatives: false,
        router: new L.Routing.OSRMv1({
          serviceUrl: 'https://routing.openstreetmap.de/routed-foot/route/v1'
        })
      }).addTo(map.value);

      routingControl.value.on('routesfound', function (e) {
        const route = e.routes[0];
        form.value.longueur = (route.summary.totalDistance / 1000).toFixed(2);
        form.value.duree = Math.round(route.summary.totalTime / 60);
      });

      updateMap();
    };

    // Fonction exécutée lors du montage du composant
    onMounted(async () => {
      await checkAuthentication();
      if (isAuthenticated.value) {
        await nextTick();
        await initializeMap();
      }
    });

    // Mise à jour de la carte lorsque les lieux sont modifiés
    watch(() => form.value.endroits, async (newEndroits) => {
      if (newEndroits.length > 0 && !map.value) {
        await nextTick();
        await initializeMap();
      }
      updateMap();
    }, { deep: true });

    // Recrée le contrôle de routage lorsque la thématique change
    watch(() => form.value.theme_id, async (newTheme) => {
      if (map.value) {
        createRoutingControl();
      }
    });

    // Gestion du changement de fichier
    const onFileChange = (e) => {
      const file = e.target.files[0];
      form.value.image = file;
      imageSize.value = (file.size / 1024).toFixed(2);
      isFileTooLarge.value = file.size / 1024 > maxFileSize;
      imageLabel.value = file ? file.name : 'Image';
    };

    // Troncature du nom du fichier si trop long
    const truncatedImageLabel = computed(() => {
      const maxLength = 30;
      if (imageLabel.value.length > maxLength) {
        return imageLabel.value.substring(0, maxLength) + '...';
      }
      return imageLabel.value;
    });

    // Réinitialisation du formulaire
    const resetForm = () => {
      form.value = {
        id: props.sentier.id,
        nom: props.sentier.nom,
        image: null,
        description: props.sentier.description,
        longueur: props.sentier.longueur,
        duree: props.sentier.duree,
        theme_id: props.sentier.theme.id,
        user_id: props.sentier.user_id,
        endroits: props.sentier.endroits.map(e => e.id)
      };
      imageLabel.value = 'Image';
      if (routingControl.value) {
        routingControl.value.setWaypoints([]);
      }
      errors.value = {};
      isFileTooLarge.value = false;
      imageSize.value = 0;
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
      if (!form.value.nom) {
        validationErrors.nom = 'Le nom est requis.';
      } else if (form.value.nom.includes('-')) {
        validationErrors.nom = 'Le nom ne doit pas contenir de tirets (-).';
      }
      if (!form.value.description) {
        validationErrors.description = 'La description est requise.';
      }
      if (!form.value.image && !props.sentier.image_url) {
        validationErrors.image = 'L\'image est requise.';
      } else if (isFileTooLarge.value) {
        validationErrors.image = 'Le fichier est trop lourd.';
      }
      if (!form.value.theme_id) {
        validationErrors.theme_id = 'La thématique est requise.';
      }
      if (form.value.endroits.length < 2) {
        validationErrors.endroits = 'Veuillez sélectionner au moins deux lieux.';
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
      formData.append('_method', 'PUT');
      formData.append('nom', form.value.nom);
      if (form.value.image) {
          formData.append('image', form.value.image);
      }
      formData.append('description', form.value.description);
      formData.append('longueur', form.value.longueur);
      formData.append('duree', form.value.duree);
      formData.append('theme_id', form.value.theme_id);
      formData.append('user_id', form.value.user_id);

      form.value.endroits.forEach((endroit, index) => {
          formData.append(`endroits[${index}]`, endroit);
      });

      try {
          const response = await axios.post(`/api/sentiers/${form.value.id}`, formData, {
          headers: {
              'Content-Type': 'multipart/form-data'
          }
          });
          const sentier = response.data;
          const slug = sentier.nom.toLowerCase().replace(/\s+/g, '-');
          popupTitle.value = 'Merci!';
          popupMessage.value = 'Votre sentier a été modifié avec succès!';
          popupActions.value = [
          {
              text: 'Voir le sentier',
              handler: () => window.location.href = `/sentiers/${slug}`
          }
          ];
          popupVisible.value = true;
          resetForm();
      } catch (error) {
          console.error('Erreur lors de la modification du sentier:', error);
          if (error.response && error.response.data.errors) {
          errors.value = cleanErrors(error.response.data.errors);
          } else {
          popupTitle.value = 'Erreur!';
          popupMessage.value = 'Il y a eu une erreur lors de la modification de votre sentier.';
          popupVisible.value = true;
          }
      }
      };

    // Met à jour la carte avec les nouveaux points de passage
    const updateMap = () => {
      if (!map.value || !routingControl.value) return;

      const selectedEndroits = form.value.endroits.map(endroitId => {
        return props.endroits.find(endroit => endroit.id === endroitId);
      });

      const waypoints = selectedEndroits.map(endroit => L.latLng(endroit.coordonneesX, endroit.coordonneesY));

      routingControl.value.setWaypoints(waypoints);
      routingControl.value.getPlan().setWaypoints(waypoints);
    };

    // Ouvre ou ferme le dropdown pour sélectionner les lieux
    const toggleDropdown = () => {
      if (!dropdownOpen.value) {
        themeDropdownOpen.value = false;
      }
      dropdownOpen.value = !dropdownOpen.value;
    };

    // Ouvre ou ferme le dropdown pour sélectionner la thématique
    const toggleThemeDropdown = () => {
      if (!themeDropdownOpen.value) {
        dropdownOpen.value = false;
      }
      themeDropdownOpen.value = !themeDropdownOpen.value;
    };

    // Texte affiché pour la thématique sélectionnée
    const selectedThemeText = computed(() => {
      const selectedTheme = props.themes.find(theme => theme.id === form.value.theme_id);
      return selectedTheme ? selectedTheme.nom : "Thématique";
    });

    // Texte affiché pour les lieux sélectionnés
    const selectedEndroitsText = computed(() => {
      if (form.value.endroits.length === 0) return "Lieux";
      const selectedNames = props.endroits
        .filter(endroit => form.value.endroits.includes(endroit.id))
        .map(endroit => endroit.nom)
        .join(", ");
      return selectedNames;
    });

    // Filtrer les lieux en fonction de la recherche
    const filteredEndroits = computed(() => {
      if (!search.value) {
        return props.endroits;
      }
      return props.endroits.filter(endroit => endroit.nom.toLowerCase().includes(search.value.toLowerCase()));
    });

    // Filtrer les thématiques disponibles
    const filteredThemes = computed(() => {
      return props.themes.filter(theme => theme.nom.toLowerCase() !== 'tout');
    });

    // Mise à jour de la carte lors du glissement des lieux
    const onDragUpdate = () => {
      updateMap();
    };

    // Mise à jour de l'ordre des lieux lors du changement
    const onDragChange = (event) => {
      const { oldIndex, newIndex } = event;
      if (oldIndex !== undefined && newIndex !== undefined) {
          const movedItem = form.value.endroits.splice(oldIndex, 1)[0];
          form.value.endroits.splice(newIndex, 0, movedItem);
          updateMap();
      }
    };

    // Gère les clics en dehors des dropdowns pour les fermer
    const handleClickOutside = (event) => {
      if (!event.target.closest('.dropdown-multi')) {
        dropdownOpen.value = false;
        themeDropdownOpen.value = false;
      }
    };

    // Ajoute un écouteur d'événements pour détecter les clics en dehors
    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
    });

    // Récupère le nom d'un lieu par son ID
    const getEndroitName = (id) => {
      const endroit = props.endroits.find(e => e.id === id);
      return endroit ? endroit.nom : '';
    };

    const popupTitle = ref('');
    const popupMessage = ref('');
    const popupVisible = ref(false);
    const popupActions = ref([]);

    return {
      form,
      isAuthenticated,
      dropdownOpen,
      themeDropdownOpen,
      imageLabel,
      existingImageUrl,
      onFileChange,
      submitForm,
      validateNom,
      toggleDropdown,
      toggleThemeDropdown,
      selectedThemeText,
      selectedEndroitsText,
      search,
      filteredEndroits,
      filteredThemes,
      onDragUpdate,
      onDragChange,
      handleClickOutside,
      resetForm,
      getEndroitName,
      updateMap,
      truncatedImageLabel,
      popupTitle,
      popupMessage,
      popupVisible,
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
  @import url('https://unpkg.com/leaflet-routing-machine/dist/leaflet-routing-machine.css');
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
  
  .input-error .dropdown-multi {
    border-color: red;
  }
  
  .input-error .rectangle-parent {
    border-color: red;
  }
  
  .label-error {
    color: red;
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
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
  }
  
  .dropdown-header {
    width: calc(100% - 24px);
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }
  
  .dropdown-list {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background-color: #FAFAFA;
    border: 1px solid #7d7d7d;
    border-radius: 0 0 10px 10px;
    z-index: 1600;
    max-height: 200px;
    overflow-y: auto;
  }
  
  .theme-dropdown-list {
    z-index: 1700;
  }
  
  .dropdown-search {
    width: calc(100% - 24px);
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
  
  .create-new-endroit-intro {
    color: #212121;
    margin-top: 10px;
    font-size: 14px;
  }
  
  .arrow-down {
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-top: 6px solid #7d7d7d;
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
    border: 1px solid #F0F0F0;
    border-radius: 4px;
    background-color: #F0F0F0;
    cursor: grab;
  }
  
  .draggable-item:active {
    cursor: grabbing;
  }
  
  .grip-icon {
    margin-right: 10px;
    font-size: 14px;
    color: #7d7d7d;
    line-height: 1;
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
  }
  
  .create-new-endroit-container {
    margin: 10px 0;
  }
  
  .create-new-endroit {
    display: flex;
    align-items: center;
    color: #7d7d7d;
    text-decoration: none;
    font-family: "Inter", sans-serif;
    font-size: 16px;
    letter-spacing: 0.5px;
    line-height: 24px;
    margin: 10px 12px;
  }
  
  .plus-icon {
    margin-right: 5px;
  }
  
  .underline-intro {
    text-decoration: underline;
  }
  
  .underline {
    text-decoration: underline;
    padding-left: 10px;
  }
  
  .create-new-endroit:hover .underline {
    text-decoration: underline;
  }
  
  a {
    color: #212121;
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
  
  .existing-image {
    margin-top: 10px;
    margin-left: 16px;
  }
  
  .image-preview {
    max-width: 50%;
    height: auto;
    border-radius: 5px;
    margin-top: 12px;
  }
</style>  