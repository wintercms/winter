import EditorAction from '../EditorAction';

export default class TextAlign extends EditorAction {
    icon() {
        switch (this.currentAlignment()) {
            case 'center':
                return 'align-center';
            case 'right':
                return 'align-right';
            case 'justify':
                return 'align-justify';
            default:
                return 'align-left';
        }
    }

    tooltip() {
        return 'Alignment';
    }

    isActive() {
        return false;
    }

    isEnabled() {
        return true;
    }

    isDropdown() {
        return true;
    }

    dropdownItems() {
        return [
            {
                label: 'Align Left',
                icon: 'align-left',
                active: (this.currentAlignment() === 'left' || !this.currentAlignment()),
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setTextAlign('left')
                        .run();
                },
            },
            {
                label: 'Align Center',
                icon: 'align-center',
                active: this.currentAlignment() === 'center',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setTextAlign('center')
                        .run();
                },
            },
            {
                label: 'Align Right',
                icon: 'align-right',
                active: this.currentAlignment() === 'right',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setTextAlign('right')
                        .run();
                },
            },
            {
                label: 'Align Justify',
                icon: 'align-justify',
                active: this.currentAlignment() === 'justify',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setTextAlign('justify')
                        .run();
                },
            },
        ];
    }

    currentAlignment() {
        try {
            const currentNode = this.editor.state.selection.$head.parent;
            if (!currentNode.type.name === 'paragraph' && !currentNode.type.name === 'heading') {
                return null;
            }
            return currentNode.attrs.textAlign || null;
        } catch {
            return null;
        }
    }
}
