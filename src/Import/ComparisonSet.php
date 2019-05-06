<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 23.03.17
 * Time: 14:49
 */

namespace Concrete\Package\BasicTablePackage\Src\Import;


use Concrete\Package\BasicTablePackage\Src\BaseEntity;

class ComparisonSet implements \Countable, \SeekableIterator
{
    const KEY_CURRENT  = 'current';
    const KEY_IMPORTED = 'imported';
    const KEY_RESULT   = 'result';
    const KEY_NEWENTRY = 'newentry';
    /**
     * @var int
     */
    protected $position = 0;
    /**
     * @var BaseEntity
     */
    protected $currentModel;
    /**
     * @var BaseEntity
     */
    protected $importModel;
    /**
     * @var BaseEntity
     */
    protected $resultModel;

    /**
     * @return BaseEntity
     */
    public function getCurrentModel()
    {
        return $this->currentModel;
    }

    /**
     * @param BaseEntity $currentModel
     * @return ComparisonSet
     */
    public function setCurrentModel(BaseEntity $currentModel)
    {
        $this->currentModel = $currentModel;
        return $this;
    }

    /**
     * @return BaseEntity
     */
    public function getImportModel()
    {
        return $this->importModel;
    }

    /**
     * @param BaseEntity $importModel
     * @return ComparisonSet
     */
    public function setImportModel(BaseEntity $importModel)
    {
        $this->importModel = $importModel;
        return $this;
    }

    /**
     * @return BaseEntity
     */
    public function getResultModel()
    {
        return $this->resultModel;
    }

    /**
     * @param BaseEntity $resultModel
     * @return ComparisonSet
     */
    public function setResultModel(BaseEntity $resultModel)
    {
        $this->resultModel = $resultModel;
        return $this;
    }

    public function isCurrentAndResultSame()
    {
        if ($this->resultModel == null) {
            //TODO maybe throw exception instead
            $this->compareAndCreateResultModel();
        }
        $fieldTypes = $this->resultModel->getFieldTypes();
        foreach ($fieldTypes as $sqlFieldName => $field) {
            if ($this->currentModel->get($sqlFieldName) != $this->resultModel->get($sqlFieldName)) {
                return false;
            }
        }
        return true;

    }

    public function compareAndCreateResultModel()
    {
        $classname = get_class($this->importModel);
        if ($this->currentModel == null) {
            $this->currentModel = new $classname();
            $this->resultModel = $this->importModel;
            return $this;
        }

        //TODO what happens if importmodel is null


        $this->resultModel = new $classname();
        $importModelFieldTypes = $this->importModel->getFieldTypes();
        foreach ($this->currentModel->getFieldTypes() as $sqlFieldName => $fieldType) {
            switch ($fieldType) {

                default:
                    if ($sqlFieldName == $this->currentModel->getIdFieldName()) {
                        $this->resultModel->set($sqlFieldName, $this->currentModel->getId());
                    } elseif ($this->importModel->getFieldTypeIsNotSet($sqlFieldName)) {
                        $this->resultModel->set($sqlFieldName, $this->currentModel->get($sqlFieldName));
                    } else {
                        $this->resultModel->set($sqlFieldName, $this->importModel->get($sqlFieldName));
                    }
                    break;
            }
        }

    }

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        switch ($this->position) {
            case 0:
                return $this->currentModel;
                break;
            case 1:
                return $this->importModel;
                break;
            case 2:
                return $this->resultModel;
                break;

        }
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        $this->position++;

    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        switch ($this->position) {
            case 0:
                return static::KEY_CURRENT;
                break;
            case 1:
                return static::KEY_IMPORTED;
                break;
            case 2:
                return static::KEY_RESULT;
                break;

        }
    }

    /**
     * Rewind the Iterator to the first element
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        $this->position = 0;
    }

    /**
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return 3;
    }

    /**
     * Seeks to a position
     * @link http://php.net/manual/en/seekableiterator.seek.php
     * @param int $position <p>
     * The position to seek to.
     * </p>
     * @return void
     * @since 5.1.0
     */
    public function seek($position)
    {
        $tempposition = $this->position;
        $this->position = $position;
        if ($this->valid()) {
            return;
        }
        $this->position = $tempposition;
        throw new \OutOfBoundsException("invalid seek position ($position)");
    }

    /**
     * Checks if current position is valid
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->position >= 0 && $this->position < 3;
    }
}