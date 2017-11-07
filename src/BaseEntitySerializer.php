<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 24.03.17
 * Time: 17:04
 */

namespace Concrete\Package\BasicTablePackage\Src;


use Concrete\Package\BasicTablePackage\Src\FieldTypes\AssociationFieldInterface;

class BaseEntitySerializer
{
    const KEYTYPE_SQL_FIELDNAME   = 'sqlfieldname';
    const KEYTYPE_POST_FIELDNAME  = 'postname';
    const KEYTYPE_LABEL_FIELDNAME = 'labelfieldname';
    const VALUE_TYPE_SQL          = 'sqlvalue';
    const VALUE_TYPE_HUMAN        = 'humanreadablevalue';
    /**
     * @var BaseEntity
     */
    protected $baseEntity;

    public function __construct(BaseEntity $baseEntity)
    {
        $this->baseEntity = $baseEntity;
    }

    public function convertTo($keytype, $valuetype, $associationHuman = false)
    {
        $returnArray = array();
        foreach ($this->baseEntity->getFieldTypes() as $sqlFieldname => $fieldType) {
            $key = null;
            switch ($keytype) {
                case static::KEYTYPE_SQL_FIELDNAME:
                    $key = $fieldType->getSQLFieldName();
                    break;
                case static::KEYTYPE_POST_FIELDNAME:
                    $key = $fieldType->getPostName();
                    break;
                case static::KEYTYPE_LABEL_FIELDNAME:
                    $key = $fieldType->getLabel();
                    break;
                default:
                    throw new \InvalidArgumentException('$keytype has to be one of the KEYTYPE constants');
                    break;
            }
            $value = null;
            if (($valuetype == static::VALUE_TYPE_SQL && $associationHuman
                 && $fieldType instanceof AssociationFieldInterface)
                || $valuetype == static::VALUE_TYPE_HUMAN) {
                $value = $fieldType->setSQLValue($this->baseEntity->get($sqlFieldname))->getTableView();
            } elseif ($valuetype == static::VALUE_TYPE_SQL) {
                $value = $this->baseEntity->get($sqlFieldname);
            } else {
                throw new \InvalidArgumentException('$keytype has to be one of the VALUETYPE constants');
            }


            $returnArray[$key] = $value;
        }
        return $returnArray;
    }


}