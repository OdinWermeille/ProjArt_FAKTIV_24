<template>
  <div class="page-container">
    <!-- Barre de recherche et boutons -->
    <div :class="$style.searchContainer">
      <div :class="$style.searchWrapper">
        <input v-model="searchQuery" type="text" :class="$style.searchInput" placeholder="Rechercher" @input="applyFiltersAndSearch" />
        <img :class="$style.searchIcon" alt="Search Icon" src="/images/icon.svg" />
      </div>
      <div :class="$style.buttonsWrapper">
        <button :class="$style.button" @click="showSortModal = true">Trier par</button>
        <button :class="$style.button" @click="showFilterModal = true">Filtrer</button>
        <img :class="$style.mapIcon" alt="Map Icon" src="/images/map.svg" @click="redirectToMap" />
      </div>
    </div>

    <!-- Liste des sentiers ou message d'absence de sentiers -->
    <div class="content">
      <div v-if="filteredSentiers.length === 0" :class="$style.noResults">
        <p>Aucun sentier ne correspond aux filtres appliqués.</p>
      </div>
      <div v-else :class="$style.groupParent">
        <div v-for="sentier in filteredSentiers" :key="sentier.id" :class="$style.card" @click="onGroupContainerClick(sentier.nom)">
          <img :class="$style.image" :alt="sentier.nom" :src="`/${sentier.image_url}`" />
          <div :class="$style.content">
            <b :class="$style.title">{{ sentier.nom }}</b>
            <div :class="$style.description">{{ truncateDescription(sentier.description) }}</div>
            <div :class="$style.info">
              <div :class="$style.length">{{ sentier.longueur }} km</div>
              <div :class="$style.separator">•</div>
              <div :class="$style.theme">{{ sentier.theme.nom }}</div>
              <div :class="$style.separator">•</div>
              <div :class="$style.duration">{{ formatDuration(sentier.duree) }}</div>
            </div>
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
            <input type="radio" name="sort" value="newest" v-model="sortOption" />
            Derniers ajouts
          </label>
          <label>
            <input type="radio" name="sort" value="oldest" v-model="sortOption" />
            Les plus anciens
          </label>
        </div>
        <div :class="$style.modalFooter">
          <button :class="$style.resetButton" @click="resetSort">RÉINITIALISER</button>
          <button :class="$style.validateButton" @click="applySort">VALIDER</button>
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
            <h4><strong>Activité</strong></h4>
            <div :class="$style.radioGroup">
              <label>
                <input type="radio" name="activity" value="tout" v-model="filterActivity" />
                Tout
              </label>
              <label>
                <input type="radio" name="activity" value="Historique" v-model="filterActivity" />
                Historique
              </label>
              <label>
                <input type="radio" name="activity" value="Arts & Culture" v-model="filterActivity" />
                Arts & Culture
              </label>
              <label>
                <input type="radio" name="activity" value="Nature" v-model="filterActivity" />
                Nature
              </label>
              <label>
                <input type="radio" name="activity" value="Architecture" v-model="filterActivity" />
                Architecture
              </label>
              <label>
                <input type="radio" name="activity" value="Street Art" v-model="filterActivity" />
                Street Art
              </label>
              <label>
                <input type="radio" name="activity" value="Sportif" v-model="filterActivity" />
                Sportif
              </label>
              <label>
                <input type="radio" name="activity" value="Gastronomie" v-model="filterActivity" />
                Gastronomie
              </label>
              <label>
                <input type="radio" name="activity" value="Éphémère" v-model="filterActivity" />
                Éphémère
              </label>
            </div>
          </div>
          <div>
            <h4><strong>Distance</strong></h4>
            <div :class="$style.radioGroupHorizontal">
              <label>
                <input type="radio" name="distance" value="tout" v-model="filterDistance" />
                Tout
              </label>
              <label>
                <input type="radio" name="distance" value="0-5" v-model="filterDistance" />
                0-5 km
              </label>
              <label>
                <input type="radio" name="distance" value="6-10" v-model="filterDistance" />
                6-10 km
              </label>
              <label>
                <input type="radio" name="distance" value="11+" v-model="filterDistance" />
                11+ km
              </label>
            </div>
          </div>
        </div>
        <div :class="$style.modalFooter">
          <button :class="$style.resetButton" @click="resetFilters">RÉINITIALISER</button>
          <button :class="$style.validateButton" @click="applyFilters">VALIDER</button>
        </div>
      </div>
    </div>
  </div>
</template>


<script lang="ts">
import { defineComponent, computed } from 'vue';

export default defineComponent({
  name: "PageListeSentiers",
  props: {
    sentiers: {
      type: Array,
      required: true
    }
  },
  data() {
    return {
      showSortModal: false,
      showFilterModal: false,
      sortOption: '',
      filterActivity: 'tout',
      filterDistance: 'tout',
      searchQuery: '',
      allSentiers: this.sentiers,
      filteredSentiers: this.sentiers
    }
  },
  methods: {
    onGroupContainerClick(nom) {
      const slug = nom.toLowerCase().replace(/\s+/g, '-'); // Convertir le nom en un slug URL-friendly
      window.location.href = `/sentiers/${slug}`;
    },
    redirectToMap() {
      window.location.href = '/carte';
    },
    closeModal() {
      this.showSortModal = false;
      this.showFilterModal = false;
    },
    truncateDescription(description) {
      const words = description.split(' ');
      return words.length > 30 ? words.slice(0, 30).join(' ') + '...' : description;
    },
    resetSort() {
      this.sortOption = '';
      this.applyFiltersAndSearch();
    },
    applySort() {
      if (this.sortOption === 'newest') {
        this.filteredSentiers.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      } else if (this.sortOption === 'oldest') {
        this.filteredSentiers.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      }
      this.closeModal();
    },
    resetFilters() {
      this.filterActivity = 'tout';
      this.filterDistance = 'tout';
      this.applyFiltersAndSearch();
    },
    applyFilters() {
      this.applyFiltersAndSearch();
      this.closeModal();
    },
    applyFiltersAndSearch() {
      this.filteredSentiers = this.allSentiers.filter(sentier => {
        const matchActivity = this.filterActivity === 'tout' || sentier.theme.nom.trim().toLowerCase() === this.filterActivity.trim().toLowerCase();
        const matchDistance = this.filterDistance === 'tout' || (
          this.filterDistance === '0-5' && sentier.longueur <= 5 ||
          this.filterDistance === '6-10' && sentier.longueur > 5 && sentier.longueur <= 10 ||
          this.filterDistance === '11+' && sentier.longueur > 10
        );
        const matchSearchQuery = sentier.nom.toLowerCase().includes(this.searchQuery.trim().toLowerCase());
        return matchActivity && matchDistance && matchSearchQuery;
      });

      this.applySort();
    },
    formatDuration(duree) {
      if (duree >= 60) {
        const hours = Math.floor(duree / 60);
        const remainingMinutes = duree % 60;
        return `${hours}h ${remainingMinutes}min`;
      }
      return `${duree} min`;
    }
  },
  watch: {
    sentiers(newSentiers) {
      this.allSentiers = newSentiers;
      this.applyFiltersAndSearch();
    }
  },
  created() {
    this.allSentiers = this.sentiers;
    this.applyFiltersAndSearch();
  }
})
</script>


<style module>
html,
body {
  font-family: "Inter", sans-serif;
  height: 100%;
  margin: 0;
}

.page-container {
  display: flex;
  flex-direction: column;
  min-height: 100vh;
}

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
  font-family: "Inter", sans-serif;
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
  font-family: "Inter", sans-serif;
  padding: 8px 16px;
  border: 1px solid #F0F0F0;
  border-radius: 24px;
  background-color: #FAFAFA;
  cursor: pointer;
  display: flex;
  align-items: center;
}

.button:hover {
  background-color: #F0F0F0;
  /* Change la couleur de fond au survol */
  color: #BFD2A6;
  /* Change la couleur de texte au survol */
}

.mapIcon {
  width: 24px;
  height: 24px;
  cursor: pointer;
}

.mapIcon:hover {
  transform: translateY(-5px);
}

.noResults {
  font-family: "Inter", sans-serif;
  text-align: center;
  margin-top: 20px;
  font-size: 1.25rem;
  color: #7D7D7D;
}

.groupParent {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  gap: 16px;
  padding: 16px;
}

.card {
  background: #FAFAFA;
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
  font-family: "Inter", sans-serif;
  font-size: 1.25rem;
  margin-top: 12px;
  margin-bottom: 12px;
}

.description {
  font-family: "Inter", sans-serif;
  font-size: 14px;
  color: #7D7D7D;
  margin-top: 12px;
  margin-bottom: 12px;
}

.info {
  font-family: "Inter", sans-serif;
  display: flex;
  align-items: center;
  font-size: 0.875rem;
  color: #4A8C2A;

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
  font-family: "Inter", sans-serif;
  background: #FAFAFA;
  width: 100%;
  max-width: 400px;
  border-top-left-radius: 16px;
  border-top-right-radius: 16px;
  padding: 16px;
  box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.2);
}

.modalHeader {
  font-family: "Inter", sans-serif;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #F0F0F0;
  padding-bottom: 8px;
  margin-bottom: 16px;
}

.closeButton {
  font-family: "Inter", sans-serif;
  background: none;
  border: none;
  font-size: 1.5rem;
  cursor: pointer;
  color: #7D7D7D;
}

.modalContent {
  font-family: "Inter", sans-serif;
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.modalContent h4 {
  font-family: "Inter", sans-serif;
  margin: 0;
  margin-bottom: 8px;
  font-size: 1rem;
}

.radioGroup {
  font-family: "Inter", sans-serif;
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.radioGroupHorizontal {
  font-family: "Inter", sans-serif;
  display: flex;
  flex-direction: row;
  gap: 16px;
}

.radioGroup label {
  font-family: "Inter", sans-serif;
  display: flex;
  align-items: center;
  gap: 8px;
}

.modalFooter {
  font-family: "Inter", sans-serif;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 16px;
  border-top: 1px solid #F0F0F0;
  margin-top: 16px;
}

.resetButton {
  font-family: "Inter", sans-serif;
  background: none;
  border: 1px solid #4A8C2A;
  border-radius: 24px;
  padding: 8px 16px;
  color: #4A8C2A;
  cursor: pointer;
}

.validateButton {
  font-family: "Inter", sans-serif;
  background: #4A8C2A;
  border: none;
  border-radius: 24px;
  padding: 8px 16px;
  color: #FAFAFA;
  cursor: pointer;
}
</style>
