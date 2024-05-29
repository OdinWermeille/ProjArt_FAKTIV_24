<template>
  <div>
    <!-- Barre de recherche et boutons -->
    <div :class="$style.searchContainer">
      <div :class="$style.searchWrapper">
        <input type="text" :class="$style.searchInput" placeholder="Rechercher" />
        <img :class="$style.searchIcon" alt="Search Icon" src="/images/icon.svg" />
      </div>
      <div :class="$style.buttonsWrapper">
        <button :class="$style.button" @click="showSortModal = true">Trier par</button>
        <button :class="$style.button" @click="showFilterModal = true">Filtrer</button>
        <img :class="$style.mapIcon" alt="Map Icon" src="/images/map.svg" @click="redirectToMap" />
      </div>
    </div>

    <!-- Liste des sentiers -->
    <div :class="$style.groupParent">
      <div
        v-for="sentier in sentiers"
        :key="sentier.id"
        :class="$style.card"
        @click="onGroupContainerClick(sentier.id)"
      >
        <img
          :class="$style.image"
          :alt="sentier.nom"
          :src="`/${sentier.image_url}`"
        />
        <div :class="$style.content">
          <b :class="$style.title">{{ sentier.nom }}</b>
          <div :class="$style.description">{{ sentier.description }}</div>
          <div :class="$style.info">
            <div :class="$style.length">{{ sentier.longueur }} km</div>
            <div :class="$style.separator">•</div>
            <div :class="$style.theme">{{ sentier.theme.nom }}</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Sort Modal -->
    <div v-if="showSortModal" :class="$style.modalBackdrop" @click="closeModal">
      <div :class="$style.modal" @click.stop>
        <div :class="$style.modalHeader">
          <button @click="closeModal" :class="$style.closeButton">&times;</button>
          <h3>Trier par</h3>
        </div>
        <div :class="$style.modalContent">
          <label>
            <input type="radio" name="sort" value="newest" />
            Derniers ajouts
          </label>
          <label>
            <input type="radio" name="sort" value="oldest" checked />
            Les plus anciens
          </label>
        </div>
        <div :class="$style.modalFooter">
          <button :class="$style.resetButton">RÉINITIALISER</button>
          <button :class="$style.validateButton" @click="closeModal">VALIDER</button>
        </div>
      </div>
    </div>

    <!-- Filter Modal -->
    <div v-if="showFilterModal" :class="$style.modalBackdrop" @click="closeModal">
      <div :class="$style.modal" @click.stop>
        <div :class="$style.modalHeader">
          <button @click="closeModal" :class="$style.closeButton">&times;</button>
          <h3>Filtrer par</h3>
        </div>
        <div :class="$style.modalContent">
          <div>
            <h4>Activité</h4>
            <label>
              <input type="radio" name="activity" value="history" />
              Histoire
            </label>
            <label>
              <input type="radio" name="activity" value="art" checked />
              Art
            </label>
            <label>
              <input type="radio" name="activity" value="nature" />
              Nature
            </label>
          </div>
          <div>
            <h4>Distance</h4>
            <label>
              <input type="radio" name="distance" value="0-5" />
              0-5 km
            </label>
            <label>
              <input type="radio" name="distance" value="6-10" checked />
              6-10 km
            </label>
            <label>
              <input type="radio" name="distance" value="11+" />
              11+ km
            </label>
          </div>
        </div>
        <div :class="$style.modalFooter">
          <button :class="$style.resetButton">RÉINITIALISER</button>
          <button :class="$style.validateButton" @click="closeModal">VALIDER</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default defineComponent({
  name: "PageListeSentiers",
  props: {
    sentiers: Array
  },
  data() {
    return {
      showSortModal: false,
      showFilterModal: false
    }
  },
  methods: {
    onGroupContainerClick(id) {
      window.location.href = `/sentiers/${id}`;
    },
    redirectToMap() {
      window.location.href = '/carte'
    },
    closeModal() {
      this.showSortModal = false;
      this.showFilterModal = false;
    }
  }
})
</script>

<style module>
.searchContainer {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 16px;
  background-color: #f5f5f5;
  border-bottom: 1px solid #ddd;
  margin-bottom: 16px;
}

.searchWrapper {
  display: flex;
  align-items: center;
  width: 100%;
  max-width: 600px;
  margin-bottom: 16px;
  position: relative;
}

.searchInput {
  flex: 1;
  padding: 8px 16px;
  border: 1px solid #ccc;
  border-radius: 24px;
  padding-right: 40px;
}

.searchIcon {
  position: absolute;
  right: 12px;
  width: 20px;
  height: 20px;
  cursor: pointer;
}

.buttonsWrapper {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
}

.button {
  padding: 8px 16px;
  border: 1px solid #ccc;
  border-radius: 24px;
  background-color: #fff;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.mapIcon {
  width: 24px;
  height: 24px;
  cursor: pointer;
}

.groupParent {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  gap: 16px;
  padding: 16px;
}

.card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.2s;
  width: 300px;
}

.card:hover {
  transform: translateY(-5px);
}

.image {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.content {
  padding: 16px;
}

.title {
  font-size: 1.25rem;
  margin-bottom: 8px;
}

.description {
  font-size: 1rem;
  color: #555;
  margin-bottom: 16px;
}

.info {
  display: flex;
  align-items: center;
  font-size: 0.875rem;
  color: #777;
}

.length {
  margin-right: 8px;
}

.separator {
  margin: 0 8px;
}

.duration {
  margin-left: 8px;
}

.modalBackdrop {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: flex-end;
  z-index: 1000;
}

.modal {
  background: white;
  width: 100%;
  max-width: 400px;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
  padding: 16px;
  box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.2);
}

.modalHeader {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
  margin-bottom: 16px;
}

.closeButton {
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #555;
}

.modalContent {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.modalContent h4 {
  margin: 0;
  margin-bottom: 8px;
  font-size: 1rem;
}

.modalFooter {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid #ddd;
  margin-top: 16px;
}

.resetButton {
  background: none;
  border: 1px solid green;
  border-radius: 24px;
  padding: 8px 16px;
  color: green;
  cursor: pointer;
}

.validateButton {
  background: green;
  border: none;
  border-radius: 24px;
  padding: 8px 16px;
  color: white;
  cursor: pointer;
}
</style>
