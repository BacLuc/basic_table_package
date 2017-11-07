<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Core\Form\Service\Widget\DateTime;
use Concrete\Core\Localization\Service\Date;
use Concrete\Package\BasicTablePackage\Src\FieldTypes\Field as Field;
use Punic\Exception;

class DateField extends Field
{
    public static $userFormat         = 'd.m.Y';
    public static $currentFormat      = array(
        'phpdatetime'         => 'Y-m-d',
        'regex'               => '\d{4}-\d{2}-\d{2}',
        'bootstrapdatepicker' => 'yyyy-mm-dd',
    );
    public static $seperator          = '.';
    public static $formatErrorMessage = 'Bitte geben Sie ein gültiges Datum im Format d.m.Y an, also z.b. 23.01.2015';
    protected     $sqlFieldname;
    protected     $label;
    protected     $validation;


    /*TODO make userFormat configurable*/
    protected $value;
    protected $postName;
    protected $errMsg = "";
    protected $nullable = true;

    /**
     * @param $sqlFieldname
     * @param $label
     * @param $postName
     */
    public function __construct($sqlFieldname, $label, $postName)
    {
        parent::__construct($sqlFieldname, $label, $postName);
        $this->sqlFieldname = $sqlFieldname;
        $this->label = $label;
        $this->postName = $postName;
        static::detectDateFormat();
    }


    public final static function detectDateFormat()
    {
        $date = new Date();
        $format = $date->formatDate(new \DateTime('2016-01-31'), false);

        //first detect seperator
        if (count(explode("/", $format)) == 3) {
            static::$seperator = '/';
        } elseif (count(explode(".", $format)) == 3) {
            static::$seperator = '.';
        } elseif (count(explode("-", $format)) == 3) {
            static::$seperator = '-';
        } else {
            throw new \Exception("Seperator of Date format could not be detected");
        }

        //now detect format
        $parts = explode(static::$seperator, $format);
        $supportedFormats = static::getSupportedFormats();
        if (intval($parts[0]) == 1 && intval($parts[1]) == 31 && intval($parts[2]) == 16) {
            static::$currentFormat = $supportedFormats['us'];
        } else {
            static::$currentFormat = $supportedFormats['other'];
        }
    }

    protected static function getSupportedFormats()
    {
        return array(
            'us'    => array(
                'phpdatetime'         => 'm' . static::$seperator . 'd' . static::$seperator . 'Y',
                'regex'               => '\d{2}' . static::$seperator . '\d{2}' . static::$seperator
                                         . '\d{4}',
                'bootstrapdatepicker' => 'mm' . static::$seperator . 'dd' . static::$seperator . 'yyyy',
            ),
            'other' => array(
                'phpdatetime'         => 'd' . static::$seperator . 'm' . static::$seperator . 'Y',
                'regex'               => '\d{2}' . static::$seperator . '\d{2}' . static::$seperator
                                         . '\d{4}',
                'bootstrapdatepicker' => 'dd' . static::$seperator . 'mm' . static::$seperator . 'yyyy',
            ),
        );
    }

    /**
     *
     * @return boolean
     */
    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * set if the col is nullable
     * @param Boolean $nullable
     */
    public function setNullable($nullable = true)
    {
        $this->nullable = $nullable;
        return $this;
    }

    public function setValue($value)
    {
        //var_dump($value);
        if ($value == '' OR $value == null) {
            $this->value = null;
            return;
        }

        $explodeValue = explode(static::$seperator, $value);


        //check if Y m d
        if (strlen($explodeValue[0]) <= 4
            && strlen($explodeValue[0]) >= 2
            && is_numeric($explodeValue[1])
            && $explodeValue[1] + 0 <= 12
            && $explodeValue[1] + 0 >= 1
            && is_numeric($explodeValue[2])
            && $explodeValue[2] + 0 <= 31
            && $explodeValue[2] + 0 >= 1
        ) {
            try {
                $this->value = (
                new \DateTime($explodeValue[0] . "-" . $explodeValue[1] . "-" . $explodeValue[2])
                )->format('Y-m-d');
            } catch (Exception $e) {

            }
            //check if d m Y
        } elseif (strlen($explodeValue[2]) <= 4
                  && strlen($explodeValue[2]) >= 2
                  && is_numeric($explodeValue[2])
                  && is_numeric($explodeValue[1])
                  && $explodeValue[1] + 0 <= 12
                  && $explodeValue[1] + 0 >= 1
                  && is_numeric($explodeValue[0])
                  && $explodeValue[0] + 0 <= 31
                  && $explodeValue[0] + 0 >= 1
        ) {
            try {
                $this->value = (
                new \DateTime($explodeValue[2] . "-" . $explodeValue[1] . "-" . $explodeValue[0])
                )->format('Y-m-d');
            } catch (Exception $e) {

            }
            //check if m d Y
        } elseif (strlen($explodeValue[2]) <= 4
                  && strlen($explodeValue[2]) >= 2
                  && is_numeric($explodeValue[2])
                  && is_numeric($explodeValue[0])
                  && $explodeValue[0] + 0 <= 12
                  && $explodeValue[0] + 0 >= 1
                  && is_numeric($explodeValue[1])
                  && $explodeValue[1] + 0 <= 31
                  && $explodeValue[1] + 0 >= 1
        ) {
            try {
                $this->value = (
                new \DateTime($explodeValue[2] . "-" . $explodeValue[0] . "-" . $explodeValue[1])
                )->format('Y-m-d');
            } catch (Exception $e) {

            }
        }


        $this->setSQLValue($this->value);
        return $this;
    }

    public function setSQLValue($value)
    {
        //for now, save the value as Y-m-d, later change to save as DateTime
        if (!($value instanceof \DateTime) && !is_null($value)) {
            throw new \InvalidArgumentException('$value has to be instance of \DateTime, was ' . $value);
        }
        $this->value = $value;
        return $this;
    }

    public function getFormView($form, $clientSideValidationActivated = true)
    {
        $returnString = $this->getLabelHtml();
        $returnString .= $this->getInputHtml($form, $clientSideValidationActivated);
        return $returnString;
    }

    /**
     * @return string
     */
    public function getLabelHtml()
    {
        $returnString = "<label for='" . $this->getHtmlId() . "'>" . $this->getLabel() . "</label>";
        return $returnString;
    }

    /**
     * @param $clientSideValidationActivated
     * @param $returnString
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated)
    {
        $returnString = '';
        $validationAttributes = array();
        if ($clientSideValidationActivated) {
            $validationAttributes = $this->addValidationAttributes(array());
        }
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        //set value to default before displaying the form
        if ($value == null && $default != null) {
            $this->setSQLValue($default);
        }
        $returnString .= '
				<div  class="input-append date datepickerdiv" data-date-format="'
                         . static::$currentFormat['bootstrapdatepicker'] . '" data-date="' . $this->getTableView() . '">
				<input id="' . $this->getHtmlId() . '" name="' . $this->getPostName() . '"  type="text" value="'
                         . $this->getTableView() . '" size="16"
				';
        //then set it back
        $this->setSQLValue($value);
        if (count($validationAttributes) > 0) {
            foreach ($validationAttributes as $key => $value) {
                $returnString .= " $key = \"$value\" ";
            }
        }

        $returnString .= '	>

				</div>
				<script>
					$(function(e){
						$("#' . $this->getHtmlId() . '").datepicker({format:"'
                         . static::$currentFormat['bootstrapdatepicker'] . '"});

					});

				</script>
				';
        $returnString .= $this->getHtmlErrorMsg();
        return $returnString;
    }

    public function addValidationAttributes($attributes)
    {
        $attributes = parent::addValidationAttributes($attributes); // TODO: Change the autogenerated stub
        //$attributes['t']
        $attributes['data-parsley-datestring'] = static::$currentFormat['regex'];
        return $attributes;
    }

    public function getTableView()
    {
        if ($this->getSQLValue() == null) {
            return null;
        }
        //var_dump($this->getValue());
        //return $this->getValue();
        /**
         * @var DateTime $this ->value
         */
        return $this->value->format(static::$currentFormat['phpdatetime']);
    }

    public function validatePost($value)
    {
        if (is_null($value) || $value == '') {
            if ($this->nullable) {
                $this->value = null;
                return true;
            } else {
                $this->errMsg = $this->getLabel() . t(static::NULLERRORMSG);
                return false;
            }
        }
        $explodePattern = explode(static::$seperator, static::$currentFormat['phpdatetime']);

        $explodeValue = explode(static::$seperator, $value);

        if (count($explodeValue) > 3) {
            $this->errMsg = static::$formatErrorMessage;
            return false;
        }
        $day = null;
        $month = null;
        $year = null;
        for ($i = 0; $i < count($explodeValue); $i++) {
            //first remove leading zeroes
            $explodeValue[$i] = ltrim($explodeValue[$i], '0');

            if (false === filter_var($explodeValue[$i], FILTER_VALIDATE_INT) || $explodeValue[$i] <= 0) {
                $this->errMsg = t(static::$formatErrorMessage);
                return false;
            }
            $explodeValue[$i] = intval($explodeValue[$i]);
            //prepend zero if only one digit
            $setValue = strlen($explodeValue[$i]) == 1 ? "0" . $explodeValue[$i] : $explodeValue[$i];
            switch ($explodePattern[$i]) {
                case 'd':
                    if (($explodeValue[$i] > 31)) {
                        $this->errMsg = t(static::$formatErrorMessage);
                        return false;
                    }
                    $day = $setValue;
                    break;
                case 'm':
                    if ($explodeValue[$i] > 12) {
                        $this->errMsg = t(static::$formatErrorMessage);
                        return false;
                    }
                    $month = $setValue;
                    break;
                case 'Y':
                    if (!(is_numeric($explodeValue[$i] + 0)
                          && strlen($explodeValue[$i]) <= 4
                    )
                    ) {
                        $this->errMsg = t(static::$formatErrorMessage);
                        return false;
                    }
                    $year = $setValue;
                    break;

            }
        }
        $this->value = new \DateTime($year . "-" . $month . "-" . $day);
        return true;
    }


}
