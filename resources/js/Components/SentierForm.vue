<template>
    <div>
      <form v-if="isAuthenticated" @submit.prevent="submitForm">
        <!-- Form fields -->
        <div>
          <label for="nom">Nom:</label>
          <input type="text" v-model="form.nom" id="nom" required>
        </div>
        <div>
          <label for="image_url">URL de l'image:</label>
          <input type="url" v-model="form.image_url" id="image_url" required>
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
          <label for="theme_id">Thématique:</label>
          <select v-model="form.theme_id" id="theme_id" required>
            <option v-for="theme in themes" :key="theme.id" :value="theme.id">{{ theme.nom }}</option>
          </select>
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
    props: {
      themes: {
        type: Array,
        required: true
      }
    },
    setup(props) {
      const isAuthenticated = ref(false);
      const form = ref({
        nom: '',
        image_url: '',
        description: '',
        longueur: '',
        duree: '',
        theme_id: '',
        user_id: null
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
  
      onMounted(() => {
        checkAuthentication();
      });
  
      const submitForm = async () => {
        try {
          const response = await axios.post('/api/sentiers', form.value); // Utilisez la même URL que dans web.php
          console.log('Sentier créé avec succès:', response.data);
        } catch (error) {
          console.error('Erreur lors de la création du sentier:', error);
        }
      };
  
      return {
        form,
        isAuthenticated,
        submitForm
      };
    }
  }
  </script>
  
  <style scoped>
  /* Ajoutez du style si nécessaire */
  </style>
  