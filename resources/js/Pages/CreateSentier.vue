<template>
  <div>
    <h2>Créer un nouveau sentier</h2>
    <!-- Le formulaire est maintenant intégré directement -->
    <form v-if="isAuthenticated" @submit.prevent="submitForm" enctype="multipart/form-data">
      <!-- Form fields -->
      <div>
        <label for="nom">Nom:</label>
        <input type="text" v-model="form.nom" id="nom" required>
      </div>
      <div>
        <label for="image">Image:</label>
        <input type="file" @change="onFileChange" id="image" required>
      </div>
      <div>
        <label for="description">Description:</label>
        <textarea v-model="form.description" id="description" required></textarea>
      </div>
      <div>
        <label for="longueur">Longueur (en km):</label>
        <input type="number" v-model="form.longueur" id="longueur" required>
      </div>
      <div>
        <label for="duree">Durée (en minutes):</label>
        <input type="number" v-model="form.duree" id="duree" required>
      </div>
      <div>
        <label>Thématique:</label>
        <div v-for="theme in themes" :key="theme.id">
          <input type="radio" :id="'theme-' + theme.id" v-model="form.theme_id" :value="theme.id">
          <label :for="'theme-' + theme.id">{{ theme.nom }}</label>
        </div>
      </div>
      <div>
        <label>Endroits:</label>
        <div v-for="endroit in endroits" :key="endroit.id">
          <input type="checkbox" :id="'endroit-' + endroit.id" v-model="form.endroits" :value="endroit.id">
          <label :for="'endroit-' + endroit.id">{{ endroit.nom }}</label>
        </div>
      </div>
      <button type="submit">Créer</button>
    </form>
    <div v-else>
      <p>Veuillez vous connecter pour créer un sentier.</p>
    </div>
  </div>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
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

    onMounted(() => {
      checkAuthentication();
      fetchThemesAndEndroits();
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

    return {
      form,
      themes,
      endroits,
      isAuthenticated,
      onFileChange,
      submitForm
    };
  }
};
</script>

<style scoped>
/* Ajoutez du style si nécessaire */
</style>
