/*
 * Server memory data source for the table control.
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

    var Server = function(tableObj) {
        Base.call(this, tableObj)

        var dataString = tableObj.getElement().getAttribute('data-data')

        if (dataString === null || dataString === undefined)
            throw new Error('The required data-data attribute is not found on the table control element.')

        this.data = JSON.parse(dataString)
    };

    Server.prototype = Object.create(BaseProto)
    Server.prototype.constructor = Server

    Server.prototype.dispose = function() {
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
    Server.prototype.getRecords = function(offset, count) {
        return new Promise((resolve) => {
            var handlerName = this.tableObj.getAlias()+'::onServerGetRecords'
            this.tableObj.$el.request(handlerName, {
                data: {
                    offset: offset,
                    count: count
                }
            }).done(function(data) {
                resolve({
                    records: data.records,
                    totalCount: data.count,
                });
            });
        });
    }

    /*
     * Identical to getRecords except using a search query.
     */
    Server.prototype.searchRecords = function(query, offset, count) {
        return new Promise((resolve) => {
            var handlerName = this.tableObj.getAlias()+'::onServerSearchRecords'
            this.tableObj.$el.request(handlerName, {
                data: {
                    query: query,
                    offset: offset,
                    count: count
                }
            }).done(function(data) {
                resolve({
                    records: data.records,
                    totalCount: data.count,
                });
            })
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
    Server.prototype.createRecord = function(recordData, placement, relativeToKey, offset, count) {
        return new Promise((resolve) => {
            var handlerName = this.tableObj.getAlias()+'::onServerCreateRecord';

            this.tableObj.$el.request(handlerName, {
                data: {
                    recordData: recordData,
                    placement: placement,
                    relativeToKey: relativeToKey,
                    offset: offset,
                    count: count
                }
            }).done(function(data) {
                resolve({
                    records: data.records,
                    totalCount: data.count,
                });
            })
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
    Server.prototype.updateRecord = function(key, recordData) {
        return new Promise((resolve) => {
            var handlerName = this.tableObj.getAlias()+'::onServerUpdateRecord'
            this.tableObj.$el.request(handlerName, {
                data: {
                    key: key,
                    recordData: recordData
                }
            }).done(() => {
                resolve();
            });
        });
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
    Server.prototype.deleteRecord = function(key, newRecordData, offset, count) {
        return new Promise((resolve) => {
            var handlerName = this.tableObj.getAlias()+'::onServerDeleteRecord'
            this.tableObj.$el.request(handlerName, {
                data: {
                    key: key,
                    offset: offset,
                    count: count
                }
            }).done(function(data) {
                resolve({
                    records: data.records,
                    totalCount: data.count,
                });
            })
        });
    }

    $.wn.table.datasource.server = Server
}(window.jQuery);
