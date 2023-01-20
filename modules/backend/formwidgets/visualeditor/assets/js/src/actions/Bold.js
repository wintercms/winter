import EditorAction from '../EditorAction';

export default class Bold extends EditorAction {
    icon() {
        return 'bold';
    }

    tooltip() {
        return 'Bold';
    }

    shortcutKey() {
        return 'B';
    }

    isActive() {
        return this.editor.isActive('bold');
    }

    isEnabled() {
        return this.editor.can().toggleBold();
    }

    action() {
        this.editor.chain()
            .focus()
            .toggleBold()
            .run();
    }
}
