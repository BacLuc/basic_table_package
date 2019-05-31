<?php

namespace BasicTablePackage\View\TableView;

use PHPUnit\Framework\TestCase;

class RowTest extends TestCase
{
    public function test_foreach_works ()
    {
        $values = [ 1, 2, 3 ];
        $row = new Row($values);
        foreach ($row as $value) {
            $key = $row->key();
            $this->assertThat($value, $this->equalTo($values[$key]));
        }
    }
}
