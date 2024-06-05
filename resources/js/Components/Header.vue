<template>
  <header class="header">
    <div class="logo">
      <a href="/sentiers">
        <img src="/images/Group.svg" alt="Logo" />
      </a>
    </div>
    <nav class="nav">
      <ul class="nav-list" :class="{ 'nav-list-mobile': isMobileMenuOpen }">
        <li class="nav-item">
          <a href="/sentiers">
            <img src="/images/chemin_noir.svg" alt="Chemin Icon" class="nav-icon" /> Sentiers
          </a>
        </li>
        <div v-if="isAuthenticated" class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/sentiers/create">
            <img src="/images/add.svg" alt="Icon +" class="nav-icon" /> Ajouter un sentier
          </a>
        </li>
        <div v-if="isAuthenticated" class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/lieux/create">
            <img src="/images/add.svg" alt="Icon +" class="nav-icon" /> Ajouter un lieu
          </a>
        </li>
        <div class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/logout">
            <img src="/images/logout.svg" alt="Logout Icon" class="nav-icon" /> Logout
          </a>
        </li>
        <li v-else class="nav-item">
          <a href="/login">
            <img src="/images/login.svg" alt="Login Icon" class="nav-icon" /> Login
          </a>
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
        const response = await axios.get('/api/user');
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
  font-family: "Inter", sans-serif;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 1rem;
  background-color: #FAFAFA; /* Couleur de fond claire */
  color: #212121;
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
  margin-left: 1rem;
  position: relative; /* Nécessaire pour le soulignement sur hover */
  cursor: pointer;
}

.nav-item a {
  font-family: "Inter", sans-serif;
  display: flex;
  align-items: center;
  padding: 10px 15px;
  background-color: transparent;
  color: #212121; /* Couleur noire plus douce */
  text-decoration: none;
  font-weight: 500; /* Ajoute du poids au texte pour une meilleure lisibilité */
  transition: background-color 0.3s, color 0.3s;
}

.nav-item a:hover {
  background-color: #F0F0F0; /* Change la couleur de fond au survol */
  color: #BFD2A6; /* Change la couleur de texte au survol */
}

.nav-icon {
  margin-right: 0.5rem;
  height: 24px; /* Taille de l'icône */
  width: 24px;
}

.nav-divider {
  height: 40px;
  width: 1px;
  background-color: #dedede; /* Couleur de la barre entre les éléments */
  margin: 0 1rem;
}

.burger-menu {
  font-family: "Inter", sans-serif;
  display: none;
  background: none;
  border: none;
  color: #212121;
  font-size: 2rem;
  cursor: pointer;
}

.header-line {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 1px;
  background-color: #F0F0F0; /* Couleur de la ligne plus claire */
}

@media (max-width: 768px) {
  .nav-list {
    display: none;
    position: absolute;
    top: 70px; /* Ajuste la position pour qu'elle soit sous le header */
    left: 0;
    right: 0;
    background-color: #fafafa;
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

  .nav-item a {
    width: 100%; /* Prend toute la largeur pour un meilleur clic */
    justify-content: center; /* Centre le contenu horizontalement */
  }

  .nav-divider {
    display: none; /* Cache les diviseurs en mode mobile */
  }

  .burger-menu {
    display: block;
  }
}
</style>
