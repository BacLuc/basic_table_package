<?php


namespace BasicTablePackage;


use BasicTablePackage\View\TableView\Field;
use BasicTablePackage\View\TableView\Row;
use BasicTablePackage\View\TableView\TableView;
use BasicTablePackage\View\TableView\TableViewConfigurationFactory;
use Doctrine\ORM\EntityManager;
use Tightenco\Collect\Support\Collection;
use function BasicTablePackage\Lib\collect as collect;

class TableViewService
{
    const HEADER_2 = "header2";
    /**
     * @var EntityManager
     */
    private $entityManager;
    private $tableViewConfigurationFactory;
    private $configuration;

    /**
     * TableViewService constructor.
     */
    public function __construct (EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->tableViewConfigurationFactory = new TableViewConfigurationFactory();
        $this->configuration = $this->tableViewConfigurationFactory->createConfiguration();
    }


    public function getTableView (): TableView
    {
        $query = $this->entityManager->createQuery(
        /** @lang DQL */
            "SELECT exampleEntity FROM BasicTablePackage\Entity\ExampleEntity exampleEntity");

        $result = $query->getResult();

        $headers = collect($this->configuration)->keys()->toArray();
        $tableView = new TableView($headers, []);
        if ($result != null) {
            $rows = collect($result)
                ->map(function ($entity) {
                    return collect($this->configuration)
                        ->map(function ($fieldFactory, $name) use ($entity) {
                            return call_user_func($fieldFactory, $entity->{$name});
                        })->map(function ($field) { return self::toTableView($field); });
                })
                ->map(function ($collection) { return self::asArray($collection); })
                ->map(function ($field) { return new Row($field); });
            $tableView = new TableView($headers, $rows->toArray());
        }
        return $tableView;
    }

    private static function toTableView (Field $field)
    {
        return $field->getTableView();
    }

    private static function asArray (Collection $collection)
    {
        return $collection->toArray();
    }
}