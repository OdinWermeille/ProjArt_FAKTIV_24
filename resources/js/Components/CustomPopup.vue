<template>
  <!-- Affiche la popup si visible est vrai -->
  <div v-if="visible" class="popup-overlay" @click.self="close">
    <div class="popup-content">
      <h2>{{ title }}</h2>
      <p>{{ message }}</p> 
      <div class="button-container">
        <!-- Boutons d'action dynamiques -->
        <button v-for="action in actions" :key="action.text" @click="action.handler" class="redirect-button">
          {{ action.text }}
        </button>
        <!-- Bouton pour fermer la popup -->
        <button @click="close" class="popup-button">FERMER</button>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String,
      default: 'Titre'
    },
    message: {
      type: String,
      default: 'Message'
    },
    visible: {
      type: Boolean,
      default: false
    },
    actions: {
      type: Array,
      default: () => []
    }
  },
  methods: {
    close() {
      this.$emit('close'); // Émet l'événement de fermeture
    }
  }
};
</script>

<style scoped>
.popup-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

.popup-content {
  background: #f0f0f0;
  padding: 20px;
  border-radius: 10px;
  text-align: center;
  width: 350px;
  max-width: 90%;
}

.popup-content h2 {
  font-family: "Inter", sans-serif;
  font-size: 18px;
  font-weight: bold;
  color: #212121;
  margin-bottom: 10px;
}

.popup-content p {
  font-family: "Inter", sans-serif;
  font-size: 16px;
  color: #7d7d7d;
  margin-bottom: 20px;
}

.button-container {
  display: flex;
  justify-content: space-around;
}

.popup-button {
  background-color: #4a8c2a;
  color: #fafafa;
  border: 1px solid #bfd2a6;
  border-radius: 20px;
  padding: 10px 20px;
  text-transform: uppercase;
  font-weight: 500;
  font-size: 14px;
  cursor: pointer;
}

.popup-button:hover {
  background-color: #fafafa;
  color: #4a8c2a;
  border: 1px solid #4a8c2a;
}

.redirect-button {
  background-color: #fafafa;
  color: #4a8c2a;
  border: 1px solid #4a8c2a;
  cursor: pointer;
  border-radius: 20px;
  padding: 10px 20px;
  text-transform: uppercase;
  font-weight: 500;
  font-size: 14px;
}

.redirect-button:hover {
  background-color: #4a8c2a;
  color: #fafafa;
  border: 1px solid #bfd2a6;
}
</style>
