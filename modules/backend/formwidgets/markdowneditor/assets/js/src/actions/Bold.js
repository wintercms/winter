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
        return false;
    }

    isEnabled() {
        return true;
    }

    action() {
        this.monaco.wrap('**', '**');
    }
}
