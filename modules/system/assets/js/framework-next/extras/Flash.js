export default class Flash extends Winter.Module {
    constructor(winter, message, type, duration) {
        super(winter);

        this.message = message;
        this.type = type || 'default';
        this.duration = duration || 7;

        this.clear();
        this.timer = null;
        this.create();
    }

    dependencies() {
        return ['transition'];
    }

    destructor() {
        if (this.timer !== null) {
            window.clearTimeout(this.timer);
        }

        if (this.flash) {
            this.flash.remove();
            this.flash = null;
        }
    }

    create() {
        this.flash = document.createElement('P');
        this.flash.innerHTML = this.message;
        this.flash.classList.add(`flash-message ${this.type}`);
        this.flash.attributes.setNamedItem('data-control', null);
        this.flash.addEventListener('click', () => this.remove());
        this.flash.addEventListener('mouseover', () => this.stopTimer());
        this.flash.addEventListener('mouseout', () => this.startTimer());

        // Add to body
        document.body.appendChild(this.flash);

        this.winter.transition(this.flash, 'enter', () => {
            this.startTimer();
        });
    }

    remove() {
        this.stopTimer();

        this.winter.transition(this.flash, 'leave', () => {
            this.flash.remove();
            this.flash = null;
            this.destructor();
        });
    }

    clear() {
        document.querySelectorAll('body > p.flash-message').remove();
    }

    startTimer() {
        this.timer = window.setTimeout(() => this.remove(), this.duration * 1000);
    }

    stopTimer() {
        window.clearTimeout(this.timer);
    }
}
