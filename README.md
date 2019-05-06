# BasicTablePackage
A Package for Concrete5 to enable easy CRUD funktionality for Doctrine Entities (or other Data Sources).

Important Files/Folders:

* [blocks](blocks/README.md)  
  * [blocks/basic_table_block_packaged](blocks/basic_table_block_packaged/README.md)  
* [languages](languages/README.md)  
* [src](src/README.md)  
  * [src/BlockOptions](src/BlockOptions/README.md)  
  * [src/DiscriminatorEntry](src/DiscriminatorEntry/README.md)  
  * [src/EntityViews](src/EntityViews/README.md)  
  * [src/Exceptions](src/Exceptions/README.md)  
  * [src/FieldTypes](src/FieldTypes/README.md)
*  controller.php
    
    
## Package Controller controller.php
The Package Controller handles the installation or uninstallation of the Package.  
First the Namespace:
```php
<?php
namespace Concrete\Package\BasicTablePackage;
```
The namespace always starts with Concrete\Package for Packages.
Then follows the modified package folder name (in this case basic\_table\_package)
You start with camelcase, and when you have a underscore (\_), then you skip the underscore, but the next letter is uppercase.  

Use statements:  
```php
<?php
use Concrete\Core\Block\BlockType\BlockType;
use Concrete\Core\Package\Package;
use Concrete\Core\Foundation\ClassLoader;
use Concrete\Package\BasicTablePackage\Src\DiscriminatorEntry\DiscriminatorListener;
use Doctrine\ORM\EntityManager;
use Punic\Exception;
use Loader;
use Core;
```
Remeber that you have to make a use statement for every class you want to use in this file,
which is not in the same namespace. If you want to use a PHP intern class, you have to prepend a \\ to assure that the global namespace is used.Like for example:  
```php
<?php
new \DateTime();
``` 
We continue with the class definition this is also for every package the same.
It always has to extend Package. (I never used inheritence with package controllers until now)
```php
<?php
class Controller extends Package
{
 //..class code
}
```  
Then we have some standard properties
```php
<?php
class Controller extends Package
{
    protected $pkgHandle = 'basic_table_package';
    protected $appVersionRequired = '5.7.4';
    protected $pkgVersion = '0.0.1';
    //..class code
 }
```
  As $pkgHandle you use the name of the package folder (and your project).  
  As $appVersionRequired you take the minimum version of concrete5 required for this package. Because basic_table_package uses Classes for the ORM functionality, concrete5.7.4 or higher is required.  
  As $pkgVersion you always start with 0.0.1 (recomended by Concrete5), and as soon as you want to publish your package on the marketplace, you have to find a system for your version numbering. I don't have one either.  
  
  Then we have the two function for the Package Name and the Package Description.
  They are mandatory and self explaining:
```php
<?php
class Controller extends Package
{
    //..class code
     public function getPackageName()
        {
            return t("BasicTablePackage");
        }
    
        public function getPackageDescription()
        {
            return t("Package to provide a basic CRUD from DB to GUI");
        }
    //..class code
 }
```
  Important is the t() function. It is used for translation, so every string visible to Visitors should be wrapped in the t function. If you have variables, you can use the t function like this:  
```php
  <?php
  t("My string contains now a variable1 %s and a variable 2 %s",$firstvariable, $secondvariable);
```
  
Now we get to the important function Controller::install. It handles the installation of the Package. Here, i install the basic_table_block 
```php
<?php
class Controller extends Package
{
    //..class code
     public function install()
         {
             //we get the EntityManager once before we start with installation
            $em = $this->getEntityManager();
             //begin transaction, so when block install fails, but parent::install was successfully, you don't have to uninstall the package
             $em->getConnection()->beginTransaction();
             try {
                 
                //first we call the parent install method
                 $pkg = parent::install();
                 //now after the parent installation, Our EntityManager has the
                 //discriminator Listener activated. More about that in src/DiscriminatorEntry/README.md
                 $em = $this->getEntityManager();
                 //install the basic_table_block_packaged
                 BlockType::installBlockType("basic_table_block_packaged", $pkg);
     
                //when everything is ok, we commit the db operations
                 $em->getConnection()->commit();
             }catch(Exception $e){
                 //else, we roll back
                 $em->getConnection()->rollBack();
     
                 throw $e;
             }
     
     
         }
    //..class code
 }
```  
In the uninstall Method, we just delete the entries in the table BlockType which reference our Package.
Then, we uninstall the Package itself. Again wrapped in a Transaction.  

#### Controller::addDiscriminatorListenerToEm
Then we have the very important Function Controller::addDiscriminatorListenerToEm
It adds the Support of the Annotation @DiscriminatorEntry to Doctrine. More in [src/DiscriminatorEntry](src/DiscriminatorEntry/README.md)  

And finally the getEntityManager function. After parent::install() is called, the autoloader finds the other classes in this Package, especially the Classes in src/DiscriminatorEntry, which are needed to add the DiscrmininatorListener to the EntityManager

## Package Icon icon.png
icon.png can be any image. It is shown in extend Concrete5 left of the Package Name and Description. Use a small icon.