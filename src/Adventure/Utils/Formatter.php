<?php

namespace App\Adventure\Utils;

/**
 * The Formatter class provides methods for formatting data in various ways, such as organizing data into rows,
 * aligning columns, and formatting key-value pairs into columns.
 */
class Formatter
{
    /**
     * Formats a 2D array of data into rows, without displaying keys,
     * with equal spacing between columns.
     *
     * @param  array<mixed, array<mixed, string>> $data    The data to format.
     * @param  int                                $gap     The spacing between columns. Default is 1.
     * @return string The formatted data as a string.
     */
    public static function formatRows(array $data, int $gap = 1): string
    {
        $columns = array_map(null, ...array_values($data));

        $columnWidths = [];
        $counter = 0;

        foreach ($columns as $column) {
            $columnWidths[] = max(array_map('strlen', $column));
        }

        $formattedRows = [];
        foreach ($data as $row) {
            $formattedRow = '';
            $counter = 0;

            foreach ($row as $value) {
                $columnWidth = $columnWidths[$counter] ?? 0;
                $formattedRow .= str_pad($value, $columnWidth + $gap, ' ', STR_PAD_RIGHT);
                $counter++;
            }

            $formattedRows[] = $formattedRow;
        }

        return implode("\n", $formattedRows);
    }

    /**
     * Formats the values of a 1D array into a single row of data, with equal spacing between columns.
     *
     * @param  string[] $data The data to format.
     * @param  int      $gap The spacing between columns. Default is 1.
     * @return string The formatted row as a string.
     */
    public static function formatSingleRow(array $data, int $gap = 1): string
    {
        $formattedRow = '';
        foreach ($data as $value) {
            $formattedRow .= str_pad($value, strlen($value) + $gap, ' ', STR_PAD_RIGHT);
        }

        return $formattedRow;
    }

    /**
     * Formats a 1D associative array of data into columns,
     * with keys and values displayed in separate columns.
     *
     * @param  array<string, string> $data The data to format.
     * @param  int                   $gap The spacing between the key and value columns. Default is 1.
     * @return string The formatted data as a string.
     */
    public static function formatKeyValueColumns(array $data, int $gap = 1): string
    {
        // Find the maximum length of the keys
        $maxLength = max(array_map('strlen', array_keys($data)));

        // Format each key-value pair
        $formatted = '';
        foreach ($data as $key => $value) {
            $formattedKey = str_pad($key, $maxLength + $gap);
            $formatted .= $formattedKey . $value . "\n";
        }

        return $formatted;
    }
}
