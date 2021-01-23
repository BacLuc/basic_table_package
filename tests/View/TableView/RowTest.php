<?php

namespace BaclucC5Crud\View\TableView;

use function BaclucC5Crud\Lib\collect as collect;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class RowTest extends TestCase {
    public function testForeachWorks() {
        $values = [1, 2, 3];
        $row = new Row(1, $values);
        foreach ($row as $value) {
            $key = $row->key();
            $this->assertThat($value, $this->equalTo($values[$key]));
        }
    }

    public function testCollectWorks() {
        $values = [1, 2, 3];
        $row = new Row(1, $values);

        collect($row)->each(function ($value, $key) use ($values) {
            $this->assertThat($value, $this->equalTo($values[$key]));
        });
    }
}
