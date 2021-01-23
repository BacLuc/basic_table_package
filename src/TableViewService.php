<?php

namespace BaclucC5Crud;

use BaclucC5Crud\Controller\PaginationConfiguration;
use BaclucC5Crud\Entity\Identifiable;
use BaclucC5Crud\Entity\TableViewEntrySupplier;
use function BaclucC5Crud\Lib\collect as collect;
use BaclucC5Crud\View\TableView\Field;
use BaclucC5Crud\View\TableView\Row;
use BaclucC5Crud\View\TableView\TableView;
use BaclucC5Crud\View\TableView\TableViewFieldConfiguration;
use Tightenco\Collect\Support\Collection;

class TableViewService {
    /**
     * @var TableViewFieldConfiguration
     */
    private $tableViewFieldConfiguration;
    /**
     * @var TableViewEntrySupplier
     */
    private $tableViewEntrySupplier;

    public function __construct(
        TableViewEntrySupplier $tableViewEntrySupplier,
        TableViewFieldConfiguration $tableViewFieldConfiguration
    ) {
        $this->tableViewEntrySupplier = $tableViewEntrySupplier;
        $this->tableViewFieldConfiguration = $tableViewFieldConfiguration;
    }

    public function getTableView(PaginationConfiguration $paginationConfiguration): TableView {
        $result = $this->tableViewEntrySupplier->getEntries($paginationConfiguration);
        $headers = collect($this->tableViewFieldConfiguration)
            ->map(function ($fieldFactory, $name) {
                return call_user_func($fieldFactory, null, $name);
            })
            ->filter(function ($value) {
                return null !== $value;
            })
            ->keys()
            ->toArray()
        ;
        $tableView = new TableView($headers, [], 0);
        if (null != $result) {
            $rows = collect($result)
                ->keyBy(function (Identifiable $entity) {
                    return $entity->getId();
                })
                ->map(function ($entity) {
                    return collect($this->tableViewFieldConfiguration)
                        ->map(function ($fieldFactory, $name) use ($entity) {
                            return call_user_func($fieldFactory, $entity->{$name}, $name);
                        })
                        ->filter(function ($value) {
                            return null !== $value;
                        })
                        ->map(function ($field) {
                            return self::toTableView($field);
                        })
                    ;
                })
                ->map(function ($collection) {
                    return self::asArray($collection);
                })
                ->map(function ($fields, $id) {
                    return new Row($id, $fields);
                })
            ;
            $tableView = new TableView($headers, $rows->toArray(), $this->tableViewEntrySupplier->count());
        }

        return $tableView;
    }

    private static function toTableView(Field $field) {
        return $field->getTableView();
    }

    private static function asArray(Collection $collection) {
        return $collection->toArray();
    }
}
