    

// import './bootstrap';
import '../css/app.css';
 
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import axios from 'axios';
import Header from './Components/Header.vue'; // Importer le Header
import Footer from './Components/Footer.vue'; // Importer le Header
 
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
 
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
 
createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        const vueApp = createApp({
            render: () => h('div', [
                h(Header),
                h(App, props),
                h(Footer)
            ])
        });
 
        vueApp.use(plugin)
            .use(ZiggyVue)
            .mount(el);
 
        vueApp.component('Header', Header);
        vueApp.component('Footer', Footer);
        vueApp.component('SentiersList', SentiersList);
        vueApp.component('SentierForm', SentierForm);
    },
    progress: {
        color: '#4B5563',
    },
});