<template>
    <header class="header">
      <div class="logo">
        <img src="/images/Group.svg" alt="Logo" />
      </div>
      <nav class="nav">
        <ul class="nav-list" :class="{ 'nav-list-mobile': isMobileMenuOpen }">
          <li class="nav-item"><a href="/sentiers">Liste</a></li>
          <li class="nav-item"><a href="/carte">Carte</a></li>
          <li class="nav-item"><a href="/sentiers/create">Ajouter un sentier</a></li>
          <li class="nav-item"><a href="/endroits/create">Ajouter un lieu</a></li>
          <li class="nav-item"><a href="/login">Login</a></li>
        </ul>
        <button class="burger-menu" @click="toggleMobileMenu">
          ☰
        </button>
      </nav>
      <div class="header-line"></div> <!-- Ligne ajoutée -->
    </header>
  </template>
 
  <script>
  export default {
    data() {
      return {
        isMobileMenuOpen: false,
      };
    },
    methods: {
      toggleMobileMenu() {
        this.isMobileMenuOpen = !this.isMobileMenuOpen;
      },
    },
  };
  </script>
 
  <style scoped>
  .header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1rem;
    background-color: white;
    color: #333;
    position: relative;
    height: 70px;
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
  }
 
  .nav-item {
    margin-left: 1rem;
  }
 
  .nav-item a {
    color: black;
    text-decoration: none;
    transition: color 0.3s;
  }
 
  .nav-item a:hover {
    color: #ddd;
  }
 
  .burger-menu {
    display: none;
    background: none;
    border: none;
    color: black;
    font-size: 2rem;
    cursor: pointer;
  }
 
  .header-line {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 1px;
    background-color: #333; /* Couleur de la ligne */
  }
 
  @media (max-width: 768px) {
    .nav-list {
      display: none;
      position: absolute;
      top: 60px;
      left: 0;
      right: 0;
      background-color: white;
      flex-direction: column;
    }
 
    .nav-list.nav-list-mobile {
      display: flex;
    }
 
    .nav-item {
      margin: 1rem 0;
      text-align: center;
    }
 
    .burger-menu {
      display: block;
    }
  }
  </style>