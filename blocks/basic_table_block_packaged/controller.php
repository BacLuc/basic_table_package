<?php

namespace Concrete\Package\BasicTablePackage\Block\BasicTableBlockPackaged;

use Concrete\Core\Block\Block;
use Concrete\Core\Block\BlockController;
use Concrete\Core\File\Set\Set;
use Concrete\Core\Http\Request;
use Concrete\Core\Package\Package;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Permission\Response\BlockResponse;
use Concrete\Core\Support\Facade\Application;
use Concrete\Package\BasicTablePackage\Src\BaseEntity;
use Concrete\Package\BasicTablePackage\Src\BaseEntityFactory;
use Concrete\Package\BasicTablePackage\Src\BaseEntityRepository;
use Concrete\Package\BasicTablePackage\Src\BaseEntitySerializer;
use Concrete\Package\BasicTablePackage\Src\BasicTableInstance;
use Concrete\Package\BasicTablePackage\Src\BlockOptions\CanEditOption;
use Concrete\Package\BasicTablePackage\Src\ExampleEntity;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DirectEditInterface;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\DropdownLinkField;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field as Field;
use Concrete\Package\BasicTablePackage\Src\Helpers\CsvResponse;
use Concrete\Package\BasicTablePackage\Src\Import\EntityDataComparer;
use Core;
use Doctrine\Common\Collections\ArrayCollection;
use GuzzleHttp\Query;
use Loader;
use OAuth\Common\Exception\Exception;
use Page;
use Port\Csv\CsvReader;
use User;

class Controller extends BlockController
{


    /**
     * if the block is already executed
     * @var bool
     */
    protected static $executed = false;
    protected static $viewvars = array();
    /**
     * the options that can be set
     * @var array
     */
    public    $options                 = array();
    protected $pkgAutoloaderRegistries = array(
        'Src' => 'Concrete\Package\Src',
    );
    /**
     * @var string
     * Blockhandle, same name as the directory
     */
    protected $btHandle = 'basic_table_block_packaged';
    /**
     * If the block is in form view
     * @var bool
     */
    protected $isFormview = false;
    /**
     * the currently edited id
     * @var null int
     */
    protected $editKey = null;
    /**
     * the Block id
     * @var null int
     */
    protected $bID = null;
    /**
     * to handle a post request more easy, here is the reverse map postname -> field
     * @var array
     */
    protected $postFieldMap = array();
    /**
     * if validatePost throws an error, here are the errormessages stored
     *
     * @var array
     */
    protected $errorMsg = array();
    /**
     * block title
     * @var string
     */
    protected $header = "BasicTablePackaged";
    /**
     * @var \Concrete\Package\BasicTablePackage\Src\BasicTableInstance
     */
    protected $basicTableInstance;
    /**
     * @var Package
     */
    protected $package;
    /**
     * Array of \Concrete\Package\BasicTablePackage\Src\BlockOptions\TableBlockOption
     * @var array
     */
    protected $requiredOptions = array();

    /**
     * @var \Concrete\Package\BasicTablePackage\Src\BaseEntity
     */
    protected $model;

    /**
     * @var Field[]
     */
    protected $errorFields;


    protected $clientSideValidationActivated = true;

    /**
     * @var array
     */
    protected $consistencyErrors = array();
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $entityManager;

    /**
     *
     * Controller constructor.
     * @param null $obj
     */
    function __construct($obj = null)
    {
        parent::__construct($obj);
        if (isset($_SESSION[$this->getHTMLId() . "rowid"])) {
            $this->editKey = $_SESSION[$this->getHTMLId() . "rowid"];
        }

        /*
         * if the basic table block is extended, $this->model is already set and should not be overwritted, that the name of the session variable is set right
         * */
        if ($this->model == null) {
            if ($this->editKey == null) {
                $this->model = new ExampleEntity();
            } else {
                $this->model = $this->getEntityManager()
                                    ->find("Concrete\\Package\\BasicTablePackage\\Src\\ExampleEntity", $this->editKey)
                ;
            }

        } else {
            if ($this->editKey == null) {
            } else {
                $model = BaseEntityRepository::getEntityById(get_class($this->getModel()), $this->editKey);
                if ($model) {
                    $this->model = $model;
                } else {

                    //in case that really happens, stop application
                    throw new \Exception();
                }
            }
        }

        $this->generatePostFieldMap();

        $c = Page::getCurrentPage();

        if (is_object($c)) {
            $this->cID = $c->getCollectionID();
        }

        //check if it is in form view
        if (isset($_SESSION[$this->getHTMLId()]['prepareFormEdit'])) {
            $this->isFormview = $_SESSION[$this->getHTMLId()]['prepareFormEdit'];
        }
        //translate the header
        $this->header = t($this->header);

        //load the current options
        $this->basicTableInstance = $this->getBasicTableInstance();
        if (!isset($this->requiredOptions)) {
            $this->requiredOptions = array();
        }

    }

    /**
     * Returns the id of the block, used in html id's and for the session variable
     *
     * @return string
     */
    function getHTMLId()
    {
        //because model is not always set, and bID plus blockhandle should be unique, we use this for identifiying the block
        return $this->btHandle . $this->bID;
    }

    public function getEntityManager()
    {
        if ($this->entityManager == null) {
            $pkg = Package::getByHandle('basic_table_package');
            $em = $pkg->getEntityManager();
            $this->package = $pkg;
            $this->entityManager = $em;
        }
        return $this->entityManager;
    }

    /**
     * @return BaseEntity|null|object
     */
    public function getModel()
    {
        if ($this->model->getId() != $this->editKey && $this->editKey != null) {
            $query = BaseEntityRepository::getEntityById(get_class($this->model), $this->editKey);

        }
        return $this->model;
    }

    /**
     * generates the postfieldmap. "postkey" => sqlfieldname
     */
    protected function generatePostFieldMap()
    {
        $fields = $this->getFields();
        if (count($fields) > 0) {
            foreach ($fields as $key => $field) {
                $this->postFieldMap[$field->getPostName()] = $key;
            }
        }
    }

    /**
     * @return array of Application\Block\BasicTableBlock\Field
     */
    public function getFields()
    {
        if ($this->editKey == null) {

            return $this->model->getFieldTypes();
        }
        return BaseEntityRepository::getEntityById(get_class($this->model), $this->editKey)->getFieldTypes();

    }

    public function getBasicTableInstance($obj = null)
    {
        if ($this->basicTableInstance == null) {
            if ($obj == null) {
                $bID = $this->bID;
            } else {
                $bID = $obj->getBlockID();
            }
            $bt = BaseEntityRepository::getEntityById(BasicTableInstance::class, $bID);


            if ($bt == null) {
                $bt = new BasicTableInstance();
                $bt->set("bID", $bID);

            }
            $this->basicTableInstance = $bt;
        }
        return $this->basicTableInstance;
    }

    /**
     * Returns the path where the basic table files are stored
     * @return string
     */
    function getBasicTablePath()
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public function getBlockTypeDescription()
    {
        return t("Show a simple Table with Data to create, edit, delete");
    }

    /**
     * @return string
     */
    public function getBlockTypeName()
    {
        return t("BasicTablePackaged");
    }

    /**
     * returns the javascript error messages translated
     * @return array
     */
    public function getJavaScriptStrings()
    {
        return array('file-required' => t('You must select a file.'));
    }

    /**
     * Returns the HTML for the possible actions
     * TODO extract html to view
     * @param $object //view object
     * @param array $row //row (with the rowid)
     * @return string
     */
    function getActions($object, $row = array())
    {
        //".$object->action('edit_row_form')."
        $string = "
    	<td class='actioncell'>
    	<form method='post' action='" . $object->action('edit_row_form') . "'>
    		<input type='hidden' name='rowid' value='" . $row['id'] . "'/>
    		<input type='hidden' name='action' value='edit' id='action_" . $row['id'] . "'>";
        $string .= $this->getEditActionIcon($row);
        $string .= $this->getDeleteActionIcon($row);


        $string .= "</form>
    	</td>";
        return $string;
    }

    /**
     * Returns the HTML for the edit button
     * @param $row
     * @return string
     */
    function getEditActionIcon($row)
    {
        return static::getActionButton($row, "edit", "btn inlinebtn actionbutton edit", "edit", "fa fa-pencil");
    }

    /**
     * @param $row
     * @param $value
     * @param $buttonclass
     * @param $title
     * @param $iconclass
     * @return string
     */
    public static function getActionButton($row, $value, $buttonclass, $title, $iconclass)
    {
        return "<button type='submit'
                            value = '$value'
                            class='$buttonclass'
                            title='" . t($title) . "'
                            aria-label='" . t($title) . "'
                            onclick=\"
                                        $('#action_" . $row['id'] . "').val('$value');
                    \">
                                        <i class ='$iconclass'> </i>
                     </button>";
    }

    /**
     * Returns the HTML for the delete button
     * @param $row
     * @return string
     */
    function getDeleteActionIcon($row)
    {
        return static::getActionButton($row, "delete", "btn inlinebtn actionbutton delete", "delete", "fa fa-trash");

    }

    /**
     * @throws \Exception
     */
    function delete()
    {
        $em = $this->getEntityManager();

        $em->beginTransaction();
        try {
            $em->remove($this->getBasicTableInstance());
            $em->flush();

            parent::delete();
            $em->commit();
        } catch (\Exception $e) {
            $em->rollback();
            throw $e;
        }
    }

    /**
     * if save is pressed, the data is saved to the sql table
     * @throws \Exception
     */
    function action_save_row($redirectOnSuccess = true)
    {


        if ($this->post('rcID')) {
            // we pass the rcID through the form so we can deal with stacks
            $c = Page::getByID($this->post('rcID'));
        } else {
            $c = $this->getCollectionObject();
        }
        //form view is over
        $v = $this->checkPostValues();
        if ($v === false) {
            return false;
        }

        if ($this->editKey == null) {
            $model = $this->model;
        } else {
            $model = BaseEntityRepository::getEntityById(get_class($this->model), $this->editKey);
        }

        if ($this->persistValues($model, $v) === false) {
            return false;
        }

        $this->getEntityManager()->flush();


        $this->finishFormView();
        if ($redirectOnSuccess) {
            $this->redirectToPage($c);

        }


    }

    /**
     * @return bool|array
     */
    public function checkPostValues()
    {
        $this->isFormview = false;
        $u = new User();

        $bo = $this->getBlockObject();

        if ($this->post('rcID')) {
            // we pass the rcID through the form so we can deal with stacks
            $c = Page::getByID($this->post('rcID'));
        } else {
            $c = $this->getCollectionObject();
        }

        if ($this->validateRequest($u) === false) {
            return $this->handleFormError();
        }

        if (isset($_POST['cancel'])) {
            if (isset($_SESSION[$this->getHTMLId() . "rowid"])) {
                unset($_SESSION[$this->getHTMLId() . "rowid"]);
            }
            $_SESSION[$this->getHTMLId()]['prepareFormEdit'] = false;
            $this->redirectToPage($c);
            return true;
        }

        $duID = 0;
        if ($u->getUserID() > 0) {
            $duID = $u->getUserID();
        }

        $v = array();
        $error = false;
        //test
        $errormsg = "";
        $savevalues = $_POST;

        foreach ($this->getFields() as $key => $value) {
            /**
             * @var Field $value
             */
            if ($key == 'id' || $value->showInForm() === false) {
            } else {
                $fieldname = $this->postFieldMap[$value->getPostName()];
                if ($value->validatePost($savevalues[$value->getPostName()])) {
                    $v[$key] = $value->getSQLValue();
                } else {
                    $error = true;
                    $this->errorFields[$value->getPostName()] = $value;
                }
            }
        }
        if ($error) {
            return $this->handleFormError();
        }

        return $v;
    }

    /**
     * @param $u
     * @return bool
     */
    public function validateRequest($u)
    {
        if ($this->requiresRegistration()) {
            if (!$u->isRegistered()) {
                $this->redirect('/login');
                return false;
            }

        }


        $antispam = Loader::helper('validation/antispam');
        if ($antispam->check('', 'survey_block')) { // we do a blank check which will still check IP and UserAgent's
            return true;
        } else {
            return $this->handleFormError();
        }
    }

    function requiresRegistration()
    {
        return $this->requiresRegistration;
    }

    /**
     * @return bool
     */
    public function handleFormError()
    {
//TODO send error msg to client
        $this->prepareFormEdit();
        $_SESSION['BasicTableFormData'][$this->bID]['inputValues'] = $_REQUEST;
        return false;
    }

    public function prepareFormEdit()
    {
        $_SESSION[$this->getHTMLId()]['prepareFormEdit'] = true;
        $this->isFormview = true;
    }

    /**
     * @param $c
     */
    protected function redirectToPage($c)
    {
        if (strlen($c->getCollectionPath()) == 0) {
            $this->redirect("");
        } else {
            $this->redirect($c->getCollectionPath());
        }
    }


    //todo

    /**
     * @param $model
     * @param $v
     * @return bool
     */
    public function persistValues($model, $v)
    {
        $this->getEntityManager()->persist($model);
        //save values
        foreach ($this->getFields() as $key => $value) {
            /**
             * @var BaseEntity $model
             */
            if ($key != $model->getIdFieldName()) {
                if ($v[$key] instanceof BaseEntity) {
                    $this->getEntityManager()->persist($v[$key]);
                } elseif ($v[$key] instanceof ArrayCollection) {
                    /**
                     * @var \Doctrine\Common\Collections\Collection $collection
                     */
                    $collection = $v[$key];
                    foreach ($collection->toArray() as $refnum => $refObject) {
                        $this->getEntityManager()->persist($refObject);
                    }
                }
                $model->set($key, $v[$key]);
            }
        }

        //if the data is inserted, the saveself fields can only save afterwards
        $this->consistencyErrors = $model->checkConsistency();
        if (count($this->consistencyErrors) > 0) {
            return $this->handleFormError();
        }
        return true;
    }

    public function finishFormView()
    {
        if (isset($_SESSION[$this->getHTMLId() . "rowid"])) {
            unset($_SESSION[$this->getHTMLId() . "rowid"]);
        }

        $_SESSION[$this->getHTMLId()]['prepareFormEdit'] = false;
        $_SESSION['BasicTableFormData'][$this->bID]['inputValues'] = null;
        unset($_SESSION['BasicTableFormData'][$this->bID]['inputValues']);
    }

    public function getConsistencyErrors()
    {
        return $this->consistencyErrors;
    }

    /**
     * that the view function of the subclasses is called
     */
    public function on_before_render()
    {
        $this->view();
    }

    public function view()
    {
        foreach (static::$viewvars as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * action display form for new entry
     *
     */
    function action_add_new_row_form()
    {
        $this->prepareFormEdit();
        // $this->view();

    }

    /**
     * action to open a form to edit/delete (manipulate) an existing row
     */
    function action_edit_row_form()
    {
        $u = new User();
        if ($this->requiresRegistration()) {
            if (!$u->isRegistered()) {
                $this->redirect('/login');
            }
        }

        //get the editkey
        $this->editKey = $_POST['rowid'];
        //save it in the session
        $_SESSION[$this->getHTMLId() . "rowid"] = $this->editKey;

        $row = $this->getRowValues();

        if ($_POST['action'] == 'edit' && strlen($this->getEditActionIcon($row)) > 0) {
            $this->prepareFormEdit();
        } elseif ($_POST['action'] == 'delete' && strlen($this->getDeleteActionIcon($row)) > 0) {
            $this->deleteRow();
        }

        $this->redirectToView();
    }

    /**
     * retrieve one row
     * @return array
     */
    public function getRowValues()
    {

        $returnArray = array();
        //if there was an error submitting the form, the values are saved in the session
        if (isset($_SESSION['BasicTableFormData'][$this->bID]['inputValues'])) {

            foreach ($_SESSION['BasicTableFormData'][$this->bID]['inputValues'] as $key => $value) {
                if (isset($this->postFieldMap[$key])) {
                    $returnArray[$this->postFieldMap[$key]] = $value;
                }
            }

        } else {


            try {
                $model = BaseEntityRepository::getEntityById(get_class($this->getModel()), $this->editKey);
                $model = self::setModelFieldTypes($model);
                if ($model) {
                    $returnArray = $model->getAsAssoc();
                } else {

                    //dummy function because we have no values
                    throw new \Exception();
                }
            } catch (\Exception $e) {
                foreach ($this->getFields() as $key => $value) {
                    if ($key == 'id') {
                    } else {
                        $returnArray[$key] = null;
                    }
                }
            }
        }
        return $returnArray;
    }

    /**
     * Doctrine instanciates Fields without calling the constructor.
     * so the Field Types have to be set afterwards.
     * here the standard way. If you want to add special field types,
     * use model->setFieldType('id' => new Field("id", "ID", "nr"));
     * $model->__construct sets the default field types defined in the model
     * @param BaseEntity $model
     * @return BaseEntity
     */
    public static function setModelFieldTypes(BaseEntity $model)
    {
        $model->setDefaultFieldTypes();
        return $model;
    }

    public function deleteRow()
    {
        $model = $this->getEntityManager()->getRepository(get_class($this->model))
                      ->findOneBy(array($this->model->getIdFieldName() => $this->editKey))
        ;
        $this->getEntityManager()->remove($model);
        $this->getEntityManager()->flush();
        $r = true;
        $_SESSION[$this->getHTMLId()]['prepareFormEdit'] = false;
        if (isset($_SESSION[$this->getHTMLId() . "rowid"])) {
            unset($_SESSION[$this->getHTMLId() . "rowid"]);

        }
        $this->editKey = null;

        if ($r) {
            return true;
        } else {
            return false;
        }
    }

    public function redirectToView()
    {
        if ($this->post('rcID')) {
            // we pass the rcID through the form so we can deal with stacks
            $c = Page::getByID($this->post('rcID'));
        } else {
            $c = $this->getCollectionObject();
        }
        $this->redirectToPage($c);
    }

    /**
     * check if block is in form view or not
     * @return bool
     */
    function displayForm()
    {
        return $this->isFormview;
    }

    function duplicate($newBID)
    {


        return parent::duplicate($newBID);

    }

    public function uninstall()
    {

    }

    /**
     * list needed javascript/css files
     */
    public function on_start()
    {
        $package = Package::getByHandle("basic_table_package");
        $al = \Concrete\Core\Asset\AssetList::getInstance();

        $al->register(
            'javascript', 'typeahead', 'blocks/basic_table_block_packaged/js/typeahead.bundle.js',
            array('minify' => false, 'combine' => true)
            , $package
        );
        $al->register(
            'javascript', 'datepicker', 'blocks/basic_table_block_packaged/js/bootstrap-datepicker.js',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'javascript', 'tagsinput', 'blocks/basic_table_block_packaged/js/bootstrap-tagsinput.js',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'javascript', 'bootgrid', 'blocks/basic_table_block_packaged/js/jquery.bootgrid.min.js',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'javascript', 'block_auto_js', 'blocks/basic_table_block_packaged/auto.js',
            array('minify' => false, 'combine' => true)
            , $package
        );
        $al->register(
            'javascript', 'block_auto_js',
            'blocks/basic_table_block_packaged/js/DirectEditAssociatedEntityMultipleField.js',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'javascript', 'parsley', 'blocks/basic_table_block_packaged/js/parsley.min.js',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'javascript', 'clientSideValidation', 'blocks/basic_table_block_packaged/js/clientSideValidation.js',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'css', 'fontawesome', 'font-awesome', array(
                array('css', 'css/font-awesome.css', array('minify' => false)),
            )
        );


        $al->register(
            'css', 'tagsinputcss', 'blocks/basic_table_block_packaged/css/bootstrap-tagsinput.css',
            array('minify' => false, 'combine' => true)
            , $package
        );
        $al->register(
            'css', 'typeaheadcss', 'blocks/basic_table_block_packaged/css/typeahead.css',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'css', 'datepickercss', 'blocks/basic_table_block_packaged/css/datepicker.css',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'css', 'bootgridcss', 'blocks/basic_table_block_packaged/css/jquery.bootgrid.min.css',
            array('minify' => false, 'combine' => true)
            , $package
        );

        $al->register(
            'css', 'basicTablecss', 'blocks/basic_table_block_packaged/view.css',
            array('minify' => false, 'combine' => true)
            , $package
        );

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

        if ($this->isClientSideValidationActivated()) {
            $groupAssets[] = array("javascript", "parsley");
            $groupAssets[] = array("javascript", "clientSideValidation");
        }

        $al->registerGroup('basictable', $groupAssets);

    }

    /**
     * @return boolean
     */
    public function isClientSideValidationActivated()
    {
        return $this->clientSideValidationActivated;
    }

    /**
     * @param boolean $clientSideActivated
     * @return $this
     */
    public function setClientSideValidationActivated($clientSideActivated)
    {
        $this->clientSideValidationActivated = $clientSideActivated;
        return $this;
    }

    /**
     * register needed javascript
     */
    public function registerViewAssets($outputContent = '')
    {
        $this->requireAsset('basictable');
    }

    /**
     * register also for add form
     */
    public function add()
    {
        $this->requireAsset('basictable');
    }

    /**
     * @param $args
     * save the added/edited block (not the row)
     */
    function save($args)
    {

        parent::save($args);


        $this->getBasicTableInstance();


        $toPersist = array();


        if (count($args) > 0) {

            $blockOptionPostMap = $this->generateOptionsPostFieldMap();
            foreach ($args as $key => $value) {


                //if there are required options
                if (count($this->getBlockOptions()) > 0) {

                    //if the key is in the postmap
                    if (isset($blockOptionPostMap[$key])) {
                        //if the option is already linked to this instance
                        if ($blockOptionPostMap[$key]->BasicTableInstance != null) {

                        } else {
                            //if not, link with instance
                            $blockOptionPostMap[$key]->set("BasicTableInstance", $this->basicTableInstance);
                            $this->basicTableInstance->addBlockOption($blockOptionPostMap[$key]);
                        }
                        $blockOptionPostMap[$key]->getFieldType()->validatePost($value);
                        $optionValue = $blockOptionPostMap[$key]->getFieldType()->getSQLValue();
                        $blockOptionPostMap[$key]->setValue(
                            $blockOptionPostMap[$key]->getFieldType()->getSQLValue()
                        );
                        $toPersist[] = $blockOptionPostMap[$key];

                    }

                }
            }
        }

        if (count($toPersist) > 0) {
            foreach ($toPersist as $num => $blockOption) {
                $blockOption->set('optionType', get_class($blockOption));
                $this->getEntityManager()->persist($blockOption);
            }
        }
        $this->getEntityManager()->persist($this->basicTableInstance);
        $this->getEntityManager()->flush();

    }

    /**
     * generates the blockOptionsPostMap
     * "postkey"=>instance of \Conrete\Package\BasicTablePackage\BlockOptions\TableBlockOption
     * @return array
     */
    protected function generateOptionsPostFieldMap()
    {
        $blockOptions = $this->getBlockOptions();

        $blockOptionsPostMap = array();
        foreach ($blockOptions as $optionnum => $option) {
            $blockOptionsPostMap[$option->getFieldType()->getPostName()] = $option;
        }

        return $blockOptionsPostMap;
    }

    /**
     * if no options are set, it returns the $this->requiredoptions array. Else it returns the merge of $this->requiredoptions and the TableBlockOptions of the basicTableInstance
     * @return array of instance of \Conrete\Package\BasicTablePackage\BlockOptions\TableBlockOption
     */
    public function getBlockOptions()
    {

        if ($this->bID == null) {
            return $this->requiredOptions;
        }

        $this->getBasicTableInstance();
        $currentBlockOptions = $this->basicTableInstance->get('tableBlockOptions');


        if (count($currentBlockOptions) == 0) {
            return $this->requiredOptions;
        } else {

            foreach ($this->requiredOptions as $optionNum => $requOption) {
                foreach ($currentBlockOptions->toArray() as $currentBlockOption) {
                    if ($currentBlockOption->optionName == $requOption->optionName) {
                        $currentBlockOption->setPossibleValues($requOption->getPossibleValues());
                        //load the right optiontype
                        if (get_class($currentBlockOption) == get_class($requOption)) {

                        } else {
                            //load the right current option
                            $realBlockOption = BaseEntityRepository::getEntityById(get_class($requOption),
                                $currentBlockOption->getId());

                            if ($realBlockOption != null) {
                                $currentBlockOption = $realBlockOption;
                            }
                        }


                        $this->requiredOptions[$optionNum] = $currentBlockOption;
                    }
                }
            }
            return $this->requiredOptions;
        }

    }

    /**
     * funciton to retrieve the table data
     * @return array
     */
    public function displayTable()
    {
        $modelList = BaseEntityRepository::getBuildQueryWithJoinedAssociations(get_class($this->getModel()))
                                         ->getQuery()->getResult()
        ;

        $tabledata = array();
        foreach ($modelList as $modelNum => $model) {
            /**
             * @var BaseEntity $model
             */
            $model = static::setModelFieldTypes($model);
            $tabledata[] = $model->getAsAssoc();
        }

        return $tabledata;
    }

    function getErrorMessages()
    {
        return $this->errorMsg;
    }

    /**
     * test of ajax functionality, exit() is important
     */
    function action_myAction()
    {
        echo json_encode(array('hi' => 'test'));
        exit();
    }

    function action_exportCSV()
    {
        //first get table data
        $query = BaseEntityRepository::getBuildQueryWithJoinedAssociations(get_class($this->getModel()));
        $result = $query->getQuery()->execute();
        //convert it to assoc array

        $assocResult = array();
        $fieldTypes = array();
        if (count($result) > 0) {
            foreach ($result as $key => $value) {
                /**
                 * @var BaseEntity $value
                 */
                $value = BaseEntityRepository::getBaseEntityFromProxy($value);

                if (count($assocResult) == 0) {
                    $fieldTypes = $value->getFieldTypes();
                }
                $assocRow = array();
                foreach ($fieldTypes as $num => $fieldType) {
                    if ($fieldType->getSQLFieldName() != $value->getIdFieldName()) {
                        /**
                         * @var Field $fieldType
                         */
                        $assocRow[$fieldType->getLabel()] =
                            $fieldType->setSQLValue($value->get($fieldType->getSQLFieldName()))->getTableView();
                    }
                }
                $assocResult[] = $assocRow;

            }
        } else {
            //if table is empty, export field names
            $fieldTypes = $this->getModel()->getFieldTypes();
            $fieldLabels = array();
            foreach ($fieldTypes as $num => $fieldType) {
                $assocResult[0][] = $fieldType->getLabel();
            }
        }

        $response = new CsvResponse($assocResult);
        $response->setDelimiter(";");
        $response->send();
        exit();

    }

    public function action_importCSV()
    {


        $goBackToTableView = false;
        $block = Block::getByID($this->bID);
        $checker = new Checker($block);

        /**
         * @var BlockResponse $response
         */
        $response = $checker->getResponseObject();

        if (!$response->canRead()) {
            $this->requiresRegistration();
            // The current user can view the block
            $this->setViewVar("errorMessage", t("You are not allowed to perform this operation."));
            $this->render("view");
            return;
        }
        $error = \Concrete\Core\File\Importer::E_PHP_FILE_ERROR_DEFAULT;
        if (isset($_FILES['csvfile']) && is_uploaded_file($_FILES['csvfile']['tmp_name'])) {
            $validator = Core::make('helper/file');
            if ('csv' != $validator->getExtension($_FILES['csvfile']['name'])) {
                $error = \Concrete\Core\File\Importer::E_FILE_INVALID_EXTENSION;
            } else {
                //needed because block is executed twice
                if ($this->isExecuted()) {

                } else {
                    $debugMessage = "";
                    $this->setExecuted();

                    $file = $_FILES['csvfile']['tmp_name'];
                    $filename = $_FILES['csvfile']['name'];
                    $importer = new \Concrete\Core\File\Importer();
                    $result = $importer->import($file, $filename);
                    if ($result instanceof \Concrete\Core\File\Version) {

                        $set = Set::getByName("Imported Files");
                        if (is_null($set)) {
                            $set = Set::createAndGetSet("Imported Files", Set::TYPE_PUBLIC);
                        }
                        $set->addFileToSet($result->getFile());


                        $fileObject = new \SplFileObject("php://memory", "w+");
                        $fileObject->fwrite($result->getFileContents());
                        $reader = new CsvReader($fileObject, ';', '"', "\\");
                        $reader->setHeaderRowNumber(0);

                        $comparer = new EntityDataComparer($this->getModel(), $reader);
                        $comparer->compare();

                        $this->setViewVar("comparisondata", $comparer->getComparisonData());
                        $token = Core::make("token")->generate("persistImport");
                        $this->setViewVar("token", $token);
                        //now serialise the data in the session
                        $app = Application::getFacadeApplication();

                        /**
                         * @var Session $session
                         */
                        $session = $app['session'];


                        $serializeddata = array();
                        if (count($comparer->getComparisonData()) > 0) {
                            foreach ($comparer->getComparisonData() as $num => $comparisonSet) {
                                $serializer = new BaseEntitySerializer($comparisonSet->getResultModel());
                                $serializeddata[$num] = $serializer->convertTo(
                                    BaseEntitySerializer::KEYTYPE_SQL_FIELDNAME
                                    , BaseEntitySerializer::VALUE_TYPE_SQL
                                    , true);
                            }
                        } else {
                            $goBackToTableView = true;
                            $this->setViewVar('errorMessage', t("Imported data and current data are the same."));
                        }

                        $sessiondata[$token] = $serializeddata;
                        $session->set("import", $sessiondata);


                    } else {
                        $error = $result;
                        $this->setViewVar('errorMessage', $importer->getErrorMessage($error));
                    }
                }
                if ($goBackToTableView) {
                    $this->render('view');
                } else {
                    $this->render('../../../basic_table_package/blocks/basic_table_block_packaged/views/import_view');

                }
                return;
            }
        } else if (isset($_FILES['csvfile'])) {
            $error = $_FILES['csvfile']['error'];
        }


        $this->render('view');
        return;

    }

    public function setViewVar($key, $value)
    {
        static::$viewvars[$key] = $value;
        return $this;
    }

    /**
     * @return bool
     */
    public function isExecuted()
    {
        return static::$executed;
    }

    /**
     * sets the block to executed status
     */
    public function setExecuted()
    {
        static::$executed = true;
    }

    public function action_persistImport()
    {

        if (static::isExecuted()) {
            return;
        }
        static::setExecuted();
        //first get request
        /**
         * @var Request $request
         */
        $validator = \Core::make("token");

        //validate token
        if (!$validator->validate("persistImport")) {
            $this->setViewVar("errorMessage", t("Your request is invalid due to inactivity, please retry."));

            //if token not valid, cancel
            $this->render("view");
            $this->redirectToView();
            return;
        }

        $postdata = Request::post();
        $token = $postdata['ccm_token'];

        $session = Core::make("session");


        $importDataWithtoken = $session->get("import");
        $importData = $importDataWithtoken[$token];
        if (count($importData) == 0) {
            $this->setViewVar("errorMessage", t("Nothing to import."));

            //if token not valid, cancel
            $this->render("view");
            $this->redirectToView();
            return;
        }

        $baseEntityFactory = new BaseEntityFactory($this->getModel());
        $this->getEntityManager()->beginTransaction();


        foreach ($postdata['acceptImport'] as $num => $accepted) {
            if ($accepted) {
                if (count($importData[$num]) > 0) {
                    $model = $baseEntityFactory->createFromSQLFieldNameSQLValue($importData[$num], true);
                    if ($model->getId() != null) {
                        $this->getEntityManager()->merge($model);
                    } else {
                        $this->getEntityManager()->persist($model);
                    }
                }
            }
        }
        try {
            $this->getEntityManager()->flush();
            $this->getEntityManager()->commit();
        } catch (Exception $e) {
            $this->getEntityManager()->rollback();
            Core::make("log")->log("ERROR", get_class($e) . " " . $e->getMessage() . " " . __FILE__ . " " . __LINE__);
            $this->setViewVar("errorMessage", t("Something went wrong with the import."));
        }

        $this->render("view");
        $this->redirectToView();
        return;
    }

    function getHeader()
    {
        return $this->header;
    }

    /**
     * @return \Concrete\Package\BasicTablePackage\Src\FieldTypes\Field[]
     */
    public function getErrorFields()
    {
        return $this->errorFields;
    }

    public function install($path)
    {
        $res = parent::install($path);
        //iterate through associations of model to fill association cache ?
    }

    public function action_get_options_of_field()
    {

        $field = $this->request->query->get('fieldname');


        $fieldTypes = $this->getFields();
        /**
         * @var DropdownLinkField $fieldType
         */
        $fieldType = $fieldTypes[$this->postFieldMap[$field]];

        $options = array();
        if ($fieldType instanceof DirectEditInterface) {
            $options = $fieldType->getFullOptions();
            //look that it is an array in javascript
            $options = array_values($options);

        } else {
            throw new \InvalidArgumentException("Invalid field name");
        }
        return new \Symfony\Component\HttpFoundation\JsonResponse($options);
    }

    public function getTableControlButtons($view)
    {
        return '
        <div class="tablecontrols">
            <form method="post" action="' . $view->action('add_new_row_form') . ' ">
    
                        
                        <button type="submit" value="" class="btn inlinebtn actionbutton add" aria-label="'
               . t("new Entry") . '" title="' . t("new Entry") . '">
                                    <i class ="fa fa-plus" aria-hidden="true"> </i>
                         </button>
                         <a href="' . $view->action('exportCSV') . '" >
                            <button type="button" value=""  class="btn inlinebtn actionbutton exportcsv" aria-label="'
               . t("export CSV") . '" title="' . t("export CSV") . '">
                                    <i class ="fa fa-download" aria-hidden="true"> </i>
                            </button>
                        </a>
                        <div class="csvimport">
                            <button type="button" value=""  class="btn inlinebtn actionbutton importcsv" aria-label="'
               . t("import CSV") . '" title="' . t("import CSV") . '">
                                    <i class ="fa fa-upload" aria-hidden="true"> </i>
                            </button>
                            
                         </div>
                        
            </form>
            <div class="hidden">
                        <form method="post" action="' . $view->action('importCSV') . ' " class="importcsv-form" enctype="multipart/form-data">
                                <input type="file" name="csvfile" class="csvfile-field"/>
                        </form>      
             </div>
           </div>  
        
        ';
    }

    public function getViewVar($key)
    {
        return static::$viewvars[$key];
    }

    public function getViewVars()
    {
        return static::$viewvars;
    }


}
