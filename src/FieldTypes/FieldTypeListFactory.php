<?php
/**
 * Created by PhpStorm.
 * User: lucius
 * Date: 02.05.17
 * Time: 14:36
 */

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;


use Concrete\Package\BasicTablePackage\Controller;
use Concrete\Package\BasicTablePackage\Src\AssociationBaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\ExtendedAssociationEntity;

class FieldTypeListFactory
{
    private $baseEntityInstance;
    private $fieldTypes;

    public function __construct(BaseEntity $baseEntityInstance)
    {
        $this->baseEntityInstance = $baseEntityInstance;
    }

    public function createFieldList()
    {

        $metadata = Controller::getEntityManagerStatic()->getMetadataFactory()
                              ->getMetadataFor(get_class($this->baseEntityInstance))
        ;

        //get the default field types for fieldnames
        foreach ($metadata->getFieldNames() as $fieldnum => $fieldname) {
            try {

                $mapping = $metadata->getFieldMapping($fieldname);
                switch ($mapping['type']) {

                    case 'boolean':
                        $this->fieldTypes[$fieldname] =
                            new BooleanField($fieldname, t($fieldname), t("post" . $fieldname));
                        break;
                    case 'date':
                        $this->fieldTypes[$fieldname] =
                            new DateField($fieldname, t($fieldname), t("post" . $fieldname));
                        break;
                    case 'integer':
                        $this->fieldTypes[$fieldname] =
                            new IntegerField($fieldname, t($fieldname), t("post" . $fieldname));
                        break;
                    case 'float':
                        $this->fieldTypes[$fieldname] =
                            new FloatField($fieldname, t($fieldname), t("post" . $fieldname));
                        break;
                    default:
                        $this->fieldTypes[$fieldname] = new Field($fieldname, t($fieldname), t("post" . $fieldname));
                        break;
                }

                if ($fieldname == $this->baseEntityInstance->getIdFieldName()) {
                    $this->fieldTypes[$fieldname] = new HiddenField($fieldname, t($fieldname), t("post" . $fieldname));
                }
            } catch (MappingException $e) {
                //wenn das feld ein association mapping ist, dann gibts error
                // $this->fieldTypes[$field] = new Field($field, t($field), t("post" . $field));
            }
        }
        if (strpos(get_called_class(), "CanEditOption") !== false) {
            //var_dump($metadata->getAssociationMappings());
        }
        //get the default field types for associations:
        foreach ($metadata->getAssociationMappings() as $className => $associationMeta) {
            /*
             * to get defaultDisplayfunction, you have to use the full namespace, because in $className the namespace is missing, but it is in $associationMeta['targetEntitty']
             * */
            if ($metadata->isSingleValuedAssociation($associationMeta['fieldName'])) {

                $this->fieldTypes[$associationMeta['fieldName']] =
                    new DropdownLinkField($associationMeta['fieldName'], t($associationMeta['fieldName']),
                        t("post" . $associationMeta['fieldName']));
                $this->fieldTypes[$associationMeta['fieldName']]->setLinkInfo($this->baseEntityInstance,
                    $associationMeta['fieldName'], $associationMeta['targetEntity'], $associationMeta['mappedBy'],
                    $associationMeta['type'], $associationMeta['targetEntity']::getDefaultGetDisplayStringFunction());
            } elseif ($metadata->isCollectionValuedAssociation($associationMeta['fieldName'])) {
                //create instance of targetentity to check wether it is a assocationentity or a direct assocation
                $targetEntityInstance = new $associationMeta['targetEntity']();
                if ($targetEntityInstance instanceof ExtendedAssociationEntity) {

                } elseif ($targetEntityInstance instanceof AssociationBaseEntity) {
                    $this->fieldTypes[$associationMeta['fieldName']] =
                        new DropdownMultilinkFieldAssociated($associationMeta['fieldName'],
                            t($associationMeta['fieldName']), t("post" . $associationMeta['fieldName']));
                } else {
                    $this->fieldTypes[$associationMeta['fieldName']] =
                        new DropdownMultilinkField($associationMeta['fieldName'], t($associationMeta['fieldName']),
                            t("post" . $associationMeta['fieldName']));
                }
                $this->fieldTypes[$associationMeta['fieldName']]->setLinkInfo($this->baseEntityInstance,
                    $associationMeta['fieldName'], $associationMeta['targetEntity'], $associationMeta['mappedBy'],
                    $associationMeta['type'], $associationMeta['targetEntity']::getDefaultGetDisplayStringFunction());
            }
        }

        return $this->fieldTypes;
    }

}