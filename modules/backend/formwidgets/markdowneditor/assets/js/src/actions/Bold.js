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
        let selection;
        let word = null;

        if (this.editor.getSelection().isEmpty()) {
            word = this.selectCurrentWord();

            if (word) {
                selection = word.selection;
                this.editor.getEditor().setSelection(word.selection);
            } else {
                selection = this.editor.getSelection();
            }
        } else {
            selection = this.editor.getSelection();
        }

        if (this.isCurrentSelectionItalic()) {
            const selection = this.editor.getSelection();

            if (
                /^\*[^*]/.test(this.editor.getModel().getValueInRange(selection)) === false
                || /[^*]\*$/.test(this.editor.getModel().getValueInRange(selection)) === false
            ) {
                this.editor.getEditor().setSelection({
                    startLineNumber: selection.startLineNumber,
                    startColumn: selection.startColumn - 1,
                    endLineNumber: selection.endLineNumber,
                    endColumn: selection.endColumn + 1,
                });
            }

            this.editor.unwrap('*', '*');
            this.editor.wrap('**', '**');
            this.editor.focus();

            if (word) {
                this.editor.getEditor().setSelection({
                    startLineNumber: word.caret.positionLineNumber,
                    startColumn: word.caret.positionColumn + 2,
                    endLineNumber: word.caret.positionLineNumber,
                    endColumn: word.caret.positionColumn + 2,
                });
            } else {
                this.editor.getEditor().setSelection(selection);
            }
            return;
        }
        if (this.isCurrentSelectionBold()) {
            const selection = this.editor.getSelection();

            if (
                /^\*\*/.test(this.editor.getModel().getValueInRange(selection)) === false
                || /\*\*$/.test(this.editor.getModel().getValueInRange(selection)) === false
            ) {
                this.editor.getEditor().setSelection({
                    startLineNumber: selection.startLineNumber,
                    startColumn: selection.startColumn - 2,
                    endLineNumber: selection.endLineNumber,
                    endColumn: selection.endColumn + 2,
                });
            }

            this.editor.unwrap('**', '**');
            this.editor.focus();

            if (word) {
                this.editor.getEditor().setSelection({
                    startLineNumber: word.caret.positionLineNumber,
                    startColumn: word.caret.positionColumn - 2,
                    endLineNumber: word.caret.positionLineNumber,
                    endColumn: word.caret.positionColumn - 2,
                });
            } else {
                this.editor.getEditor().setSelection({
                    startLineNumber: selection.startLineNumber,
                    startColumn: selection.startColumn - 2,
                    endLineNumber: selection.endLineNumber,
                    endColumn: selection.endColumn - 2,
                });
            }
            return;
        }

        this.editor.wrap('**', '**');
        this.editor.focus();

        if (word) {
            this.editor.getEditor().setSelection({
                startLineNumber: word.caret.positionLineNumber,
                startColumn: word.caret.positionColumn + 2,
                endLineNumber: word.caret.positionLineNumber,
                endColumn: word.caret.positionColumn + 2,
            });
        } else {
            this.editor.getEditor().setSelection({
                startLineNumber: selection.startLineNumber,
                startColumn: selection.startColumn + 2,
                endLineNumber: selection.endLineNumber,
                endColumn: selection.endColumn + 2,
            });
        }
    }
}
