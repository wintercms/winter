import { createApp } from 'vue'
import IconPicker from './components/IconPicker.vue'

(() => {
    const init = () => {
        const elements = document.querySelectorAll("[data-control='iconpicker']:not([data-v-app])");
        elements.forEach(element => {
            element.querySelector(".input-group").addEventListener("click", () => {
                snowboard.request(element.querySelector("input")?.form || null, element.dataset.alias + "::onLoadIconLibrary", {
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
    };

    window.addEventListener('load', init);

    if (document.readyState === "complete") {
        init();
    }
})();
