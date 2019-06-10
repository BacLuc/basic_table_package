<?php


namespace BasicTablePackage;


use BasicTablePackage\View\TableView\Field;
use BasicTablePackage\View\TableView\Row;
use BasicTablePackage\View\TableView\TableView;
use BasicTablePackage\View\TableView\TableViewConfigurationFactory;
use Doctrine\ORM\EntityManager;

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
        $tableView = TableView::empty();
        if ($result != null) {
            $headers = [];
            $rows = [];
            $first = true;
            foreach ($result as $entity) {
                $values = [];
                foreach ($this->configuration as $name => $fieldFactory) {
                    if ($first) {
                        $headers[] = $name;
                    }
                    /**
                     * @var Field $field
                     */
                    $field = call_user_func($fieldFactory, $entity->{$name});
                    $values[] = $field->getTableView();
                }
                $rows[] = new Row($values);
            }
            $tableView = new TableView($headers, $rows);
        }
        return $tableView;
    }
}