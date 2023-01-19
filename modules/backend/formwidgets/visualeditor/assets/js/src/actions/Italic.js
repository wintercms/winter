import EditorAction from '../EditorAction';

export default class Italic extends EditorAction {
    icon() {
        return 'italic';
    }

    isActive() {
        return this.editor.isActive('italic');
    }

    isEnabled() {
        return true;
    }

    action() {
        this.editor.chain()
            .focus()
            .toggleItalic()
            .run();
    }
}
