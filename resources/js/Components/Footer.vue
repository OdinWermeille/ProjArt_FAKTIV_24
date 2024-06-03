<script setup>
// Ajoutez ici des scripts si nécessaire
</script>

<template>
    <div class="app-container">
        <!-- Contenu principal -->
        <div class="main-content">
            <!-- Votre contenu ici -->
        </div>
        
        <!-- Pied de page -->
        <footer class="footer">
            <div class="footer-content">
                <div class="logo-container">
                    <img class="logo" src="/images/Group.svg" alt="SCVD Logo" />
                    <img class="logo" src="/images/Rectangle 11.png" alt="Canton de Vaud Logo" />
                </div>
                <div class="text-container">
                    <p><strong>2024 SCVD, Sentiers culturels vaudois</strong></p>
                    <p><strong>Etat de Vaud</strong></p>
                    <p>Les designs sont des marques par l’état de Vaud en Suisse ainsi que dans certaines autres juridictions.</p>
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
.app-container {
    display: flex;
    flex-direction: column;
    min-height: 10vh; /* Assurez-vous que le conteneur prend toute la hauteur de la fenêtre */
}

.main-content {
    flex: 1; /* Permet au contenu principal de prendre autant d'espace qu'il le peut, poussant le footer vers le bas */
    display: flex;
    flex-direction: column;
}

.content {
    flex-grow: 1;
    /* Ajoutez du style supplémentaire ici si nécessaire */
}

.footer {
    background: #bfd2a6;
    color: #333;
    padding: 20px;
    text-align: center;
    width: 100%;
    box-sizing: border-box;
    position: relative;
    bottom: 0;
    left: 0;
}

.footer-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.logo-container {
    display: flex;
    justify-content: space-between;
    width: 100%;
    max-width: 500px;
    margin-bottom: 20px;
}

.logo {
    height: auto;
    max-width: 100%;
}

.text-container {
    text-align: left;
    max-width: 500px;
    font-family: 'Inter', sans-serif;
    font-size: 14px;
    line-height: 1.5;
}

.text-container p {
    margin: 5px 0;
}
</style>
