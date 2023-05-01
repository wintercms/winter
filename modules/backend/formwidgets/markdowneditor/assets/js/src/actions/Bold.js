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
            if (word && !this.isCurrentWordBold()) {
                this.editor.getEditor().setSelection(word.selection);
            }
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

    selectCurrentWord() {
        const selection = this.editor.getSelection();
        const valueInSelection = this.editor.getModel().getValueInRange(selection);

        // If the selection crosses multiple words or lines, we cannot select a word.
        if (
            selection.startLineNumber !== selection.endLineNumber
            || valueInSelection.includes(' ')
        ) {
            return null;
        }

        const position = this.editor.getPosition();
        const word = this.editor.getModel().getWordAtPosition(position);

        if (!word) {
            return null;
        }

        return {
            word: word.word,
            selection: {
                startLineNumber: position.lineNumber,
                startColumn: word.startColumn,
                endLineNumber: position.lineNumber,
                endColumn: word.endColumn,
            },
        };
    }

    isCurrentWordBold() {
        const word = this.selectCurrentWord();

        if (!word) {
            return false;
        }

        // Check boundaries
        const currentLineLength = this.editor.getModel().getLineLength(word.selection.startLineNumber);

        if (word.selection.startColumn < 3) {
            return false;
        }

        if (word.selection.endColumn > (currentLineLength - 1)) {
            return false;
        }

        // Get expanded value
        const expandedValue = this.editor.getModel().getValueInRange({
            startLineNumber: word.selection.startLineNumber,
            startColumn: word.selection.startColumn - 2,
            endLineNumber: word.selection.endLineNumber,
            endColumn: word.selection.endColumn + 2,
        });

        if (expandedValue.startsWith('**') && expandedValue.endsWith('**')) {
            return true;
        }

        return false;
    }

    isCurrentSelectionBold() {
        const selection = this.editor.getSelection();

        if (selection.startLineNumber !== selection.endLineNumber) {
            return false;
        }

        const currentLineLength = this.editor.getModel().getLineLength(selection.startLineNumber);

        if (selection.startColumn < 3) {
            return false;
        }

        if (selection.endColumn > (currentLineLength - 1)) {
            return false;
        }

        const expandedValue = this.editor.getModel().getValueInRange({
            startLineNumber: selection.startLineNumber,
            startColumn: selection.startColumn - 2,
            endLineNumber: selection.endLineNumber,
            endColumn: selection.endColumn + 2,
        });

        if (expandedValue.startsWith('**') && expandedValue.endsWith('**')) {
            return true;
        }

        return false;
    }
}
