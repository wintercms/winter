# Monaco Code Editor for Winter CMS

This is the Monaco Editor integration for Winter CMS Backend, replacing the legacy Ace Editor with Microsoft's Monaco Editor (the same editor that powers VS Code).

## Overview

**Monaco Editor** provides a rich, modern code editing experience with:
- IntelliSense (code completion)
- Syntax highlighting for 15+ languages
- Advanced find/replace with regex support
- Multi-cursor editing
- Code folding
- Bracket matching and colorization
- Minimap overview
- Color picker for CSS colors
- And many more VS Code features

## Features

### Supported Languages (15)

1. **TypeScript** - Full TypeScript support with type checking
2. **JavaScript** - Modern ES6+ support
3. **CSS** - Including CSS3 properties
4. **JSON** - With schema validation
5. **HTML** - HTML5 support
6. **INI** - Configuration files
7. **LESS** - CSS preprocessor
8. **Markdown** - Rich markdown editing
9. **MySQL** - SQL syntax highlighting
10. **PHP** - Full PHP support
11. **SCSS** - Sass CSS preprocessor
12. **Twig** - Template engine syntax
13. **XML** - Markup language support
14. **YAML** - Configuration file support

### Monaco Features (20+)

Enabled features include:
- Anchor select
- Bracket matching
- Caret operations
- Clipboard operations
- Code lens
- Color picker
- Comment toggling
- Context menu
- Cursor undo/redo
- Find and replace
- Code folding
- Go to symbol
- Hover information
- In-place replace
- Indentation
- Inline hints
- Links
- Multi-cursor editing
- Parameter hints
- Rename symbol
- Smart select
- Snippets
- Suggest (autocomplete)
- Word highlighter
- Word operations

### Themes (35+)

Includes legacy tmTheme themes plus modern JSON themes

### User Preferences

All editor preferences are configurable from **Backend → Preferences → Code editor**:

**Appearance:**
- Font size (default: 12px)
- Theme selection
- Show/hide line numbers (gutter)
- Show/hide invisibles (whitespace)
- Highlight active line
- Show minimap
- Bracket colorization
- Color picker for CSS

**Behavior:**
- Tab size (default: 4 spaces)
- Use soft tabs (spaces) vs hard tabs
- Word wrap
- Auto-closing brackets/quotes
- Code folding
- Indent guides
- Print margin

All preferences persist across sessions and are stored per-user.

## Editor Architecture

Winter CMS uses a **dual-editor architecture** to optimize for different use cases:

### Monaco Editor (this FormWidget)
**Used by:** CodeEditor FormWidget
**Location:** `/modules/backend/formwidgets/codeeditor/`
**Purpose:** Advanced code editing with IntelliSense, syntax highlighting, and modern IDE features
**Bundle Size:** ~15 MB gzipped (main bundle + workers)
**Best for:** Writing PHP, JavaScript, CSS, YAML, and other code files

### Ace Editor (preserved)
**Used by:** RichEditor and MarkdownEditor FormWidgets
**Location:** `/modules/backend/assets/vendor/ace-codeeditor/`
**Purpose:** HTML source code editing within WYSIWYG editors
**Bundle Size:** ~500 KB (significantly lighter)
**Best for:** Viewing/editing raw HTML in rich text contexts

### Why Both?

**Monaco for CodeEditor:**
- Full IntelliSense and code completion
- Advanced refactoring tools
- Multi-cursor editing
- Rich language support
- Worth the bundle size for dedicated code editing

**Ace for RichEditor/MarkdownEditor:**
- Users rarely need advanced IDE features for HTML source view
- Lighter bundle improves page load performance
- Sufficient for basic HTML editing needs
- Reduces total application bundle by keeping WYSIWYG tools lean

This architecture balances modern features where they matter most (code editing) with performance optimization for general-purpose rich text editing.

## Technical Details

### Architecture

```text
modules/backend/formwidgets/codeeditor/
├── assets/
│   ├── css/
│   │   └── codeeditor.css - Compiled styles
│   ├── fonts/
│   │   └── codicon.ttf - Monaco icons font
│   ├── js/
│   │   ├── codeeditor.js - Main Monaco integration
│   │   └── build/
│   │       ├── codeeditor.bundle.js - Main bundle (19 MB)
│   │       ├── css.worker.js - CSS language worker
│   │       ├── editor.worker.js - Base editor worker
│   │       ├── html.worker.js - HTML language worker
│   │       ├── json.worker.js - JSON language worker
│   │       ├── ts.worker.js - TypeScript worker
│   │       └── [language-chunks] - 15 language modules
│   ├── less/
│   │   └── codeeditor.less - Source styles
│   ├── themes/
│   │   ├── [34 .tmTheme files] - Legacy TextMate themes
│   │   ├── one-dark-pro.json - Modern JSON theme
│   │   └── winter.json - Modern JSON theme
│   ├── winter.mix.js - Laravel Mix build configuration
│   └── package.json - NPM dependencies (in parent)
├── partials/
│   └── codeeditor.htm - Widget template
└── CodeEditor.php - FormWidget class
```

### Build System

**Current:** Laravel Mix 6 with Webpack 5

#### Build Command
```bash
php artisan mix:compile --package=module-backend.formwidgets.codeeditor -p
```

#### Build Configuration

See `assets/winter.mix.js`:
- Uses `monaco-editor-webpack-plugin` for proper worker splitting
- Polyfills for browser compatibility (> 0.5%, last 2 versions, Firefox ESR)
- Removes inline codicon font CSS (post-build hook)
- Minification and terser optimization

### Web Workers

Monaco Editor uses Web Workers for language services:

| Worker | Size | Purpose |
|--------|------|---------|
| editor.worker.js | 1.6 MB | Base editor operations |
| ts.worker.js | 22 MB | TypeScript/JavaScript IntelliSense |
| css.worker.js | 4.7 MB | CSS validation and completion |
| html.worker.js | 3.3 MB | HTML validation |
| json.worker.js | 2.2 MB | JSON schema validation |

Workers are loaded asynchronously and run in separate threads for better performance.

### Theme System

Themes are loaded directly as static assets via HTTP fetch (no PHP handler required). Theme preference values include the file extension (e.g., `twilight.tmTheme`, `one-dark-pro.json`).

#### Supported Formats

**1. TextMate Themes (.tmTheme)**
Legacy XML-based themes. Converted to Monaco format at runtime using `fast-plist` library.

**2. JSON Themes (.json)**
Modern VS Code theme format. Parsed and mapped to Monaco's theme structure.

```javascript
// codeeditor.js - Themes loaded via static fetch
async fetchTheme(themeName) {
    // Theme name includes extension (e.g., "twilight.tmTheme", "one-dark-pro.json")
    // Legacy values without extension default to .tmTheme
    const basePath = window.Snowboard.url().asset('/modules/backend/formwidgets/codeeditor/assets/themes/');
    const response = await fetch(`${basePath}${themeName}`);
    // Format determined from file extension
}
```

## Usage

### Basic Usage

```yaml
# fields.yaml
code:
    type: codeeditor
    size: giant
    language: php
```

### Available Options

```yaml
code:
    type: codeeditor
    # Editor size
    size: tiny|small|large|huge|giant  # Default: large

    # Programming language
    language: php|javascript|css|html|twig|yaml|etc  # Default: php

    # Theme (overrides user preference)
    theme: twilight|monokai|github|one-dark-pro|etc

    # Line numbers
    showGutter: true|false  # Default: true

    # Word wrapping
    wordWrap: true|false  # Default: true

    # Code folding
    codeFolding: true|false  # Default: true

    # Auto-closing brackets
    autoClosing: true|false  # Default: true

    # Soft tabs (spaces)
    useSoftTabs: true|false  # Default: true
    tabSize: 2|4|8  # Default: 4

    # Font size (px)
    fontSize: 10|12|14|16|18  # Default: 12

    # Read-only mode
    readOnly: true|false  # Default: false
    disabled: true|false  # Sets readOnly

    # Display options
    showInvisibles: true|false  # Default: false
    highlightActiveLine: true|false  # Default: true
    displayIndentGuides: true|false  # Default: true
    showPrintMargin: true|false  # Default: false
    showMinimap: true|false  # Default: true
    bracketColors: true|false  # Default: false
    showColors: true|false  # Default: true (CSS color picker)
```

### JavaScript API

```javascript
// Get editor instance
const $editor = $('#my-editor');
const wrapper = $editor.data('oc.codeeditor');

// Access Monaco instance directly
const monacoEditor = wrapper.editor;

// Get/set content (via wrapper)
const code = wrapper.getValue();
wrapper.setValue('function test() {}');

// Get/set language
wrapper.setLanguage('javascript');

// Change theme
wrapper.setTheme('one-dark-pro');

// Insert at cursor
wrapper.insert('code here');

// Get cursor position
const position = wrapper.getPosition();  // { lineNumber: 1, column: 1 }

// Fullscreen
wrapper.enterFullscreen();
wrapper.exitFullscreen();
```

### Migrating from ACE to Monaco API

Winter CMS has migrated from ACE Editor to Monaco Editor. While backward compatibility is maintained for accessing the editor instance via jQuery `.data('oc.codeEditor')`, direct ACE API calls need to be updated.

#### Breaking Changes

**ACE's Session API is Removed:**
- `editor.getSession()` → No longer available
- ACE used a separate "session" object for document operations
- Monaco combines session and model into a single API

**Position Indexing Changed:**
- ACE uses **0-indexed** positions (rows and columns start at 0)
- Monaco uses **1-indexed** positions (lines and columns start at 1)
- Example: ACE row 5 = Monaco line 6, ACE column 0 = Monaco column 1

**Annotations Replaced with Markers:**
- ACE's `setAnnotations()` → Monaco's `monaco.editor.setModelMarkers()`
- Different data structure and API

#### Quick Migration Guide

**Getting/Setting Editor Value:**

```javascript
// ❌ OLD (ACE API - Deprecated)
const editor = $('[data-control=codeeditor]').data('oc.codeEditor').editor;
const value = editor.getSession().getValue();
editor.getSession().setValue('new value');

// ✅ NEW (Recommended - Use Wrapper)
const wrapper = $('[data-control=codeeditor]').data('oc.codeEditor');
const value = wrapper.getValue();
wrapper.setValue('new value');

// ✅ ALTERNATIVE (Direct Monaco API)
const monacoEditor = wrapper.editor;
const value = monacoEditor.getModel().getValue();
monacoEditor.getModel().setValue('new value');
```

**Inserting Text at Cursor:**

```javascript
// ❌ OLD (ACE API)
editor.insert('text');

// ✅ NEW (Wrapper provides this method)
wrapper.insert('text');
```

**Working with Annotations/Markers:**

```javascript
// ❌ OLD (ACE Annotations)
editor.getSession().setAnnotations([
    { row: 5, column: 0, text: 'Warning message', type: 'warning' }
]);

// Clear annotations
editor.getSession().setAnnotations([]);

// ✅ NEW (Monaco Wrapper Method - Recommended)
wrapper.setMarkers('sourceId', [
    {
        startLineNumber: 6,        // ACE row 5 = Monaco line 6 (1-indexed!)
        startColumn: 1,            // ACE column 0 = Monaco column 1
        endLineNumber: 6,
        endColumn: Number.MAX_VALUE,  // End of line
        message: 'Warning message',
        severity: wrapper.monaco.MarkerSeverity.Warning  // Info, Warning, or Error
    }
]);

// Clear markers
wrapper.setMarkers('sourceId', []);
```

**Getting Cursor Position:**

```javascript
// ❌ OLD (ACE API)
const cursor = editor.getCursorPosition();  // { row: 5, column: 10 } (0-indexed)

// ✅ NEW (Wrapper)
const position = wrapper.getPosition();  // { lineNumber: 6, column: 11 } (1-indexed)

// ✅ ALTERNATIVE (Direct Monaco)
const position = wrapper.editor.getPosition();
```

**Getting Selection:**

```javascript
// ❌ OLD (ACE API)
const range = editor.getSelection().getRange();

// ✅ NEW (Wrapper)
const selection = wrapper.getSelection();

// ✅ ALTERNATIVE (Direct Monaco)
const selection = wrapper.editor.getSelection();
```

#### API Comparison Table

| Operation | ACE API (Deprecated) | Monaco Wrapper (Recommended) | Direct Monaco API |
|-----------|---------------------|------------------------------|-------------------|
| Get value | `editor.getSession().getValue()` | `wrapper.getValue()` | `editor.getModel().getValue()` |
| Set value | `editor.getSession().setValue(v)` | `wrapper.setValue(v)` | `editor.getModel().setValue(v)` |
| Insert text | `editor.insert(text)` | `wrapper.insert(text)` | Complex - use wrapper |
| Get position | `editor.getCursorPosition()` | `wrapper.getPosition()` | `editor.getPosition()` |
| Get selection | `editor.getSelection()` | `wrapper.getSelection()` | `editor.getSelection()` |
| Set annotations | `editor.getSession().setAnnotations()` | `wrapper.setMarkers(id, markers)` | `monaco.editor.setModelMarkers()` |
| Focus editor | `editor.focus()` | `wrapper.focus()` | `editor.focus()` |
| Set language | N/A | `wrapper.setLanguage(lang)` | Complex - use wrapper |

#### Migration Checklist for Plugin Developers

If your plugin interacts with the CodeEditor widget, follow these steps:

1. **Update Editor Instance Access:**
   - ✅ Keep: `.data('oc.codeEditor')` - Returns the wrapper
   - ⚠️ Avoid: `.data('oc.codeEditor').editor` - Returns raw Monaco (advanced use only)

2. **Replace ACE Session Methods:**
   - ❌ Remove all: `getSession().getValue()` → ✅ Use: `getValue()`
   - ❌ Remove all: `getSession().setValue()` → ✅ Use: `setValue()`

3. **Update Annotations:**
   - ❌ Remove: `getSession().setAnnotations(annotations)`
   - ✅ Add: `wrapper.setMarkers(sourceId, markers)`
   - ⚠️ Remember: Convert 0-indexed row/column to 1-indexed line/column
   - Use `wrapper.monaco.MarkerSeverity` for severity constants

4. **Test Thoroughly:**
   - Verify all editor interactions work
   - Check that cursor operations use correct indexing
   - Ensure markers/warnings display correctly

#### Available Wrapper Methods

The Monaco Snowboard editor wrapper provides these convenience methods:

```javascript
const wrapper = $('[data-control=codeeditor]').data('oc.codeEditor');

// Content
wrapper.getValue()              // Get editor content
wrapper.setValue(value)         // Set editor content
wrapper.insert(text)           // Insert at cursor position

// Position & Selection
wrapper.getPosition()          // Get cursor position (1-indexed)
wrapper.getSelection()         // Get selection range

// Markers (Errors/Warnings/Info with squiggly underlines)
wrapper.setMarkers(sourceId, markers)  // Set error/warning markers in editor
// Example: wrapper.setMarkers('myPlugin', [{ startLineNumber: 5, startColumn: 1,
//   endLineNumber: 5, endColumn: Number.MAX_VALUE, message: 'Warning',
//   severity: wrapper.monaco.MarkerSeverity.Warning }])

// Decorations (Visual highlights WITHOUT error semantics)
wrapper.setDecorations(sourceId, decorations)  // Set visual highlights (no squiggles)
// Example: wrapper.setDecorations('myHighlight', [{ range: new monaco.Range(5, 1, 5, Number.MAX_VALUE),
//   options: { isWholeLine: true, className: 'myHighlightClass',
//   linesDecorationsClassName: 'myGutterClass' } }])

// Configuration
wrapper.setLanguage(lang)      // Change syntax highlighting language
wrapper.setTheme(theme)        // Change color theme
wrapper.focus()                // Focus the editor

// View
wrapper.enterFullscreen()      // Enter fullscreen mode
wrapper.exitFullscreen()       // Exit fullscreen mode
wrapper.refresh()              // Refresh editor (re-create instance)

// Direct Access (Advanced)
wrapper.editor                 // Access Monaco editor instance
wrapper.getEditor()           // Same as wrapper.editor
wrapper.getModel()            // Get Monaco model
wrapper.monaco                // Access Monaco namespace (for constants like MarkerSeverity)
```

#### Example: Winter.Builder Plugin Migration

The Winter.Builder plugin was migrated to use Monaco API. Here's a real example:

**Before (ACE):**
```javascript
Localization.prototype.copyStringsFromDone = function(data) {
    var codeEditor = this.getCodeEditor($masterTabPane);

    // Set value using ACE Session API
    codeEditor.getSession().setValue(responseData.strings);

    // Set annotations using ACE
    var annotations = [];
    for (var i = 0; i < updatedLines.length; i++) {
        annotations.push({
            row: updatedLines[i],        // 0-indexed
            column: 0,
            text: 'New String',
            type: 'warning'
        });
    }
    codeEditor.getSession().setAnnotations(annotations);
}
```

**After (Monaco):**
```javascript
Localization.prototype.copyStringsFromDone = function(data) {
    var wrapper = this.getCodeEditor($masterTabPane);

    // Set value using wrapper method
    wrapper.setValue(responseData.strings);

    // Convert to Monaco decorations (visual highlights without error semantics)
    var decorations = [];
    for (var i = 0; i < updatedLines.length; i++) {
        decorations.push({
            range: new wrapper.monaco.Range(
                updatedLines[i] + 1,  // Convert to 1-indexed!
                1,                    // Start column
                updatedLines[i] + 1,  // End line (same line)
                Number.MAX_VALUE      // End column (end of line)
            ),
            options: {
                isWholeLine: true,
                className: 'builder-new-translation-line',         // Background highlight
                linesDecorationsClassName: 'builder-new-translation-gutter',  // Gutter indicator
                hoverMessage: { value: 'New string or section' }   // Tooltip on hover
            }
        });
    }
    wrapper.setDecorations('builderLocalization', decorations);
}
```

## Testing

### Playwright E2E Tests

Comprehensive test suite with 55+ tests:

```bash
# Run all tests
npm run test:e2e

# Run with UI
npm run test:e2e:ui

# Run specific test file
npx playwright test fullscreen.spec.js

# Debug tests
npm run test:e2e:debug
```

### Test Coverage

- **Fullscreen functionality** (6 tests)
- **Theme loading and switching** (9 tests)
- **Language support** (14 tests for all 15 languages)
- **Monaco features** (14 tests: find, replace, folding, multi-cursor, etc.)
- **Preferences persistence** (12 tests)

See `tests/e2e/README-TESTING.md` for full testing documentation.

## Resources

- **Monaco Editor Documentation:** https://microsoft.github.io/monaco-editor/
- **Monaco Editor GitHub:** https://github.com/microsoft/monaco-editor
- **VS Code Themes:** https://marketplace.visualstudio.com/search?target=VSCode&category=Themes
- **Winter CMS Docs:** https://wintercms.com/docs
- **PR #801:** https://github.com/wintercms/winter/pull/801
- **Issue #431:** https://github.com/wintercms/winter/issues/431
