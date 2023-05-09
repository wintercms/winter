import EditorAction from '../EditorAction';

export default class Italic extends EditorAction {
    icon() {
        return 'italic';
    }

    tooltip() {
        return 'Italic';
    }

    shortcutKey() {
        return 'I';
    }

    isActive() {
        if (!this.editor || !this.editor.getModel()) {
            return false;
        }

        return this.isCurrentWordItalic() || this.isCurrentSelectionItalic();
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
                this.editor.wrap('*', '*');
                this.editor.focus();
                return;
            }
            if (word) {
                this.editor.getEditor().setSelection(word.selection);
            }
        } else if (this.isCurrentSelectionItalic()) {
            const selection = this.editor.getSafeSelection();

            if (
                /^\*[^*]/.test(this.editor.getModel().getValueInRange(selection))
                && /[^*]\*$/.test(this.editor.getModel().getValueInRange(selection))
            ) {
                this.editor.getEditor().setSelection({
                    startLineNumber: selection.startLineNumber,
                    startColumn: selection.startColumn + 1,
                    endLineNumber: selection.endLineNumber,
                    endColumn: selection.endColumn - 1,
                });
            } else {
                this.editor.getEditor().setSelection({
                    startLineNumber: selection.startLineNumber,
                    startColumn: selection.startColumn - 1,
                    endLineNumber: selection.endLineNumber,
                    endColumn: selection.endColumn + 1,
                });
            }

            this.editor.unwrap('*', '*');
            this.editor.focus();
            return;
        } else if (this.isCurrentSelectionBold()) {
            const selection = this.editor.getSafeSelection();

            if (
                /^\*\*/.test(this.editor.getModel().getValueInRange(selection))
                && /\*\*$/.test(this.editor.getModel().getValueInRange(selection))
            ) {
                this.editor.getEditor().setSelection({
                    startLineNumber: selection.startLineNumber,
                    startColumn: selection.startColumn + 2,
                    endLineNumber: selection.endLineNumber,
                    endColumn: selection.endColumn - 2,
                });
            } else {
                this.editor.getEditor().setSelection({
                    startLineNumber: selection.startLineNumber,
                    startColumn: selection.startColumn - 2,
                    endLineNumber: selection.endLineNumber,
                    endColumn: selection.endColumn + 2,
                });
            }

            this.editor.unwrap('**', '**');
            this.editor.wrap('*', '*');
            this.editor.focus();
            return;
        }

        this.editor.wrap('*', '*');
        this.editor.focus();
    }

    selectCurrentWord() {
        const selection = this.editor.getSelection().toJSON();
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
        const expandedValue = this.getExpandedWord();

        if (expandedValue === false) {
            return false;
        }

        return (expandedValue.startsWith('**') && expandedValue.endsWith('**'));
    }

    isCurrentWordItalic() {
        const expandedValue = this.getExpandedWord();

        if (expandedValue === false) {
            return false;
        }

        console.log(expandedValue);

        return (/^[^*]?\*/.test(expandedValue) && /\*[^*]?$/.test(expandedValue));
    }

    isCurrentSelectionBold() {
        const selection = this.editor.getSelection().toJSON();

        if (
            /^\*\*/.test(this.editor.getModel().getValueInRange(selection))
            && /\*\*$/.test(this.editor.getModel().getValueInRange(selection))
        ) {
            return true;
        }

        const expandedValue = this.getExpandedSelection();

        if (expandedValue === false) {
            return false;
        }

        return (expandedValue.startsWith('**') && expandedValue.endsWith('**'));
    }

    isCurrentSelectionItalic() {
        const selection = this.editor.getSelection().toJSON();

        if (
            /^\*[^*]/.test(this.editor.getModel().getValueInRange(selection))
            && /[^*]\*$/.test(this.editor.getModel().getValueInRange(selection))
        ) {
            return true;
        }

        const expandedValue = this.getExpandedSelection();

        if (expandedValue === false) {
            return false;
        }

        return (/^[^*]\*/.test(expandedValue) && /\*[^*]$/.test(expandedValue));
    }

    getExpandedWord() {
        const word = this.selectCurrentWord();

        if (!word) {
            return false;
        }

        // Check boundaries
        const currentLineLength = this.editor.getModel().getLineLength(word.selection.startLineNumber);

        if (word.selection.startColumn < 3) {
            word.selection.startColumn = 1;
        } else {
            word.selection.startColumn -= 2;
        }

        if (word.selection.endColumn > (currentLineLength - 1)) {
            word.selection.endColumn = currentLineLength + 1;
        } else {
            word.selection.endColumn += 2;
        }

        // Get expanded value
        return this.editor.getModel().getValueInRange({
            startLineNumber: word.selection.startLineNumber,
            startColumn: word.selection.startColumn,
            endLineNumber: word.selection.endLineNumber,
            endColumn: word.selection.endColumn,
        });
    }

    getExpandedSelection() {
        const selection = this.editor.getSafeSelection();

        if (selection.startLineNumber !== selection.endLineNumber) {
            return false;
        }

        const currentLineLength = this.editor.getModel().getLineLength(selection.startLineNumber);

        if (selection.startColumn < 3) {
            selection.startColumn = 1;
        } else {
            selection.startColumn -= 2;
        }

        if (selection.endColumn > (currentLineLength - 1)) {
            selection.endColumn = currentLineLength + 1;
        } else {
            selection.endColumn += 2;
        }

        return this.editor.getModel().getValueInRange({
            startLineNumber: selection.startLineNumber,
            startColumn: selection.startColumn,
            endLineNumber: selection.endLineNumber,
            endColumn: selection.endColumn,
        });
    }
}
