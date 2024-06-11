<template>
  <header class="header">
    <div class="logo">
      <a href="/sentiers">
        <img src="/storage/images/Group.svg" alt="Logo" />
      </a>
    </div>
    <nav class="nav">
      <ul class="nav-list" :class="{ 'nav-list-mobile': isMobileMenuOpen }">
        <li class="nav-item">
          <a href="/sentiers">
            <img src="/storage/images/chemin_noir.svg" alt="Chemin Icon" class="nav-icon" /> Sentiers
          </a>
        </li>
        <div v-if="isAuthenticated" class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/sentiers/create">
            <img src="/storage/images/add.svg" alt="Icon +" class="nav-icon" /> Ajouter un sentier
          </a>
        </li>
        <div v-if="isAuthenticated" class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/lieux/create">
            <img src="/storage/images/add.svg" alt="Icon +" class="nav-icon" /> Ajouter un lieu
          </a>
        </li>
        <div class="nav-divider"></div>
        <li v-if="isAuthenticated" class="nav-item">
          <a href="/logout">
            <img src="/storage/images/logout.svg" alt="Logout Icon" class="nav-icon" /> Logout
          </a>
        </li>
        <li v-else class="nav-item">
          <a href="/login">
            <img src="/storage/images/login.svg" alt="Login Icon" class="nav-icon" /> Login
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
  background-color: #FAFAFA;
  color: #212121;
  position: relative;
  height: 70px;
  width: 100%;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
  z-index: 20;
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
  padding-left: 1rem;
}

.nav-item {
  margin-left: 1rem;
  position: relative;
  cursor: pointer;
}

.nav-item a {
  font-family: "Inter", sans-serif;
  display: flex;
  align-items: center;
  padding: 10px 15px;
  background-color: transparent;
  color: #212121;
  text-decoration: none;
  font-weight: 500;
  transition: background-color 0.3s, color 0.3s;
}

.nav-item a:hover {
  background-color: #F0F0F0;
  color: #BFD2A6;
}

.nav-icon {
  margin-right: 0.5rem;
  height: 24px;
  width: 24px;
}

.nav-divider {
  height: 40px;
  width: 1px;
  background-color: #dedede;
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
  background-color: #F0F0F0;
}

@media (max-width: 768px) {
  .nav-list {
    display: none;
    position: absolute;
    top: 70px;
    left: 0;
    right: 0;
    background-color: #fafafa;
    flex-direction: column;
    align-items: center;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    z-index: 30;
    transition: all 0.3s ease-in-out;
  }

  .nav-list.nav-list-mobile {
    display: flex;
  }

  .nav-item {
    margin: 1rem 0;
    width: 100%;
    text-align: center;
  }

  .nav-item a {
    width: 100%;
    justify-content: center;
  }

  .nav-divider {
    display: none;
  }

  .burger-menu {
    display: block;
  }
}
</style>
