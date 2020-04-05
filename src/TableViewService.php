<?php


namespace BaclucC5Crud;


use BaclucC5Crud\Entity\TableViewEntrySupplier;
use BaclucC5Crud\View\TableView\Field;
use BaclucC5Crud\View\TableView\Row;
use BaclucC5Crud\View\TableView\TableView;
use BaclucC5Crud\View\TableView\TableViewFieldConfiguration;
use Tightenco\Collect\Support\Collection;
use function BaclucC5Crud\Lib\collect as collect;

class TableViewService
{

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


    public function getTableView(): TableView
    {
        $result = $this->tableViewEntrySupplier->getEntries();
        $headers = collect($this->tableViewFieldConfiguration)
            ->map(function ($fieldFactory, $name) {
                return call_user_func($fieldFactory, null, $name);
            })
            ->filter(function ($value) {
                return $value !== null;
            })
            ->keys()
            ->toArray();
        $tableView = new TableView($headers, []);
        if ($result != null) {
            $rows = collect($result)
                ->keyBy(function ($entity) {
                    return $entity->id;
                })
                ->map(function ($entity) {
                    return collect($this->tableViewFieldConfiguration)
                        ->map(function ($fieldFactory, $name) use ($entity) {
                            return call_user_func($fieldFactory, $entity->{$name}, $name);
                        })
                        ->filter(function ($value) {
                            return $value !== null;
                        })
                        ->map(function ($field) {
                            return self::toTableView($field);
                        });
                })
                ->map(function ($collection) {
                    return self::asArray($collection);
                })
                ->map(function ($fields, $id) {
                    return new Row($id, $fields);
                });
            $tableView = new TableView($headers, $rows->toArray());
        }
        return $tableView;
    }

    private static function toTableView(Field $field)
    {
        return $field->getTableView();
    }

    private static function asArray(Collection $collection)
    {
        return $collection->toArray();
    }
}