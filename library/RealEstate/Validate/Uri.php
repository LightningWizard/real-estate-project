<?php

class RealEstate_Validate_Uri extends Zend_Validate_Abstract{
    
    const NOT_URI = 'NotUri';
    
    protected $_messageTemplates = array(
        self::NOT_URI => "Invalid URI",
    );

    public function isValid($value) {
        $this->_setValue($value);

        $valid = Zend_Uri::check($value);
       
        if ($valid)  {
            return true;
        } else {
            $this->_error(self::NOT_URI);
            return false;
        }
    }
}