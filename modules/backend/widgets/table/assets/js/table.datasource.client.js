/*
 * Client memory data source for the table control.
 */
+function ($) { "use strict";

    // NAMESPACE CHECK
    // ============================

    if ($.wn.table === undefined)
        throw new Error("The $.wn.table namespace is not defined. Make sure that the table.js script is loaded.");

    if ($.wn.table.datasource === undefined)
        throw new Error("The $.wn.table.datasource namespace is not defined. Make sure that the table.datasource.base.js script is loaded.");

    // CLASS DEFINITION
    // ============================

    var Base = $.wn.table.datasource.base,
        BaseProto = Base.prototype

    var Client = function(tableObj) {
        Base.call(this, tableObj)

        var dataString = tableObj.getElement().getAttribute('data-data')

        if (dataString === null || dataString === undefined)
            throw new Error('The required data-data attribute is not found on the table control element.')

        this.data = JSON.parse(dataString)
    };

    Client.prototype = Object.create(BaseProto)
    Client.prototype.constructor = Client

    Client.prototype.dispose = function() {
        BaseProto.dispose.call(this)
        this.data = null
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
    Client.prototype.getRecords = function(offset = 0, count) {
        return Promise.resolve({
            records: (count > 0) ? this.data.slice(offset, offset + count) : this.data,
            totalCount: this.data.length,
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
    Client.prototype.createRecord = function(recordData, placement, relativeToKey, offset, count) {
        // Prevent the records going over the max limit
        if (this.tableObj.options.maxItems && this.data.length >= this.tableObj.options.maxItems) {
            return Promise.reject(
                new Error('The maximum number of records has been reached.')
            );
        }

        if (placement === 'bottom') {
            // Add record to the bottom of the dataset
            this.data.push(recordData)
        }
        else if (placement == 'above' || placement == 'below') {
            // Add record above or below the passed record key
            var recordIndex = this.getIndexOfKey(relativeToKey)
            if (placement == 'below')
                recordIndex ++

            this.data.splice(recordIndex, 0, recordData)
        }

        return this.getRecords(offset, count);
    }

    /*
     * Updates a record with the specified key with the passed data
     *
     * - key - the record key in the dataset (primary key, etc)
     * - recordData - the record fields.
     *
     * This expects a promise to be returned, but the promise needs no data.
     */
    Client.prototype.updateRecord = function(key, recordData) {
        var recordIndex = this.getIndexOfKey(key)

        if (recordIndex !== -1) {
            recordData[this.tableObj.options.keyColumn] = key
            this.data[recordIndex] = recordData
            return Promise.resolve();
        }

        return Promise.reject(
            new Error('Record with they key '+key+ ' is not found in the data set')
        );
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
    Client.prototype.deleteRecord = function(key, newRecordData, offset, count) {
        // Prevent the records from going under the min limit
        if (this.data.length <= this.tableObj.options.minItems) {
            return Promise.reject(
                new Error('The maximum number of records has been reached.')
            );
        }

        var recordIndex = this.getIndexOfKey(key)

        if (recordIndex !== -1) {
            this.data.splice(recordIndex, 1)
            return this.getRecords(offset, count)
        }

        return Promise.reject(
            new Error('Record with they key '+key+ ' is not found in the data set')
        );
    }

    Client.prototype.getIndexOfKey = function(key) {
        var keyColumn = this.tableObj.options.keyColumn

        return this.data.map(function(record) {
            return record[keyColumn] + ""
        }).indexOf(key + "")
    }

    Client.prototype.getAllData = function() {
        return this.data
    }

    $.wn.table.datasource.client = Client
}(window.jQuery);
