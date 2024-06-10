import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
//import { InertiaProgress } from '@inertiajs/progress';
import axios from 'axios';

import Header from './Components/Header.vue'; // Importer le Header
import Footer from './Components/Footer.vue'; // Importer le Footer

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

createInertiaApp({
  title: (title) => `${title} - ${appName}`,
  resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
  setup({ el, App, props, plugin }) {
    const vueApp = createApp({
      render: () => h('div', { id: 'app' }, [
        h(Header),
        h('div', { class: 'main-content' }, [
          h(App, props)
        ]),
        h(Footer)
      ])
    });

    vueApp.use(plugin)
      .use(ZiggyVue, Ziggy)
      .mount(el);

    vueApp.component('Header', Header);
    vueApp.component('Footer', Footer);
  },
  progress: {
    color: '#4B5563',
  },
});

if ('serviceWorker' in navigator) {
  window.addEventListener('load', () => {
    navigator.serviceWorker.register('/service-worker.js')
      .then(registration => {
        console.log('Service Worker enregistré avec succès:', registration);
      })
      .catch(error => {
        console.log('Erreur lors de l\'enregistrement du Service Worker:', error);
      });
  });
}
