<script setup>
// Vous pouvez ajouter ici des scripts si nécessaire
</script>
 
<template>
    <footer class="footer">
        <div class="container mx-auto rectangle-parent">
            <div class="group-child"></div>
            <img class="group-icon" alt="" src="Group.svg" />
 
            <div class="scvd-sentiers-culturels-container">
                <span class="scvd-sentiers-culturels-container1">
                    <p class="scvd-sentiers-culturels-vaudo">
                        <b>2024 SCVD, Sentiers culturels vaudois</b>
                    </p>
                    <p class="scvd-sentiers-culturels-vaudo">
                        <b>Etat de Vaud</b>
                    </p>
                    <p class="scvd-sentiers-culturels-vaudo">
                        <b>&nbsp;</b>
                    </p>
                    <p class="scvd-sentiers-culturels-vaudo">
                        Les designs sont des marques par l’état de Vaud en Suisse ainsi que dans certaines autres juridictions.
                    </p>
                </span>
            </div>
            <img class="group-item" alt="" src="Rectangle 11.png" />
        </div>
    </footer>
</template>
 
<style scoped>
.footer {
    background: #bfd2a6; /* Adapté au thème clair */
    color: #333; /* Texte de couleur sombre */
    padding: 20px 0;
    text-align: center;
}
 
.dark .footer {
    background: #333; /* Adapté au thème sombre */
    color: #f8f8f8; /* Texte de couleur claire */
}
 
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 15px;
}
 
.rectangle-parent {
    position: relative;
}
 
 
.group-icon {
    width: 50px;
    height: auto;
}
 
.scvd-sentiers-culturels-container {
    text-align: left;
}
 
.scvd-sentiers-culturels-container1 {
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    line-height: 1.5;
}
 
.scvd-sentiers-culturels-vaudo {
    margin: 5px 0;
}
 
.group-item {
    width: 100%;
    height: auto;
    margin-top: 10px;
}
</style>