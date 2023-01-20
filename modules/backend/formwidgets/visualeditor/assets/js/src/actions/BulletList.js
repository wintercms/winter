import EditorAction from '../EditorAction';

export default class BulletList extends EditorAction {
    icon() {
        return 'list-ul';
    }

    tooltip() {
        return 'Unordered List';
    }

    isActive() {
        return this.editor.isActive('bulletList');
    }

    isEnabled() {
        return this.editor.can().toggleBulletList();
    }

    action() {
        this.editor.chain()
            .focus()
            .toggleBulletList()
            .run();
    }
}
