<template>
    <div>
      <h2>Créer un nouveau sentier</h2>
      <SentierForm :themes="themes" />
    </div>
  </template>
  
  <script>
  import axios from 'axios';
  import { ref, onMounted } from 'vue';
  import SentierForm from '@/Components/SentierForm.vue';
  
  export default {
    components: {
      SentierForm,
    },
    setup() {
      const themes = ref([]);
  
      const fetchThemes = async () => {
        try {
          const response = await axios.get('/api/themes'); // Créez une route API pour récupérer les thématiques
          themes.value = response.data;
        } catch (error) {
          console.error('Erreur lors de la récupération des thématiques:', error);
        }
      };
  
      onMounted(() => {
        fetchThemes();
      });
  
      return {
        themes,
      };
    },
  };
  </script>
  
  <style scoped>
  /* Ajoutez du style si nécessaire */
  </style>
  