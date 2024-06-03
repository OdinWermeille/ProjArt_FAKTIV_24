<template>
  <header class="header">
    <div class="logo">
      <a href="/carte">
        <img src="/images/Group.svg" alt="Logo" />
      </a>
    </div>
    <nav class="nav">
      <ul class="nav-list" :class="{ 'nav-list-mobile': isMobileMenuOpen }">
        <li class="nav-item"> 
          <a href="/sentiers"><img src="/images/list.png" alt="List Icon" class="nav-icon" /> Liste</a>
        </li>
        <div class="nav-divider"></div>
        <li class="nav-item">
          <a href="/carte"><img src="/images/map.svg" alt="Map Icon" class="nav-icon" /> Carte</a>
        </li>
        <div v-if="isAuthenticated" class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/sentiers/create"><img src="/path/to/add-trail-icon.svg" alt="Add Trail Icon" class="nav-icon" /> Ajouter un sentier</a>
        </li>
        <div v-if="isAuthenticated" class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/endroits/create"><img src="/path/to/add-place-icon.svg" alt="Add Place Icon" class="nav-icon" /> Ajouter un lieu</a>
        </li>
        <div class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/logout"><img src="/path/to/logout-icon.svg" alt="Logout Icon" class="nav-icon" /> Logout</a>
        </li>
        <li v-else class="nav-item">
          <a href="/login"><img src="/path/to/login-icon.svg" alt="Login Icon" class="nav-icon" /> Login</a>
        </li>
      </ul>
      <button class="burger-menu" @click="toggleMobileMenu">
        ☰
      </button>
    </nav>
    <div class="header-line"></div>
  </header>
</template>

<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
  setup() {
    const isMobileMenuOpen = ref(false);
    const isAuthenticated = ref(false);
    const form = ref({ user_id: null });

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

    const toggleMobileMenu = () => {
      isMobileMenuOpen.value = !isMobileMenuOpen.value;
    };

    return {
      isMobileMenuOpen,
      isAuthenticated,
      toggleMobileMenu,
    };
  },
};
</script>

<style scoped>
.header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1rem;
  background-color: #f8f9fa; /* Couleur de fond claire */
  color: #333;
  position: relative;
  height: 70px;
  width: 100%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Ajoute une ombre pour un effet de profondeur */
  z-index: 20; /* Assurez-vous que le header a un z-index élevé */
}

.logo img {
  height: 40px;
}

.nav {
  display: flex;
  align-items: center;
}

.nav-list {
  display: flex;
  list-style: none;
  margin: 0;
  padding: 0;
  align-items: center;
  padding-left: 1rem; /* Ajoute une marge à gauche pour éloigner les liens du bord */
}

.nav-item {
  display: flex;
  align-items: center;
  margin-left: 1rem;
  position: relative; /* Nécessaire pour le soulignement sur hover */
  padding: 10px 15px;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s;
  background-color: transparent;
}

.nav-icon {
  margin-right: 0.5rem;
  height: 24px; /* Taille de l'icône */
  width: 24px;
}

.nav-item a {
  display: flex;
  align-items: center;
  color: #333; /* Couleur noire plus douce */
  text-decoration: none;
  font-weight: 500; /* Ajoute du poids au texte pour une meilleure lisibilité */
  transition: color 0.3s;
}

.nav-item:hover {
  background-color: #e9ecef; /* Change la couleur de fond au survol */
}

.nav-item:hover a {
  color: #BFD2A6; /* Change la couleur de texte au survol */
}

.nav-divider {
  height: 40px;
  width: 1px;
  background-color: #ddd; /* Couleur de la barre entre les éléments */
  margin: 0 1rem;
}

.burger-menu {
  display: none;
  background: none;
  border: none;
  color: #333;
  font-size: 2rem;
  cursor: pointer;
}

.header-line {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 1px;
  background-color: #ddd; /* Couleur de la ligne plus claire */
}

@media (max-width: 768px) {
  .nav-list {
    display: none;
    position: absolute;
    top: 70px; /* Ajuste la position pour qu'elle soit sous le header */
    left: 0;
    right: 0;
    background-color: white;
    flex-direction: column;
    align-items: center; /* Centre les éléments */
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); /* Ajoute une ombre pour le menu mobile */
    z-index: 30; /* Assurez-vous que le menu a un z-index élevé */
    transition: all 0.3s ease-in-out; /* Ajoute une transition douce pour le menu mobile */
  }

  .nav-list.nav-list-mobile {
    display: flex;
  }

  .nav-item {
    margin: 1rem 0;
    width: 100%; /* Prend toute la largeur pour un meilleur clic */
    text-align: center;
  }

  .nav-divider {
    display: none; /* Cache les diviseurs en mode mobile */
  }

  .burger-menu {
    display: block;
  }
}
</style>