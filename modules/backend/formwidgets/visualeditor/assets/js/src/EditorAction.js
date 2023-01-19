export default class EditorAction {
    constructor(editor) {
        this.editor = editor;
    }

    icon() {
        return 'location-crosshairs';
    }

    isActive() {
        return false;
    }

    isEnabled() {
        return false;
    }

    action() {
    }
}
