<?php

namespace Concrete\Package\BasicTablePackage\Src\FieldTypes;

use Concrete\Core\Form\Service\Form;
use Doctrine\ORM\EntityManager;

/**
 * Class Field
 * @IgnoreAnnotation("package")
 *  Concrete\Package\BasicTablePackage\Src\FieldTypes
 * A normal textfield, provides most of the methods for subfieldTypes
 */
class FloatField extends IntegerField
{
    const NOFLOATERRORMSG = " must be an float";
    protected $step = 'any';
    /**
     * @var EntityManager
     */
    protected $em;


    /**
     * @param int $step
     * @return $this
     */
    public function setStep($step)
    {
        if (filter_var($step, FILTER_VALIDATE_FLOAT) === false) {
            throw new \InvalidArgumentException("Step for Float Field has to be Float");
        }
        $this->step = $step;
        return $this;
    }


    public function validatePost($value)
    {
        if ($this->nullable === false && strlen($value) == 0) {
            $this->errMsg = $this->getLabel() . t(static::NULLERRORMSG);
            return false;
        }
        if (strlen($value) == 0) {
            $this->value = null;
            return true;
        }

        if (strpos($value, ",") !== false) {
            if (strpos($value, ".") !== false) {
                //point and comma are in string, so we have to look which one is only once
                $offsetComma = strpos($value, ",");
                $secondComma = strpos($value, ",", $offsetComma + 1);

                $offsetPoint = strpos($value, ".");
                $secondPoint = strpos($value, ".", $offsetPoint + 1);
                if ($secondComma !== false && $secondPoint !== false) {
                    //if multiple points and multiple commas, it is no float
                    $this->errMsg = $this->getLabel() . t(static::NOFLOATERRORMSG);
                    return false;
                } elseif ($secondComma !== false && $secondPoint === false) {
                    //remove commas, the point is the decimal sign
                    $value = str_replace(",", "", $value);
                } elseif ($secondComma === false && $secondPoint !== false) {
                    //remove points, the comma is the decimal sign
                    $value = str_replace(".", "", $value);
                    //replace comma with point, because this is right
                    $value = str_replace(",", ".", $value);
                } elseif ($secondComma === false && $secondPoint === false) {
                    //both appear just once, the last one is the decimal operator
                    if ($offsetComma > $offsetPoint) {
                        //remove the point
                        $value = str_replace(".", "", $value);
                        //replace comma with point, because this is right
                        $value = str_replace(",", ".", $value);
                    } else {
                        //replace comma with nothing, because not needed
                        $value = str_replace(",", "", $value);
                    }
                }
            } else {
                //TODO detect thousend seperation point
                $value = str_replace(",", ".", $value);
            }

        }


        if (filter_var($value, FILTER_VALIDATE_FLOAT) === false) {
            $this->errMsg = $this->getLabel() . t(static::NOFLOATERRORMSG);
            return false;
        }
        $value = filter_var($value, FILTER_VALIDATE_FLOAT);
        if (strlen($this->min) > 0) {
            if ($value < $this->min) {
                $this->errMsg = $this->getLabel() . t(static::MINERRORMSG, $this->min);
                return false;
            }
        }

        if (strlen($this->max) > 0) {
            if ($value > $this->max) {
                $this->errMsg = $this->getLabel() . t(static::MAXERRORMSG, $this->max);
                return false;
            }
        }

        if (strlen($this->step) > 0) {
            if ($this->step != 'any') {

                if (strlen($this->min) > 0) {
                    $min = 0;
                    $min = $this->min;
                    $interval = $value - $min;
                } else {
                    $interval = $value;
                }


                if ($interval % $this->step != 0) {
                    $this->errMsg = $this->getLabel() . t(static::STEPERRORMSG, $this->step);
                    return false;
                }
            }
        }

        $this->value = $value;
        return true;
    }


    /**
     * @param $form
     * @param $clientSideValidationActivated
     * @param $returnString
     * @return string
     */
    public function getInputHtml($form, $clientSideValidationActivated)
    {
        $attributes = array(
            'title' => $this->getPostName(),
            'value' => $this->getSQLValue(),
            'id'    => $this->getHtmlId(),
        );

        if ($clientSideValidationActivated) {
            $attributes = $this->addValidationAttributes($attributes);
        }
        $value = $this->getSQLValue();
        $default = $this->getDefault();
        if ($value == null && $default != null) {
            $value = $default;
        }


        /**
         * @var Form $form
         */
        $returnString =
            static::inputType($this->getHtmlId(), $this->getPostName(), "number", $value, $attributes, $form);
        $returnString .= $this->getHtmlErrorMsg();
        return $returnString;
    }

}
