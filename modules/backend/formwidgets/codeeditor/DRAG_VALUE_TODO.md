# Drag Value CodeEditor Integration - TODO

This file tracks the necessary updates to `drag.value.js` for Monaco Editor compatibility. These changes are tracked separately from the main Builder plugin updates and will be handled in a follow-up task.

## File Location

`/Users/luketowers/Repositories/WinterCMS/Core/winter/modules/system/assets/ui/js/drag.value.js`

## Changes Required

### Issue: ACE-Specific Class Check

**Line 95-96:**
```javascript
if ($el.hasClass('ace_text-input'))
    return this.handleClickCodeEditor(event, $el)
```

**Problem:**
- Checks for `ace_text-input` class which is ACE-specific
- Monaco uses different class names for its input elements
- This prevents the drag-value feature from working with Monaco editors

**Solution:**
Update the class check to work with Monaco's text input class.

**Monaco's Text Input Class:**
- Monaco uses: `monaco-editor` on the main container
- Text input element: `inputarea monaco-mouse-cursor-text`
- Alternative: Check for `data-control="codeeditor"` parent instead

**Recommended Fix:**
```javascript
// Option 1: Check for Monaco's input area class
if ($el.hasClass('inputarea') && $el.closest('[data-control=codeeditor]').length)
    return this.handleClickCodeEditor(event, $el)

// Option 2: Check for CodeEditor container directly
if ($el.closest('[data-control=codeeditor]').length)
    return this.handleClickCodeEditor(event, $el)
```

### Method: handleClickCodeEditor

**Lines 104-109:**
```javascript
DragValue.prototype.handleClickCodeEditor = function(event, $el) {
    var $editorArea = $el.closest('[data-control=codeeditor]')
    if (!$editorArea.length) return

    $editorArea.codeEditor('getEditorObject').insert(this.textValue)
}
```

**Status:** ✅ **Already Compatible**
- Uses `.codeEditor('getEditorObject').insert()`
- Monaco wrapper provides `.insert()` method (line 465-473 in codeeditor.js)
- No changes needed

## Testing Required

After implementing the fix:

1. Test dragging a value onto a CodeEditor field
2. Verify the value is inserted at the cursor position
3. Test with multiple CodeEditor instances on the same page
4. Test in both standalone CodeEditor and within Builder plugin

## Priority

**Low Priority** - This is a nice-to-have feature, not critical functionality.

## Related Files

- Main CodeEditor widget: `/modules/backend/formwidgets/codeeditor/assets/js/codeeditor.js`
- Monaco wrapper `.insert()` method: Lines 465-473
- Drag value handler: `/modules/system/assets/ui/js/drag.value.js`

## Notes

- The `.insert()` method is already provided by the Monaco wrapper
- Only the class detection logic needs updating
- Consider whether this feature is widely used before prioritizing
