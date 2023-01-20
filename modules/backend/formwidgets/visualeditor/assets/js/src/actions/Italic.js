import EditorAction from '../EditorAction';

export default class Italic extends EditorAction {
    icon() {
        return 'italic';
    }

    tooltip() {
        return 'Italic';
    }

    shortcutKey() {
        return 'I';
    }

    isActive() {
        return this.editor.isActive('italic');
    }

    isEnabled() {
        return this.editor.can().toggleItalic();
    }

    action() {
        this.editor.chain()
            .focus()
            .toggleItalic()
            .run();
    }
}
