import EditorAction from '../EditorAction';

export default class OrderedList extends EditorAction {
    icon() {
        return 'list-ol';
    }

    tooltip() {
        return 'Ordered List';
    }

    isActive() {
        return this.editor.isActive('orderedList');
    }

    isEnabled() {
        return this.editor.can().toggleOrderedList();
    }

    action() {
        this.editor.chain()
            .focus()
            .toggleOrderedList()
            .run();
    }
}
