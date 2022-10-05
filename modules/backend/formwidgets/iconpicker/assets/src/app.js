import { createApp } from 'vue'
import App from './components/App.vue'

window.addEventListener('load', () => {
    const elements = document.querySelectorAll("[data-control='iconpicker']:not([data-v-app])");
    elements.forEach(element => {
        createApp(App, {
            ...element.dataset,
            fontLibraries: JSON.parse(element.parentElement.querySelector("script[type='application/json']").textContent)
        }).mount(element)
    });
});
