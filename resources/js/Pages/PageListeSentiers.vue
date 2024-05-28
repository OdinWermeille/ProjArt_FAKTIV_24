<template>
  <div :class="$style.groupParent">
    <div v-for="sentier in sentiers" :key="sentier.id" :class="$style.rectangleParent" @click="onGroupContainerClick(sentier.id)">
      <div :class="$style.groupChild"></div>
      <img :class="$style.groupItem" :alt="sentier.nom" :src="sentier.image_url" />
      <b :class="$style.btimentsDeLausanne">{{ sentier.nom }}</b>
      <div :class="$style.sentierHistoriqueAutour">{{ sentier.description }}</div>
      <div :class="$style.kmParent">
        <div :class="$style.km">{{ sentier.longueur }} km</div>
        <div :class="$style.histoire">{{ sentier.theme.name }}</div>
        <div :class="$style.div">.</div>
      </div>
    </div>
    <!-- Boutons pour Trier et Filtrer -->
    <div :class="$style.rectangleContainer" @click="onGroupContainer2Click">
      <div :class="$style.rectangleDiv"></div>
      <div :class="$style.trierPar">Trier par</div>
      <div :class="$style.div2">&lt;</div>
    </div>
    <div :class="$style.groupDiv" @click="onGroupContainer3Click">
      <div :class="$style.rectangleDiv"></div>
      <div :class="$style.filtrer">Filtrer</div>
      <div :class="$style.div3">&lt;</div>
    </div>
    <!-- Boutons Carte et Liste -->
    <div :class="$style.groupContainer">
      <div :class="$style.selectorselected3Wrapper">
        <div :class="$style.selectorselected3">
          <a :class="$style.filtreBtn" href="/carte">
            <div :class="$style.carte">Carte</div>
          </a>
          <a :class="$style.filtreBtn1" href="/sentiers">
            <div :class="$style.carte">Liste</div>
          </a>
        </div>
      </div>
      <div :class="$style.filtreBtn2">
        <div :class="$style.listeWrapper">
          <div :class="$style.liste1">Liste</div>
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
    methods: {
      onGroupContainerClick(id) {
        Inertia.get(`/sentiers/${id}`)
      },
      onGroupContainer2Click() {
        // Add your code here
      },
      onGroupContainer3Click() {
        // Add your code here
      },
      onFiltreBtnContainerClick() {
        // Add your code here
      },
      onFiltreBtnContainer1Click() {
        // Add your code here
      }
    }
  })
</script>

<style module>
/* CSS Styles */
.groupParent {
  width: 100%;
  position: relative;
  height: auto;
  text-align: left;
  font-size: 18px;
  color: #212121;
  font-family: Inter;
  padding: 20px;
}

.rectangleParent {
  width: 100%;
  max-width: 288px;
  margin: 20px auto;
  position: relative;
  cursor: pointer;
}

.groupChild {
  width: 100%;
  height: 305px;
  border-radius: 11px;
  background-color: #f0f0f0;
}

.groupItem {
  width: 100%;
  height: auto;
  max-height: 175px;
  border-radius: 5px;
  object-fit: cover;
  margin-top: 13px;
}

.btimentsDeLausanne,
.musesDart {
  display: block;
  margin-top: 20px;
  margin-left: 17px;
  letter-spacing: 0.5px;
  line-height: 24px;
}

.sentierHistoriqueAutour,
.sentierArtistiqueAutour {
  display: block;
  margin-top: 10px;
  margin-left: 17px;
  font-size: 14px;
  letter-spacing: 0.5px;
  line-height: 17px;
  font-weight: 500;
}

.kmParent,
.kmGroup {
  display: flex;
  align-items: center;
  margin-top: 20px;
  margin-left: 17px;
}

.km,
.km1,
.histoire,
.art,
.div,
.div1 {
  font-size: 14px;
  letter-spacing: 0.5px;
  line-height: 24px;
  font-weight: 300;
}

.rectangleContainer,
.groupDiv {
  width: 45%;
  margin: 10px 2.5%;
  cursor: pointer;
  font-size: 16px;
}

.rectangleDiv {
  width: 100%;
  height: 25px;
  border-radius: 5px;
  border: 0.5px solid #7d7d7d;
  box-sizing: border-box;
}

.trierPar,
.filtrer {
  display: flex;
  align-items: center;
  letter-spacing: 0.5px;
  line-height: 24px;
  font-weight: 300;
  padding-left: 4.44%;
}

.div2,
.div3 {
  display: flex;
  align-items: center;
  justify-content: center;
  transform: rotate(-90deg);
  transform-origin: 0 0;
  font-size: 21px;
  letter-spacing: 0.5px;
  line-height: 24px;
  font-family: Roboto;
}

.groupContainer {
  width: 100%;
  text-align: center;
  font-size: 17px;
  margin-top: 20px;
}

.selectorselected3Wrapper {
  width: 100%;
  height: 45px;
  margin-bottom: 20px;
}

.selectorselected3 {
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-radius: 40px;
  border: 0.5px solid #7d7d7d;
  box-sizing: border-box;
  width: 100%;
  height: 100%;
  padding: 5px;
}

.filtreBtn,
.filtreBtn1 {
  flex: 1;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 15px 25px;
  border-radius: 25px;
  cursor: pointer;
  text-decoration: none;
  color: inherit;
}

.filtreBtn1 {
  border-radius: 0 40px 40px 0;
}

.filtreBtn2 {
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.1) inset, 0 3px 2px rgba(255, 255, 255, 0.25) inset, 0 0 5px rgba(0, 0, 0, 0.1);
  border-radius: 25px;
  background-color: #bfd2a6;
  border: 0.5px solid #bfd2a6;
  width: 100%;
  height: 35px;
}

.listeWrapper {
  width: 40px;
  height: 13px;
}

.liste1 {
  font-size: 13px;
  font-weight: 500;
}

@media (max-width: 768px) {
  .rectangleParent,
  .rectangleGroup,
  .rectangleParent1 {
    width: 90%;
    margin: 20px auto;
  }
  
  .rectangleContainer,
  .groupDiv {
    width: 100%;
    margin: 10px 0;
  }
}
</style>
