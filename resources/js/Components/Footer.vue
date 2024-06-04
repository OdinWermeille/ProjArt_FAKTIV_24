<template>
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
  </template>
  
  <style scoped>
  .footer {
    background: #bfd2a6;
    color: #333;
    padding: 20px;
    text-align: center;
    width: 100%;
    box-sizing: border-box;
    flex-shrink: 0; /* Empêche le footer de rétrécir */
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
  