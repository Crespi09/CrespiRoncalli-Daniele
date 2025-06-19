import './assets/main.css'

import { createApp } from 'vue'
import App from '../src/App.vue'
import router from './router'
import { BehaviorSubject } from 'rxjs';
import { jwtDecode } from 'jwt-decode';
import PrimeVue from 'primevue/config';
import Aura from '@primeuix/themes/aura';
import initRouter from './router';
import ToastService from 'primevue/toastservice';

// import '@primevue/themes/aura-light-green/theme.css'

import 'primeicons/primeicons.css'


const app = createApp(App);

app.use(ToastService);

interface CustomJwtPayload {
    exp: number;
    data: {
        email?: string
    };
}

const token = localStorage.getItem('token');

let email = null;

if (token) {

    try {

        console.log("token: " + token);

        const decodedToken = jwtDecode<CustomJwtPayload>(token);

        if (!(Date.now() >= decodedToken.exp! * 1000)) {

            email = decodedToken.data.email;
        }

    } catch (e) {
        console.log(e);
    }

}

const currentUser$ = new BehaviorSubject<string | null>(email!);

app.provide('currentUser$', currentUser$);

app.use(PrimeVue, {
    theme: {
        preset: Aura,
        options: {
            darkModeSelector: '.my-app-dark',
        }
    }
});
app.use(initRouter(currentUser$))
app.mount('#app')