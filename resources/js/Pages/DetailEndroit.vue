<template>
    <div class="container">
        <div class="content">
            <h1 class="titre">{{ endroit.nom }}</h1>
            <img class="image-endroit" :alt="endroit.nom" :src="`/${endroit.image_url}`" />
            <div class="localit-wrapper">
                <div class="localit">Localité</div>
                <b class="lausanne">{{ endroit.localite }}</b>
            </div>
            <div class="voir-sur-la-carte-wrapper">
                <button class="voir-sur-la">Voir sur la carte</button>
            </div>
            <div class="description-wrapper">
                <div class="description">DESCRIPTION</div>
                <div class="la-cathdrale-de">
                    {{ endroit.description }}
                </div>
            </div>
            <img class="map-image" alt="Map" src="map.png" />
        </div>
    </div>
</template>
 
<script>
export default {
    name: 'DetailEndroit',
    props: {
        endroit: Object,
    },
    
};
</script>
 
<style scoped>
.container {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    font-family: 'Roboto', sans-serif;
    color: #212121;
}
 
 
.content {
    padding: 20px 0;
}
 
.titre {
    font-size: 24px;
    font-weight: bold;
    height : 48px;
}
 
.image-endroit {
    width: 100%;
    height: auto;
    object-fit: cover;
    margin-bottom: 20px;
    border-radius: 10px; /* Effet arrondi */
}
 
.localit-wrapper {
    margin-bottom: 20px;
}
 
.localit {
    font-size: 14px;
    color: #666;
}
 
.lausanne {
    font-size: 16px;
    font-weight: bold;
}
 
.voir-sur-la-carte-wrapper {
    display: flex;
    justify-content: center;
    margin-bottom: 20px;
}
 
.voir-sur-la {
    background-color: #4a8c2a;
    color: #fafafa;
    text-transform: uppercase;
    font-weight: 500;
    padding: 10px 20px;
    border-radius: 20px;
    border: none;
    cursor: pointer;
}
 
.description-wrapper {
    margin-bottom: 20px;
}
 
.description {
    font-size: 14px;
    font-weight: bold;
    margin-bottom: 10px;
}
 
.la-cathdrale-de {
    font-size: 14px;
    line-height: 1.6;
    color: #666;
}
 
.map-image {
    width: 100%;
    height: auto;
    margin-bottom: 20px;
    border-radius: 10px; /* Effet arrondi pour l'image de la carte aussi */
}
 
 
</style>