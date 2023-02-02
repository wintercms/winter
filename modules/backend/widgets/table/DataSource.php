<?php

namespace Backend\Widgets\Table;

/**
 * Table widget data source.
 *
 * A table data source provides the remote listing and management of records for the Table widget.
 *
 * @author Alexey Bobkov, Samuel Georges (original implementation)
 * @author Ben Thomson <git@alfreido.com> (interface implementation)
 * @copyright 2023 Winter CMS
 */
interface DataSource
{
    /**
     * Initializes records in the data source.
     *
     * This is called on widget initialization as a constructor for the datasource. The method doesn't replace existing
     * records and could be called multiple times in order to fill the data source.
     */
    public function initRecords(array $records): void;

    /**
     * Return records from the data source.
     *
     * The results can be paginated using the `$offset` and `$count` parameters. This method will return an array of
     * records to display within the table. If there are no more records, an empty array should be returned.
     *
     * @param int $offset Specifies the offset of the first record to return, zero-based.
     * @param int $count Specifies the number of records to return. If 0, all records should be returned.
     */
    public function getRecords(int $offset = 0, int $count = 0): array;

    /**
     * Returns the total number of records in the data source.
     */
    public function getCount(): int;

    /**
     * Searches records in the data source.
     *
     * This functions similar to the `getRecords` method, except that records should be filtered using the given query.
     *
     * @param string $query The search query to filter records by.
     * @param int $offset Specifies the offset of the first record to return, zero-based.
     * @param int $count Specifies the number of records to return. If 0, all records should be returned.
     */
    public function searchRecords(string $query, int $offset = 0, int $count = 0): array;
}
