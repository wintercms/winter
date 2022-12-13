import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';

((Snowboard) => {
    class CodeEditor extends Snowboard.PluginBase {
        construct(element) {
            this.element = element;
            __webpack_public_path__ = '/modules/backend/formwidgets/codeeditor/assets/';
            this.createEditor();
        }

        createEditor() {
            monaco.editor.create(this.element, {
                value: '',
                language: 'html',
                theme: 'vs-dark',
            });
        }
    }

    Snowboard.addPlugin('backend.formwidgets.codeeditor', CodeEditor);
    Snowboard['backend.ui.widgetHandler']().register('codeeditor', 'backend.formwidgets.codeeditor');
})(window.Snowboard);
