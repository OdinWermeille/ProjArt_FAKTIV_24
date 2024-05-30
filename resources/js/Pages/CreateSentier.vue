<template>
  <div class="group-parent inter-text">
    <div class="group-container">
      <div class="group-child"></div>
      <div class="rectangle-wrapper">
        <h2 class="ajouter-un-lieu">Ajouter un sentier</h2>
        <form v-if="isAuthenticated" @submit.prevent="submitForm" enctype="multipart/form-data">
          <!-- Form fields -->
          <div class="input-group">
            <input class="group-item" type="text" v-model="form.nom" id="nom" placeholder="Nom" required>
          </div>
          <div class="input-group image-upload">
            <div class="rectangle-parent">
              <div class="group-child"></div>
              <label class="supporting-text" for="image">Image</label>
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
              <div v-if="themeDropdownOpen" class="dropdown-list">
                <label v-for="theme in themes" :key="theme.id" class="dropdown-list-item">
                  <input type="radio" :value="theme.id" v-model="form.theme_id" />
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
                  <input type="checkbox" :value="endroit.id" v-model="form.endroits" />
                  {{ endroit.nom }}
                </label>
              </div>
            </div>
          </div>
          <div class="ajouter-wrapper">
            <button type="submit" class="ajouter">Créer</button>
          </div>
        </form>
        <div v-else>
          <p class="ajouter-un-lieu">Veuillez vous connecter pour créer un lieu.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

export default {
  name: 'CreatePlace',
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
      endroits: [] // Added for multi-select
    });

    const dropdownOpen = ref(false);
    const themeDropdownOpen = ref(false);
    const search = ref("");

    const fetchThemesAndEndroits = async () => {
      try {
        const themeResponse = await axios.get('/api/themes'); // Créez une route API pour récupérer les thématiques
        themes.value = themeResponse.data;

        const endroitResponse = await axios.get('/api/endroits'); // Créez une route API pour récupérer les endroits
        endroits.value = endroitResponse.data;
      } catch (error) {
        console.error('Erreur lors de la récupération des thématiques et des endroits:', error);
      }
    };

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
      await fetchThemesAndEndroits();
    });

    const onFileChange = (e) => {
      form.value.image = e.target.files[0];
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

      // Append each endroit ID separately
      form.value.endroits.forEach((endroit, index) => {
        formData.append(`endroits[${index}]`, endroit);
      });

      try {
        const response = await axios.post('/api/sentiers', formData, {
          headers: {
            'Content-Type': 'multipart/form-data'
          }
        });
        console.log('Sentier créé avec succès:', response.data);
      } catch (error) {
        console.error('Erreur lors de la création du sentier:', error);
      }
    };

    const toggleDropdown = (event) => {
      dropdownOpen.value = !dropdownOpen.value;
      event.stopPropagation(); // Empêcher la propagation pour éviter les clics non désirés
    };

    const toggleThemeDropdown = (event) => {
      themeDropdownOpen.value = !themeDropdownOpen.value;
      event.stopPropagation(); // Empêcher la propagation pour éviter les clics non désirés
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

    // Ajouter un écouteur d'événement global pour fermer les listes déroulantes
    const handleClickOutside = (event) => {
      if (!event.target.closest('.dropdown-multi')) {
        dropdownOpen.value = false;
        themeDropdownOpen.value = false;
      }
    };

    onMounted(() => {
      document.addEventListener('click', handleClickOutside);
    });

    return {
      form,
      themes,
      endroits,
      isAuthenticated,
      dropdownOpen,
      themeDropdownOpen,
      onFileChange,
      submitForm,
      toggleDropdown,
      toggleThemeDropdown,
      selectedThemeText,
      selectedEndroitsText,
      search,
      filteredEndroits
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
  height: 50px;
  padding: 12px; /* Ajouter un padding pour un espacement uniforme */
  font-size: 16px;
  font-family: "Inter", sans-serif;
  background-color: transparent; /* Enlever le fond blanc */
  margin: 0 16px; /* Ajouter de la marge sur les côtés */
}
.dropdown-item {
  height: 50px; /* Agrandir la hauteur de la liste déroulante */
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
  height: 50px; /* Ajuster la hauteur */
  display: flex;
  align-items: center;
  justify-content: space-between;
  cursor: pointer; /* Ajouter le curseur pointeur pour indiquer qu'il est cliquable */
}
.dropdown-header {
  width: calc(100% - 24px); /* Laisser de la place pour la flèche */
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
  z-index: 1000;
  max-height: 200px; /* Limiter la hauteur de la liste déroulante */
  overflow-y: auto; /* Ajouter un défilement si nécessaire */
}
.dropdown-search {
  width: calc(100% - 24px); /* Laisser de la place pour la marge */
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
.arrow-down {
  width: 0;
  height: 0;
  border-left: 6px solid transparent;
  border-right: 6px solid transparent;
  border-top: 6px solid #7d7d7d; /* Couleur de la flèche */
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
select.group-item {
  -webkit-appearance: none; /* Supprimer l'apparence par défaut */
  -moz-appearance: none; /* Supprimer l'apparence par défaut */
  appearance: none; /* Supprimer l'apparence par défaut */
}
select.group-item:focus {
  outline: none; /* Supprimer le contour lors du focus */
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
.radio-group {
  margin: 0 16px;
}
.checkbox-group {
  margin: 0 16px;
}
</style>
