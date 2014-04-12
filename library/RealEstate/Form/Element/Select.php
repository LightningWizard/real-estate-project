<?php

class RealEstate_Form_Element_Select extends Zend_Form_Element_Select{

    public function addReflectedOptions(array $options, Lis_Grid_Reflector_Interface $reflector = null) {
        if($reflector !== null) {
            foreach ($options as $value) {
                $this->addMultiOption($value, $reflector->execute($value));
            }
        } else {
            foreach ($options as $value) {
                $this->addMultiOption($value, $value);
            }
        }
        return $this;
    }
}