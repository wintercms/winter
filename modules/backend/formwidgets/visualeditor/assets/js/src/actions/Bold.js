import EditorAction from '../EditorAction';

export default class Bold extends EditorAction {
    icon() {
        return 'bold';
    }

    isActive() {
        return this.editor.isActive('bold');
    }

    isEnabled() {
        return true;
    }

    action() {
        this.editor.chain()
            .focus()
            .toggleBold()
            .run();
    }
}
