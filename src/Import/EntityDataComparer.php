<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 23.03.17
 * Time: 09:58
 */

namespace Concrete\Package\BasicTablePackage\Src\Import;


use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityFactory;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Port\Reader;

class EntityDataComparer
{
    /**
     * @var BaseEntity
     */
    protected $model;

    /**
     * @var Reader
     */
    protected $reader;

    protected $comparisonData;

    public function __construct(BaseEntity $model, Reader $reader)
    {
        $this->model = $model;
        $this->reader = $reader;
    }


    public function compare()
    {


        //then get all existing data and create a uniquestring -> object map
        $modelList =
            BaseEntityRepository::getBuildQueryWithJoinedAssociations(get_class($this->model))->getQuery()->execute();
        $uniqueStringMap = array();
        $classname = get_class($this->model);
        $uniqueFunction = $classname::getDefaultGetDisplayStringFunction();

        foreach ($modelList as $num => $model) {
            /**
             * @var BaseEntity $model
             */
            $uniqueStringMap[$uniqueFunction($model)] = $model;
        }


        $factory = new BaseEntityFactory($this->model);
        foreach ($this->reader as $row) {
            $importModel = $factory->createFromLabelArray($row);
            $comparisonSet = new ComparisonSet();
            if (isset($uniqueStringMap[$uniqueFunction($importModel)])) {
                $comparisonSet->setCurrentModel($uniqueStringMap[$uniqueFunction($importModel)]);
            }
            $comparisonSet->setImportModel($importModel);
            $comparisonSet->compareAndCreateResultModel();
            if (!$comparisonSet->isCurrentAndResultSame()) {
                $this->comparisonData[] = $comparisonSet;
            }
        }
    }

    /**
     * @return ComparisonSet[]
     */
    public function getComparisonData()
    {
        return $this->comparisonData;
    }

}