<?php

namespace BasicTablePackage\View\TableView;

use PHPUnit\Framework\TestCase;
use function BasicTablePackage\Lib\collect as collect;

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

    public function test_collect_works ()
    {
        $values = [ 1, 2, 3 ];
        $row = new Row($values);

        collect($row)->each(function ($value, $key) use ($values) {
            $this->assertThat($value, $this->equalTo($values[$key]));
        });
    }
}
