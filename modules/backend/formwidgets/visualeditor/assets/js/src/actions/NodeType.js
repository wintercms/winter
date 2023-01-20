import EditorAction from '../EditorAction';

export default class NodeType extends EditorAction {
    icon() {
        return 'paragraph';
    }

    tooltip() {
        return 'Block type';
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
                label: 'Paragraph',
                active: this.status() === 'P',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setParagraph()
                        .run();
                },
            },
            {
                label: 'Heading 1',
                active: this.status() === 'H1',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setHeading({ level: 1 })
                        .run();
                },
            },
            {
                label: 'Heading 2',
                active: this.status() === 'H2',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setHeading({ level: 2 })
                        .run();
                },
            },
            {
                label: 'Heading 3',
                active: this.status() === 'H3',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setHeading({ level: 3 })
                        .run();
                },
            },
            {
                label: 'Heading 4',
                active: this.status() === 'H4',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setHeading({ level: 4 })
                        .run();
                },
            },
            {
                label: 'Heading 5',
                active: this.status() === 'H5',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setHeading({ level: 5 })
                        .run();
                },
            },
            {
                label: 'Code',
                active: this.status() === 'CODE',
                enabled: true,
                action: () => {
                    this.editor.chain()
                        .focus()
                        .setCodeBlock()
                        .run();
                },
            },
        ];
    }

    status() {
        try {
            const currentNode = this.editor.state.selection.$head.parent;
            if (currentNode.type.name === 'paragraph') {
                return 'P';
            }
            if (currentNode.type.name === 'heading') {
                return `H${currentNode.attrs.level}`;
            }
            if (currentNode.type.name === 'codeBlock') {
                return 'Code';
            }
        } catch {
            return null;
        }

        return null;
    }
}
