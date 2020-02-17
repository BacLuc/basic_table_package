# The Basic Table Block
This is the block which provides the basic crud functionality for database tables, and can be modified to serve other purposes.
The css folder contains the css needed, the js folder the js needed. Most of them are unmodified library code,
except for
  
[clientSideValidation.js](js/clientSideValidation.js)  
This file is needed to provide client side validation, which is done on one side with the native HTML client side validation, and with the [parsley.js]() library.  

[DirectEditAssociatedEntityMultipleField.js](js/DirectEditAssociatedEntityMultipleField.js)  
This file adds the autocompletion for DirectEditAssociatedEntity and DirectEditAssicatiedEntityMultiple Fields and the add/remove functionality for the DirectEditAssicatiedEntityMultiple.  

In the views directory are the table and the form view of the block.

## The Package Controller
The [controller.php](controller.php) is the Package Controller, the most important file. 
It contains all the needed Functions and some helpers.  
The normal live cycle of a table package is as follows
1. Adding Block to Area
    1. function __construct is called (see 4.i)
    2. function getBlockOptions is called (in file [add.php](add.php), which includes [edit.php](edit.php))
        1. If no block Id (bID) is set, the required Options are returned
        2. Else the already saved BlockOptions are loaded and returned
     3. Foreach Block Option, the Form View is called (Which displays a label and a input for the block option. If it is already filled with data, the data is displayed)
     4. When the user presses add, post request is sent and function save is called with the post variables
         1. parent::save($args) is called
         2. BasicTableInstance is loaded
             1. If there is already a BasicTableInstance with the same id as the block, this is returned
             2. else a new one is generated and returned
         1. the sendt values get compared with the required options, given the right options to validate, 
         then everything gets persisted and the new Block is created.
2. Editing existing Block: same as 1.
3. Deleting Block:
    1. BasicTableInstance is deleted
    2. parent::delete is called
    3. if everything is ok, transaction is commited, else rollback
4. Table View
    1. function __construct is called
        1. parent constructor is called
        2. Session is checked if editkey exists
        3. model is found. If no Model is provided by child class, \Concrete\Package\BaclucC5Crud\Src\ExampleEntity is taken
        4. If editkey is set, the right model is loaded, else a new one is generated
        5. Postfieldmap is generated. (a map where the key is the postname of a field, and the value is the sql fieldname)
        6. It is checked if the session variable set which indicates that we are in form view.
        7. BasicTableInstance is loaded (which holds the blockoptions)
        8. The required BlockOptions are loaded
     2. [view.php](view.php) is called
        1. Because view.php may be called several times, it is checked if the block has already been executed. if not, continue
        2. then, [views/table_view.php](views/table_view.php) is included
            1. The table data is loaded with function displayTable() as 2 dimensional associative array.
            2. Icon to create Entry is created
            3. function getFields is called
                 1. if the editkey is set, the fields of a specified instance of the model are returned
                 2. else the fields of $controller->model are returned
            3. Iteration through the Fields to display the header column
            4. Iteration through the rows to display all the rows
                1. Iteration through the fields: First instead of displaying the id, displayActions is called  
                   In DisplayAction the id of the row is saved, and the icons to edit or delete are displayed
                2. Iteration through the fields: For all the other fields, the value of the row is set as the SQL Value of the field. Then the getTableView is called on the Field to get the HTML for the table cell
            2. Icon to create Entry is created
         1. The Controller is set to executed
      1. The user is now possibly able to:
          1. Add a new Entry
          2. Edit an Entry
          3. Delete an Entry
1. Add a new Entry
    1. prepareFormEdit is called
        1. The Session Variable prepareFormEdit is set
        2. The Controller property isFormView is set to true
2. Edit a existing Entry
    1. function action_edit_row_form is called
        1. The edit key is set from the post variables
        2. if action is edit: prepareFormEdit is called (see 5.i)
        3. if action is delete: deleteRow is called
            1. The row is deleted
            2. the controller is set to isFormView = false
            3. The session variable isFormView is deleted
            4. The editkey of the controller is deleted
            5. The editkey in the session is deleted
         1. To prevent repeated deletion/edit, redirectToView is called so that the post and get parameters are lost
3. Delete an Entry
    1. function action_edit_row_form is called(see 6.i)
4. form view
    1. function __construct is called (see 4.i)
    2. [view.php](view.php) is called
         1. Because view.php may be called several times, it is checked if the block has already been executed. if not, continue
         2. then, [views/form_view.php](views/form_view.php) is included
               1. the action of the form is set as $view->action('save_row'), that the function action_save_row of the controller is called
               2. if clientside validation is activated, the class is added to the form
               3. then possible former consistency errors are loaded and displayed (if saving the form failes, the form is displayed again)
               4. Then, the model is loaded (if the editkey is set, the model with this id is loaded)
               5. It is checked if the model has a defaultFormView, if so, the errorMsgs are handed over and then getFormView of this defautFormView is called
               6. if not
                   1. getFields is called (see 4.ii.b.c)
                   2. The former Errors are loaded
                   3. Iteration through each field  
                        + check if it should be displayed  
                        + SQL Value is set  
                        + Error Messages are set  
                        + View is handed over  
                        + getFormView of the Field is called, which returns label and input  
               
               
5. save row
    1. first the page is loaded
    2. Then, the post values are validated by calling checkPostValues
        1. the request is validated (token, user permission) with validateRequest
        2. if this failes, handleFormError is called
            1. prepareFormEdit is called
            2. the input values are saved in the request (that they can be put back in the form again) (exit)
        3. else if cancel is set, delete isFormView and editkey and redirect (exit)
        4. Possible additional fields are added which contain default values for the table (not yet used)
        5. Then the fields are loaded
        6. Iteration through all the fields
            1. for each field, validatePost is called, which validates the input, and if valid, saves it in the field
            2. If the input is not valid, the form is in error state and the error messages are collected
            3. else the sql value is retrieved from the field by calling getSQLValue and saved in an array
         7. If there was an error in one field, handleFormError is called (see 5.ii.b)
         8. else the array is returned
     1. Then, if editkey is set, the right model to edit is loaded, else a new entry has to be made
     2. Then, persistValues($model,$v) is called to put the values into the model
         1. the model is persistet
         2. Iteration through all fields
             1. If the value is an child of BaseEntity, it is persistet too
             2. if it is an instance of Collection, all the values in the collection are persistet to
             3. the value of the model is set to the value provided by the user
          1. checkConsistency of the model is called, if it failes, consistencyerrors are saved and handleFormError is called
          2. else true is returned
      1. the entities are flushed by calling EntityManager::flush
      2. finishFormView is called (session variables are deleted)
      3. if $redirectonsuccess is true, redirectction is called
      
      
## How are Javascript and css provided?
In the function on_start, first register js and css files as assets. If you add a less file, it is interpreted by concrete5.
```php
<?php
$package = Package::getByHandle("basic_table_package");
        $al = \Concrete\Core\Asset\AssetList::getInstance();

        $al->register(
            'javascript', 'typeahead', 'blocks/basic_table_block_packaged/js/typeahead.bundle.js',
            array('minify' => false, 'combine' => true)
            , $package
        );
```
Then, you group the assets to a assetgroup with a name, that you have to just add a group to provide multiple css and js files
```php
<?php
$groupAssets = array(
            array('css', 'font-awesome'),
            array('css', 'tagsinputcss'),
            array('css', 'datepickercss'),
            array('css', 'bootgridcss'),
            array('css', 'basicTablecss'),
            array('css', 'typeaheadcss'),
            array('javascript', 'jquery'),
            array('javascript', 'bootstrap'),
            array('javascript', 'typeahead'),
            array('javascript', 'datepicker'),
            array('javascript', 'bootgrid'),
            array('javascript', 'tagsinput'),
            array('javascript', 'block_auto_js'),
        );
        $al->registerGroup('basictable', $groupAssets);
```
Then you have the function registerViewAssets, which tells concrete5 which assetgroups have to be provided that the block works
```php
<?php
 function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('basictable');
    }
```

And that it is available in the block add/edit form to, you have to require it there too:
```php
<?php
/**
     * register also for add form
     */
     function add()
    {
        $this->requireAsset('basictable');
    }
```