<template>
  <div class="min-h-screen flex flex-col justify-start items-center pt-20 sm:pt-32">
    <div class="group-parent inter-text">
      <div class="group-container">
        <div class="group-child"></div>
        <div class="rectangle-wrapper">
          <h2 class="ajouter-un-lieu">Bienvenue</h2>
          <h3 class="sous-titre">Authentifiez-vous pour commencer</h3>

          <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
            {{ status }}
          </div>

          <form @submit.prevent="submit">
            <div class="input-group">
              <input id="username" type="text" :class="{ 'group-item': true, 'input-error': form.errors.username }"
                v-model="form.username" required autofocus autocomplete="username" placeholder="Nom d'utilisateur" />
              <span v-if="form.errors.username" class="error-message"><i class="fas fa-exclamation-circle"></i>{{
                form.errors.username }}</span>
            </div>

            <div class="input-group">
              <input id="password" type="password" :class="{ 'group-item': true, 'input-error': form.errors.password }"
                v-model="form.password" required autocomplete="current-password" placeholder="Mot de passe" />
              <span v-if="form.errors.password" class="error-message"><i class="fas fa-exclamation-circle"></i>{{
                form.errors.password }}</span>
            </div>
            <div v-if="verifIfError" class="error-message"><i class="fas fa-exclamation-circle"></i>{{ verifIfError }}
            </div>

            <div class="ajouter-wrapper">
              <button type="submit" class="ajouter" :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing">
                Connexion
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import InputError from '@/Components/InputError.vue';
import { useForm } from '@inertiajs/vue3';
import { Inertia } from '@inertiajs/inertia';
import { ref } from 'vue';

defineProps({
  canResetPassword: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const verifIfError = ref(window.location.search.includes('error=AuthFailed') ? 'Nom d\'utilisateur ou mot de passe incorrect' : '');

const form = useForm({
  username: '',
  password: '',
  remember: false,
});


const submit = () => {
  form.post(route('login'), {
    onFinish: () => {
      form.reset('password');
      if (Object.keys(form.errors).length === 0) {
        Inertia.visit('/sentiers');
      }
      window.location.href = '/login?error=AuthFailed';
    },
  });
};
</script>

<style scoped>
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
/* Import Font Awesome */

.inter-text {
  font-family: "Inter", sans-serif;
  font-optical-sizing: auto;
  font-weight: 400;
  font-style: normal;
  font-variation-settings: "slnt" 0;
}

.group-child {
  position: absolute;
  top: 0px;
  left: 0px;
  border-radius: 11px;
  background-color: #f0f0f0;
  width: 100%;
  height: 100%;
  border: none;
  /* Enlever le contour */
}

.rectangle-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
  padding: 30px 15px;
  /* Ajouter plus de padding en haut et en bas */
  box-sizing: border-box;
}

.ajouter-un-lieu {
  font-size: 18px;
  font-weight: bold;
  /* Mettre le titre en gras */
  letter-spacing: 0.5px;
  line-height: 24px;
  display: flex;
  font-family: "Inter", sans-serif;
  color: #212121;
  text-align: center;
  align-items: center;
  justify-content: center;
  margin-bottom: 10px;
}

.sous-titre {
  font-size: 18px;
  /* Même taille que le titre */
  font-weight: normal;
  /* Pas en gras */
  letter-spacing: 0.5px;
  line-height: 24px;
  display: flex;
  font-family: "Inter", sans-serif;
  color: #212121;
  text-align: center;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}

.input-group {
  margin-bottom: 20px;
  /* Augmenter la marge entre les champs */
}

.group-item {
  border-radius: 10px;
  border: 1px solid #7d7d7d;
  box-sizing: border-box;
  width: calc(100% - 32px);
  /* Ajouter de la marge sur les côtés */
  height: 50px;
  padding: 12px;
  /* Ajouter un padding pour un espacement uniforme */
  font-size: 16px;
  font-family: "Inter", sans-serif;
  background-color: transparent;
  /* Enlever le fond blanc */
  margin: 0 16px;
  /* Ajouter de la marge sur les côtés */
}

.input-error {
  border-color: red;
}

.ajouter {
  position: relative;
  text-transform: uppercase;
  font-weight: 500;
}

.ajouter-wrapper {
  border-radius: 20px;
  background-color: #4a8c2a;
  border: 1px solid #bfd2a6;
  display: flex;
  flex-direction: row;
  align-items: center;
  justify-content: center;
  padding: 10px 20px;
  text-align: center;
  font-size: 14px;
  color: #fafafa;
  margin: 30px auto 0 auto;
  width: fit-content;
  cursor: pointer;
}

.ajouter-wrapper:hover {
  background-color: #fafafa;
  color: #4a8c2a;
  border: #4a8c2a solid 1px;
  cursor: pointer;
}

.group-container {
  position: relative;
  top: 0px;
  left: 0px;
  width: 100%;
  height: 100%;
}

.group-parent {
  width: 100%;
  position: relative;
  height: auto;
  text-align: left;
  font-size: 16px;
  color: #7d7d7d;
  font-family: "Inter", sans-serif;
  padding: 20px;
  box-sizing: border-box;
  max-width: 700px;
}

body {
  margin: 0;
  line-height: normal;
  font-family: "Inter", sans-serif;
}

.error-message {
  color: red;
  font-size: 12px;
  margin: 0 16px;
  margin-top: 5px;
  display: flex;
  align-items: center;
}

.error-message i {
  margin-right: 5px;
}

.label-error {
  color: red;
}
</style>
