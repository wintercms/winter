/*
 * Base class for the table data sources.
 *
 * As of Winter 1.2.1, all methods that are provided by the datasource should return promises, in
 * order to provide a unified expectation between client and server datasources.
 *
 * @author Alexey Bobkov, Samuel Georges
 * @author Winter CMS
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
     * Fetches records from the underlying data source and returns a Promise object
     * containing two keys: `records` and `totalCount`, representing an array of records
     * and the total number of records in the data source, respectively.
     *
     * Each record contains the key field which uniquely identifies
     * the record. The name of the key field is defined with the table
     * widget options.
     */
    Base.prototype.getRecords = function(offset, count) {
        return Promise.resolve({
            records: [],
            totalCount: 0,
        });
    }

    /*
     * Identical to getRecords except using a search query.
     */
    Base.prototype.searchRecords = function(query, offset, count) {
        return Promise.resolve({
            records: [],
            totalCount: 0,
        });
    }

    /*
     * Creates a record with the passed data and returns a Promise object
     * containing two keys: `records` and `totalCount`, representing an array of all records,
     * including the newly created one, and the total number of records in the data source,
     * respectively.
     *
     * Parameters provided to this method:
     *
     * - recordData - the record fields
     * - placement - "bottom" (the end of the data set), "above", "below"
     * - relativeToKey - a row key, required if the placement is not "bottom"
     * - offset - the current page's first record index (zero-based)
     * - count - number of records to return
     */
    Base.prototype.createRecord = function(recordData, placement, relativeToKey, offset, count) {
        return Promise.resolve({
            records: [],
            totalCount: 0,
        });
    }

    /*
     * Updates a record with the specified key with the passed data
     *
     * - key - the record key in the dataset (primary key, etc)
     * - recordData - the record fields.
     *
     * This expects a promise to be returned, but the promise needs no data.
     */
    Base.prototype.updateRecord = function(key, recordData) {
        return Promise.resolve();
    }

    /*
     * Deletes a record with the specified key and returns a Promise object
     * containing two keys: `records` and `totalCount`, representing an array of all records,
     * excluding the deleted record, and the total number of records in the data source,
     * respectively.
     *
     * - key - the record key in the dataset (primary key, etc).
     * - newRecordData - replacement record to add to the dataset if the deletion
     *   empties it.
     * - offset - the current page's first record key (zero-based)
     * - count - number of records to return
     */
    Base.prototype.deleteRecord = function(key, newRecordData, offset, count) {
        return Promise.resolve({
            records: [],
            totalCount: 0,
        });
    }

    $.wn.table.datasource.base = Base;
}(window.jQuery);
