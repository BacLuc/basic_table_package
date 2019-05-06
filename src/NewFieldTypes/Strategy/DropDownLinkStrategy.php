<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 02.05.17
 * Time: 11:43
 */

namespace Concrete\Package\BasicTablePackage\Src\NewFieldTypes\Strategy;


use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Concrete\Package\BasicTablePackage\Src\NewFieldTypes\AbstractAssociationField;

class DropDownLinkStrategy extends DropdownStrategy implements Validator, EditRepresentation, ViewRepresentation
{

    /**
     * @var AbstractAssociationField
     */
    protected $fieldType;

    public function validatePost($value)
    {

        $values = array_keys($this->getOptions());
        if (count($values) == 0) {
            return true;
        }
        if (in_array($value, $values)) {

            //TODO check if is to refactor
            $model = BaseEntityRepository::getEntityById($this->fieldType->getTargetEntity(), $value);

            if ($model != null && $model != false) {
                BaseEntity::getEntityManagerStatic()->persist($model);
                $this->setSQLValue($model);
            } else {
                $this->setSQLValue(null);
            }

        } else {
            if ($this->fieldType->getNullable() !== false && $value == null) {
                return true;
            }
            return false;
        }
        //because of directeditfield references of referenced object could have been changed, persist them too


        return true;
    }

    public function getOptions()
    {
        return $this->fieldType->getOptions();
    }

    public function setSQLValue($value)
    {
        $targetEntity = $this->fieldType->getTargetEntity();
        if ($value instanceof $targetEntity || is_null($value)) {
            $this->value = $value;
        } else {
            throw new InvalidArgumentException("Parameter \$value is " . get_class($value) . ", should be "
                                               . $this->targetEntity . " ");
        }
        return $this;
    }

    public function getInputHtml($form, $clientSideValidationActivated = true)
    {
        $value = $this->getSQLValue();
        $default = $this->fieldType->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }
        if ($this->value != null) {
            $setValue = $value->getId();
        } else {

            $setValue = "";

        }
        $html = static::select($this->fieldType->getHtmlId(), $this->fieldType->getPostName(), $this->getOptions(),
            $setValue);

        $html .= $this->fieldType->getHtmlErrorMsg();
        return $html;
    }

    public function getTableView()
    {
        $value = $this->getSQLValue();
        if (is_null($value)) {
            return "";
        }
        /**
         * @var callable $displayStringFunction
         */
        $displayStringFunction = $this->fieldType->getGetDisplayStringFunction();
        return $displayStringFunction($value);
    }

    public function setValue($value)
    {
        $this->setSQLValue($value);
        return $this;
    }

    public function getViewRepresentation()
    {
        $options = $this->getOptions();
        return $options[$this->getSQLValue()->getId()];
    }

}