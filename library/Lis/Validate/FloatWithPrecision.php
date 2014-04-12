<?php
class Lis_Validate_FloatWithPrecision extends Zend_Validate_Float
{
    const NOT_FLOATWITHPERCISION = 'notFloatWithPercision';
    protected $_messageTemplates = array(
        
    );
    protected $_messageVariables = array(
        'precision' => '_precision'
    );
    protected $_precision;

    public function __construct($precision, $locale = null)
    {
        parent::__construct($locale);
        $this->setPrecision($precision);
    }

    public function getPrecision()
    {
        return $this->_precision;
    }
    public function setPrecision($precision)
    {
        $this->_precision = $precision;
        return $this;
    }

    public function isValid($value)
    {
        $this->_messageTemplates = array(
            parent::NOT_FLOAT => "'%value%' does not appear to be a float",
        );
        if (!parent::isValid($value)) {
            return false;
        } else {
            $this->_messageTemplates = array(
                self::NOT_FLOATWITHPERCISION => "'%value%' does not appear to be a float or contains more then %precision% after delimiter"
            );
        }

        $valueString = (string) $value;

        $this->_setValue($valueString);
        $delimiter = Zend_Locale_Data::getList($this->_locale, 'symbols');
        $delimiter = $delimiter['decimal'];

        $delimiterPosition = strpos($valueString, $delimiter);

        if (false === $delimiterPosition) {
            return true;
        }
        
        if ($this->_precision < strlen($valueString) - $delimiterPosition - 1) {
            $this->_error();
            return false;
        }
        
        return true;
    }
}