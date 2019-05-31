<?php


namespace BasicTablePackage;


use BasicTablePackage\View\TableView\Row;
use BasicTablePackage\View\TableView\TableView;

class TableViewService
{
    const HEADER_2 = "header2";
    public function getTableView() : TableView {
        $row1 = new Row([ "test1", "test2" ]);
        $row2 = new Row([ "test3", "test4" ]);
        return new TableView([ "header1", "header1" ], [ $row1, $row2]);
    }
}