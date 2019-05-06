# The Source Folder src
Here is everything that is related to Models, BlockOptions, DiscriminatorEntry and FieldTypes.
For further docs on the different files, please read the README's of the folders
 * [BlockOptions](BlockOptions/README.md)  
 * [DiscriminatorEntry](DiscriminatorEntry/README.md)  
 * [EntityViews](EntityViews/README.md)  
 * [Exceptions](Exceptions/README.md)  
 * [FieldTypes](FieldTypes/README.md)

Here is the documentation about the files which are directly in this folder.
First, from here on, the modified PSR4 standard is not guilty anymore. From the src folder (Which has the namespace \Concrete\Package\BasicTablePackage\Src)
on, the file paths and the namespaces have to be the same. Thats why the directories here have uppercase letters.

#### AbstractFormView.php
[ AbstractFormView.php](AbstractFormView.php)  
Abstract class to provide common functionality for special form views for entites, like 
the constructor with the link to the entity, setParentPostName to use the AbstractFormView for DirectEdit Fields
, setErrorMsg to add the former Error messages and getFilledVariables, which returns an 
array with the sqlfieldnames as keys and as value an array with the label and the form field.


#### AbstractSubFormView.php
[ AbstractSubFormView.php](AbstractSubFormView.php)  
Abstract class to distinct AbstractFormView and AbstractSubFormView's, no functionality yet

#### AssociationBaseEntity.php
[AssociationBaseEntity.php](AssociationBaseEntity.php)  
Abstract class for Entities, which act as a cross table(entity)

#### ExtendedAssociationBaseEntity.php
[ExtendedAssociationBaseEntity.php](ExtendedAssociationBaseEntity.php)  
Abstract class for Entities, which act as a cross table(entity), which has additional properties.

#### AssociationCache.php
[ExtendedAssociationBaseEntity.php](ExtendedAssociationBaseEntity.php)  
Entity to monitor from which entity to which entity associations exists, to show the information on DirectEditfields,
which entities are currently connected to this dataset

#### BaseEntity.php
Abstract class to provide common functionality for entities. All Entities used for packages dependend on BasicTablePackage have to be child classes of BaseEntity.php
* getFieldTypes()  
 Returns an array of the FieldTypes of this Entity. If $this->fieldTypes is not yet set, $this->getDefaultFieldTypes is called.
* getDefaultGetDisplayStringFunction()  
 Returns a common function which returns a unique string for the BaseEntity passed as parameter consisting of every property.
 Should be overridden in the children, but that every entity has such a function, it is also implemented here.
* getAsAssoc()  
 Returns the values of an enttiy as an array with the sql columns as keys. Associated entities are still objects
* getEntityManager()  
 returns the Package Entity Manager, that all entities use the same EntityManager
* getId()  
 returns the identifier. Must be overridden when the identifier column is not called id
* getIdFieldName()  
  returns the name of the identifier column. Must be overridden when the identifier column is not called id
* setDefaultFieldTypes()  
 Scans the Entity Metadata with the Doctrine ORM MetadataFactory for all columns.
 according to the column type, the matching Field Class is used:
 Only column types with a specified Doctrine Column type can be recognized, for example email field type cannot.
 If the id is null, the default values are set too
 ```php
 <?php
  switch ($mapping['type']) {
 
                     case 'boolean':
                         $this->fieldTypes[$fieldname] = new BooleanField($fieldname, t($fieldname), t("post" . $fieldname));
                         break;
                     case 'date':
                         $this->fieldTypes[$fieldname] = new DateField($fieldname, t($fieldname), t("post" . $fieldname));
                         break;
                     case 'integer':
                         $this->fieldTypes[$fieldname] = new IntegerField($fieldname, t($fieldname), t("post" . $fieldname));
                         break;
                     case 'float':
                         $this->fieldTypes[$fieldname] = new FloatField($fieldname, t($fieldname), t("post" . $fieldname));
                         break;
                     default:
                         $this->fieldTypes[$fieldname] = new Field($fieldname, t($fieldname), t("post" . $fieldname));
                         break;
                 }
 ```
  And does the same with associations
  ```php
  <?php
  if($metadata->isSingleValuedAssociation($associationMeta['fieldName'])){
  
                  $this->fieldTypes[$associationMeta['fieldName']] = new DropdownLinkField($associationMeta['fieldName'], t($associationMeta['fieldName']), t("post" . $associationMeta['fieldName']));
                  $this->fieldTypes[$associationMeta['fieldName']]->setLinkInfo($this,$associationMeta['fieldName'],$associationMeta['targetEntity'],$associationMeta['mappedBy'],$associationMeta['targetEntity']::getDefaultGetDisplayStringFunction() );
              }elseif($metadata->isCollectionValuedAssociation($associationMeta['fieldName'])){
                   //create instance of targetentity to check wether it is a assocationentity or a direct assocation
                  $targetEntityInstance = new $associationMeta['targetEntity'];
                  if($targetEntityInstance instanceof  ExtendedAssociationEntity){
  
                  }elseif($targetEntityInstance instanceof AssociationBaseEntity){
                      $this->fieldTypes[$associationMeta['fieldName']] = new DropdownMultilinkFieldAssociated($associationMeta['fieldName'], t($associationMeta['fieldName']), t("post" . $associationMeta['fieldName']));
                  }else {
                      $this->fieldTypes[$associationMeta['fieldName']] = new DropdownMultilinkField($associationMeta['fieldName'], t($associationMeta['fieldName']), t("post" . $associationMeta['fieldName']));
                  }
                  $this->fieldTypes[$associationMeta['fieldName']]->setLinkInfo($this,$associationMeta['fieldName'],$associationMeta['targetEntity'],$associationMeta['mappedBy'],$associationMeta['targetEntity']::getDefaultGetDisplayStringFunction());
              }
  ```
* mergeCollections($coll1,$coll2)  
 Was meant to merge 2 collections instead of always adding a whole new collection to collection valued properties.
 not yet used
 
* toTableAssoc()  
 Similar to getAsAssoc, but this time object of stdClass is returned, and Objects/Collections are converted to Strings
 
* getTypeaheadTemplate()  
 returns a string with an html template how the option in the autocompletion should look like.
 as variables, all the properties are available (they are generated with toTableAssoc), plus the unique string
 generated by the class' defaultGetDisplayStringFunction  
 they have to be surrounded with two swifted braces (like \{\{id\}\})  
 else normal html is allowed  
 
  default is:  
  ```php
  <?php
  $template = "<div>{{uniqueIdString}}</div>";
  ```
* static getBaseEntityFromProxy(BaseEntity $entity)  
 if an entity was lazy loaded, you can use this function to load the entity
 
*  checkConsistency  
 is normally automatically called after an entity is persistet but not yet flushed. To override in child classes to
 check if the consistency constraints are matched. Is circle aware, throws an exception if it is already being checked,
 which should be catched
 
* static getBuildQueryWithJoinedAssociations($classname, callable $addFilterFunction)
 Returns a query builder instance with the entity itself and all associations left joined.
 for more, see phpdoc of function
 
#### BaseEntity.php
 [BaseEntity.php](BaseEntity.php)  
 Entity which holds the BlockOptions for a Block. Concists currently just of an id. bID of the BasicTableInstance has to be the same as the bID of the Block.
 
#### EntityGetterSetter.php
 [EntityGetterSetter.php](EntityGetterSetter.php)  
  Because the properties of child classes are not visible to parent classes, normally every class had to implement get($name), set($name,$value) and the magic functions __get($name) and __set($name,$value). These functions have been extracted to the trait EntityGetterSetter.php. Every Entity has to use this trait.

#### ExampleEntity.php
 [ExampleEntity.php](ExampleEntity.php)  
 If no entity is given for a block which extends basic_table_block_packaged, ExampleEntity is used which has just a key and value. It's also here to test the basic functionality.

#### Group.php
 [Group.php](Group.php)  
 Because we need a reference to the concrete5 intern Group Table, and there is not yet an Entity for it, this Group class was programmed with the same columns as the original Group Table.
 If the schema of concrete5 changes, this class has to be adapted.