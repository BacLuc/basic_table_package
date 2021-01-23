<?php

namespace BaclucC5Crud\Controller\Validation;

use function BaclucC5Crud\Lib\collect as collect;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class DateValidatorTest extends TestCase {
    /**
     * @dataProvider getFormats
     *
     * @param mixed $date
     * @param mixed $isValid
     */
    public function testDateFormats($date, $isValid) {
        $fieldName = 'test';
        $dateValidator = new DateValidator($fieldName);
        $postvalues[$fieldName] = $date;
        self::assertThat($dateValidator->validate($postvalues)->isError(), $isValid ? self::isFalse() : self::isTrue());
    }

    public function getFormats() {
        return $this->describeDataSet([
            ['1980-05-31', true],
            ['1980/05/31', true],
            ['1980.05.31', true],
            ['2030.05.31', false],
            ['31-05-1980', true],
            ['31.05.1980', true],
            ['31/05/1980', false],
            ['05/31/1980', true],
            ['05.31.1980', false],
            ['05-31-1980', false],

            ['05.31.1980 14:34', false],
            ['05.31.1980 23:59', false],
            ['05.31.1980 00:00', false],
            ['05.31.1980 25:00', false],

            ['05.31.1980T14:34', false],
            ['05.31.1980T23:59', false],
            ['05.31.1980T00:00', false],
            ['05.31.1980T25:00', false],

            ['80-05-31', true],
            ['80/05/31', false],
            ['80.05.31', false],
            ['31-05-80', false],
            ['31.05.80', true],
            ['31/05/80', false],
            ['05/31/80', true],
            ['05.31.80', false],
            ['05-31-80', false],

            ['bla', false],
            [0, false],
            [-1, false],
            [1.2, false],
            [null, true],
            ['', true],
        ]);
    }

    /**
     * @dataProvider getRanges
     *
     * @param mixed $date
     * @param mixed $isValid
     */
    public function testDateRanges($date, $isValid) {
        $fieldName = 'test';
        $dateValidator = new DateValidator($fieldName);
        $postvalues[$fieldName] = $date;
        self::assertThat($dateValidator->validate($postvalues)->isError(), $isValid ? self::isFalse() : self::isTrue());
    }

    public function getRanges() {
        return $this->describeDataSet([
            ['01.01.1900', true],
            ['31.12.2050', true],
            ['32.12.2050', false],
            ['31.13.2050', false],
            ['31.11.2050', true],
            ['31.02.2050', true],
        ]);
    }

    private function describeDataSet(array $data) {
        return collect($data)->keyBy(function ($dataRow) {
            return '['.collect($dataRow)->join(', ').']';
        });
    }
}
