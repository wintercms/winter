import { createApp } from 'vue'
import IconPicker from './components/IconPicker.vue'

window.addEventListener('load', () => {
    const elements = document.querySelectorAll("[data-control='iconpicker']:not([data-v-app])");
    elements.forEach(element => {
        element.querySelector(".input-group").addEventListener("click", () => {
            snowboard.request(null, element.dataset.alias + "::onLoadIconLibrary", {
                success: (data) => {
                    let iconPicker = createApp(IconPicker, {
                        ...element.dataset,
                        fontLibraries: JSON.parse(data.result)
                    }).mount(element);

                    iconPicker.togglePicker();
                },
            })
        });
    });
});
