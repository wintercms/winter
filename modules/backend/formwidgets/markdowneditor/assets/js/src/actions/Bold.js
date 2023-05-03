import Italic from './Italic';

export default class Bold extends Italic {
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
        if (!this.editor || !this.editor.getModel()) {
            return false;
        }

        return this.isCurrentWordBold() || this.isCurrentSelectionBold();
    }

    isEnabled() {
        return true;
    }

    action() {
        if (this.editor.getSelection().isEmpty()) {
            const word = this.selectCurrentWord();

            if (word && this.isCurrentWordItalic()) {
                this.editor.getEditor().setSelection({
                    startLineNumber: word.selection.startLineNumber,
                    startColumn: word.selection.startColumn - 1,
                    endLineNumber: word.selection.endLineNumber,
                    endColumn: word.selection.endColumn + 1,
                });
                this.editor.unwrap('*', '*');
                this.editor.wrap('**', '**');
                this.editor.focus();
                return;
            }
            if (word && this.isCurrentWordBold()) {
                this.editor.getEditor().setSelection({
                    startLineNumber: word.selection.startLineNumber,
                    startColumn: word.selection.startColumn - 2,
                    endLineNumber: word.selection.endLineNumber,
                    endColumn: word.selection.endColumn + 2,
                });
                this.editor.unwrap('**', '**');
                this.editor.focus();
                return;
            }
            if (word) {
                this.editor.getEditor().setSelection(word.selection);
            }
        } else if (this.isCurrentSelectionItalic()) {
            const selection = this.editor.getSelection();

            this.editor.getEditor().setSelection({
                startLineNumber: selection.startLineNumber,
                startColumn: selection.startColumn - 1,
                endLineNumber: selection.endLineNumber,
                endColumn: selection.endColumn + 1,
            });
            this.editor.unwrap('*', '*');
            this.editor.wrap('**', '**');
            this.editor.focus();
            return;
        } else if (this.isCurrentSelectionBold()) {
            const selection = this.editor.getSelection();

            this.editor.getEditor().setSelection({
                startLineNumber: selection.startLineNumber,
                startColumn: selection.startColumn - 2,
                endLineNumber: selection.endLineNumber,
                endColumn: selection.endColumn + 2,
            });
            this.editor.unwrap('**', '**');
            this.editor.focus();
            return;
        }

        this.editor.wrap('**', '**');
        this.editor.focus();
    }
}
