<template>
    <div class="sentier-page">
      <h1>{{ sentier.nom }}</h1>
      <section class="image-section">
        <img :alt=" 'image ' + sentier.nom" :src="`/${sentier.image_url}`"/>
      </section>
      <section class="info-section">
        <div class="info">
          <span>Longueur</span>
          <p>{{ sentier.longueur }} km</p>
        </div>
        <div class="info">
          <span>Thématique</span>
          <p>{{ sentier.theme.nom }}</p>
        </div>
        <div class="info">
          <span>Durée</span>
          <p>{{ sentier.duree }} min</p>
        </div>
      </section>
      <div class="button-container">
        <button class="map-button">VOIR SUR LA CARTE</button>
      </div>
      <hr class="separator" />
      <section class="description">
        <h2>Description</h2>
        <p>{{ sentier.description }}</p>
      </section>
      <section class="map-section">
        <!-- Placeholder for map image -->
        <!-- <img src="map-placeholder.png" alt="Map of the trail" /> -->
      </section>
      <hr class="separator" />
      <section class="points-section">
        <h2>Endroits du sentier</h2>
        <ul>
            <li v-for="endroit in sentier.endroits" :key="endroit.id" @click="onGroupContainerClick(endroit.id)">
                {{ endroit.nom }}
            </li>
        </ul>
      </section>
    </div>
  </template>
 
  <script>
  export default {
    props: {
      sentier: Object
    },
 
    methods: {
      onGroupContainerClick(id) {
        window.location.href = `/endroits/${id}`;
      },
    }
  }
  </script>
 
  <style scoped>
  h1{
    font-family: "Inter", sans-serif;
    font-size: 18px;
    font-weight: bold;
    height: 48px;
  }

  h2{
    font-family: "Inter", sans-serif;
    font-size: 16px;
    font-weight: bold;
    height: 48px;
  }
 
  .sentier-page {
    font-family: "Inter", sans-serif;
    color: #333;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
  }
 
  .image-section img {
    width: 100%;
    height: auto;
    border-radius: 10px;
  }
 
  .info-section {
    display: flex;
    justify-content: space-between;
    margin: 20px 0;
  }
 
  .info {
    text-align: left;
  }
 
  .info span {
    display: flex;
    font-weight: bold;
  }
 
  .button-container {
    display: flex;
    justify-content: center;
    margin: 20px 0;
    }
 
  .map-button {
    width: 200px;
    padding: 10px;
    background-color: #4A8C2A;
    color: white;
    border: none;
    border-radius: 20px;
    text-align: center;
    cursor: pointer;
    margin: 20px 0;
  }
 
  .description {
    margin: 20px 0;
  }
 
  .map-section img {
    width: 100%;
    height: auto;
    border-radius: 10px;
  }
 
  .points-section ul {
    list-style: none;
    padding: 0;
  }
 
  .points-section li {
    display: flex;
    align-items: center;
    margin: 10px 0;
  }
 
  .points-section i {
    margin-right: 10px;
  }

  .separator {
    border: none;
    border-top: 2px solid #ddd;
    margin: 20px 0;
}
  </style>