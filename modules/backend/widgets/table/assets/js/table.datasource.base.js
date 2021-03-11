/*
 * Base class for the table data sources.
 */
+function ($) { "use strict";

    // DATASOURCE NAMESPACES
    // ============================

    if ($.wn.table === undefined)
        throw new Error("The $.wn.table namespace is not defined. Make sure that the table.js script is loaded.");

    if ($.wn.table.datasource === undefined)
        $.wn.table.datasource = {}

    // CLASS DEFINITION
    // ============================

    var Base = function(tableObj) {
        //
        // State properties
        //

        this.tableObj = tableObj
    }

    Base.prototype.dispose = function() {
        this.tableObj = null
    }

    /*
     * Fetches records from the underlying data source and
     * passes them to the onSuccess callback function.
     * The onSuccess callback parameters: records, totalCount.
     * Each record contains the key field which uniquely identifies
     * the record. The name of the key field is defined with the table
     * widget options.
     */
    Base.prototype.getRecords = function(offset, count, onSuccess) {
        onSuccess([])
    }

    /*
     * Identical to getRecords except using a search query.
     */
    Base.prototype.searchRecords = function(query, offset, count, onSuccess) {
        onSuccess([])
    }

    /*
     * Creates a record with the passed data and returns the updated page records
     * to the onSuccess callback function.
     *
     * - recordData - the record fields
     * - placement - "bottom" (the end of the data set), "above", "below"
     * - relativeToKey - a row key, required if the placement is not "bottom"
     * - offset - the current page's first record index (zero-based)
     * - count - number of records to return
     * - onSuccess - a callback function to execute when the updated data gets available.
     *
     * The onSuccess callback parameters: records, totalCount.
     */
    Base.prototype.createRecord = function(recordData, placement, relativeToKey, offset, count, onSuccess) {
        onSuccess([], 0)
    }

    /*
     * Updates a record with the specified key with the passed data
     *
     * - key - the record key in the dataset (primary key, etc)
     * - recordData - the record fields.
     */
    Base.prototype.updateRecord = function(key, recordData) {
    }

    /*
     * Deletes a record with the specified key.
     *
     * - key - the record key in the dataset (primary key, etc).
     * - newRecordData - replacement record to add to the dataset if the deletion
     *   empties it.
     * - offset - the current page's first record key (zero-based)
     * - count - number of records to return
     * - onSuccess - a callback function to execute when the updated data gets available.
     *
     * The onSuccess callback parameters: records, totalCount.
     */
    Base.prototype.deleteRecord = function(key, newRecordData, offset, count, onSuccess) {
        onSuccess([], 0)
    }

    $.wn.table.datasource.base = Base;
}(window.jQuery);