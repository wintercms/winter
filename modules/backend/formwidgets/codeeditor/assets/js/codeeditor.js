import * as monaco from 'monaco-editor/esm/vs/editor/editor.api';

export default class CodeEditor extends Snowboard.PluginBase {
    construct(element) {
        this.element = element;
        this.monaco = monaco;
    }
};
