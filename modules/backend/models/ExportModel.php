<?php namespace Backend\Models;

use File;
use Lang;
use Model;
use Response;
use League\Csv\Writer as CsvWriter;
use League\Csv\EscapeFormula as CsvEscapeFormula;
use ApplicationException;
use SplTempFileObject;

/**
 * Model used for exporting data
 *
 * @package winter\wn-backend-module
 * @author Alexey Bobkov, Samuel Georges
 */
abstract class ExportModel extends Model
{
    use \Backend\Traits\ExportFileStorage;

    /**
     * Called when data is being exported.
     * The return value should be an array in the format of:
     *
     *   [
     *       'db_name1' => 'Some attribute value',
     *       'db_name2' => 'Another attribute value'
     *   ],
     *   [...]
     *
     */
    abstract public function exportData($columns, $sessionKey = null);

    /**
     * Export data based on column names and labels.
     * The $columns array should be in the format of:
     *
     *   [
     *       'db_name1' => 'Column label',
     *       'db_name2' => 'Another label',
     *       ...
     *   ]
     *
     */
    public function export($columns, $options)
    {
        $sessionKey = array_get($options, 'sessionKey');
        $data = $this->exportData(array_keys($columns), $sessionKey);
        return $this->processExportData($columns, $data, $options);
    }

    /**
     * Download a previously compiled export file belonging to the active backend user.
     * @return void
     */
    public function download($name, $outputName = null)
    {
        $csvPath = static::findExportFilePath($name);

        return Response::download($csvPath, $outputName, ['Content-Type' => 'text/csv'])->deleteFileAfterSend(true);
    }

    /**
     * Converts a data collection to a CSV file.
     */
    protected function processExportData($columns, $results, $options)
    {
        /*
         * Validate
         */
        if (!$results) {
            throw new ApplicationException(Lang::get('backend::lang.import_export.empty_error'));
        }

        /*
         * Parse options
         */
        $defaultOptions = [
            'firstRowTitles' => true,
            'useOutput' => false,
            'fileName' => 'export.csv',
            'delimiter' => null,
            'enclosure' => null,
            'escape' => null
        ];

        $options = array_merge($defaultOptions, $options);
        $columns = $this->exportExtendColumns($columns);

        /*
         * Prepare CSV
         */
        $csv = CsvWriter::createFromFileObject(new SplTempFileObject);

        $csv->setOutputBOM(CsvWriter::BOM_UTF8);

        if ($options['delimiter'] !== null) {
            $csv->setDelimiter($options['delimiter']);
        }

        if ($options['enclosure'] !== null) {
            $csv->setEnclosure($options['enclosure']);
        }

        if ($options['escape'] !== null) {
            $csv->setEscape($options['escape']);
        }

        $csv->addFormatter(new CsvEscapeFormula());

        /*
         * Add headers
         */
        if ($options['firstRowTitles']) {
            $headers = $this->getColumnHeaders($columns);
            $csv->insertOne($headers);
        }

        /*
         * Add records
         */
        foreach ($results as $result) {
            $data = $this->matchDataToColumns($result, $columns);
            $csv->insertOne($data);
        }

        /*
         * Output
         */
        if ($options['useOutput']) {
            $csv->output($options['fileName']);
        }

        /*
         * Save for download
         */
        $csvName = static::makeExportFileName();
        $csvPath = static::getExportFilePath($csvName);
        $output = $csv->__toString();

        File::put($csvPath, $output);

        return $csvName;
    }

    /**
     * Used to override column definitions at export time.
     */
    protected function exportExtendColumns($columns)
    {
        return $columns;
    }

    /**
     * Extracts the headers from the column definitions.
     */
    protected function getColumnHeaders($columns)
    {
        $headers = [];

        foreach ($columns as $column => $label) {
            $headers[] = Lang::get($label);
        }

        return $headers;
    }

    /**
     * Ensures the correct order of the column data.
     */
    protected function matchDataToColumns($data, $columns)
    {
        $results = [];

        foreach ($columns as $column => $label) {
            $results[] = array_get($data, $column);
        }

        return $results;
    }

    /**
     * Implodes a single dimension array using pipes (|)
     * Multi dimensional arrays are not allowed.
     * @return string
     */
    protected function encodeArrayValue($data, $delimeter = '|')
    {
        $newData = [];
        foreach ($data as $value) {
            if (is_array($value)) {
                $newData[] = 'Array';
            } else {
                $newData[] = str_replace($delimeter, '\\'.$delimeter, $value);
            }
        }

        return implode($delimeter, $newData);
    }
}
