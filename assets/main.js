

import { createApp } from 'vue';
import App from './App'
import router from './router'

import {Quasar} from 'quasar';
import langEu from 'quasar/lang/eu'

import './styles/app.scss'
import {createPinia} from "pinia";

const pinia = createPinia();

createApp(App)
    .use(router)
    .use(Quasar, {lang: langEu})
    .use(pinia)
    .mount('#app');
