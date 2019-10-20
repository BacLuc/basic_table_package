<?php


namespace BasicTablePackage;


use BasicTablePackage\Entity\Repository;
use BasicTablePackage\View\TableView\Field;
use BasicTablePackage\View\TableView\Row;
use BasicTablePackage\View\TableView\TableView;
use BasicTablePackage\View\TableView\TableViewFieldConfiguration;
use Tightenco\Collect\Support\Collection;
use function BasicTablePackage\Lib\collect as collect;

class TableViewService
{

    /**
     * @var Repository
     */
    private $repository;
    private $tableViewFieldConfiguration;

    /**
     * TableViewService constructor.
     */
    public function __construct(Repository $repository, TableViewFieldConfiguration $tableViewFieldConfiguration)
    {
        $this->repository = $repository;
        $this->tableViewFieldConfiguration = $tableViewFieldConfiguration;
    }


    public function getTableView(): TableView
    {
        $result = $this->repository->getAll();
        $headers = collect($this->tableViewFieldConfiguration)->keys()->toArray();
        $tableView = new TableView($headers, []);
        if ($result != null) {
            $rows = collect($result)
                ->keyBy(function ($entity) {
                    return $entity->id;
                })
                ->map(function ($entity) {
                    return collect($this->tableViewFieldConfiguration)
                        ->map(function ($fieldFactory, $name) use ($entity) {
                            return call_user_func($fieldFactory, $entity->{$name});
                        })->map(function ($field) {
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