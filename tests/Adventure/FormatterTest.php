<?php

use App\Adventure\Utils\Formatter;
use PHPUnit\Framework\TestCase;

/**
 * Unit tests for Formatter class.
 */
class FormatterTest extends TestCase
{
    /**
     * Test case for formatRows method with return string.
     */
    public function testFormatRowsAsString(): void
    {
        $data = [
            ['Name', 'Age', 'Country'],
            ['John', '25', 'USA'],
            ['Emily', '30', 'UK'],
            ['Mark', '28', 'Canada'],
        ];

        $expectedResult =
            "Name  Age Country \n" .
            "John  25  USA     \n" .
            "Emily 30  UK      \n" .
            "Mark  28  Canada  "
        ;

        // Test formatting rows as a string
        $resultString = Formatter::formatRows($data);
        $this->assertEquals($expectedResult, $resultString);
    }

    /**
     * Test case for formatRows method with return array.
     */
    public function testFormatRowsAsArray(): void
    {
        $data = [
            ['Name', 'Age', 'Country'],
            ['John', '25', 'USA'],
            ['Emily', '30', 'UK'],
            ['Mark', '28', 'Canada'],
        ];

        $expectedResult = [
            'Name  Age Country ',
            'John  25  USA     ',
            'Emily 30  UK      ',
            'Mark  28  Canada  '
        ];

        // Test formatting rows as an array
        $resultArray = Formatter::formatRows($data, 1, true);
        $this->assertEquals($expectedResult, $resultArray);
    }

    /**
     * Test case for formatSingleRow method.
     */
    public function testFormatSingleRow(): void
    {
        $data = ['Name', 'Age', 'Country'];

        $expectedResult = "Name Age Country ";

        $result = Formatter::formatSingleRow($data);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test case for formatKeyValueColumns.
     */
    public function testFormatKeyValueColumns(): void
    {
        $data = [
            'Name' => 'John',
            'Age' => '25',
            'Country' => 'USA',
        ];

        $expectedResult =
            "Name    John\n" .
            "Age     25\n" .
            "Country USA\n";

        $result = Formatter::formatKeyValueColumns($data);
        $this->assertEquals($expectedResult, $result);
    }

    /**
     * Test case for formatKeyValueColumns with different gap size.
     */
    public function testFormatKeyValueColumnsGapSize(): void
    {
        $data = [
            'Name' => 'John',
            'Age' => '25',
            'Country' => 'USA',
        ];

        $expectedResult =
            "Name       John\n" .
            "Age        25\n" .
            "Country    USA\n";

        $result = Formatter::formatKeyValueColumns($data, 4);
        $this->assertEquals($expectedResult, $result);
    }
}
