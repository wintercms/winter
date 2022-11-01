import { createApp } from 'vue'
import App from './components/App.vue'

window.addEventListener('load', () => {
    const elements = document.querySelectorAll("[data-control='iconpicker']:not([data-v-app])");
    elements.forEach(element => {
        element.querySelector(".input-group").addEventListener("click", () => {
            snowboard.request(null, "onLoadIconLibrary", {
                success: (data) => {
                    let app = createApp(App, {
                        ...element.dataset,
                        fontLibraries: JSON.parse(data.result)
                    }).mount(element);

                    app.togglePicker();
                },
            })
        });
    });
});
