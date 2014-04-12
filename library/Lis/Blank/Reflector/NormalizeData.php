<?php
class Lis_Blank_Reflector_NormalizeData extends Lis_Blank_Reflector_Abstract
{
    protected $_locale = null;
    protected $_filters = array(
        'b2d' => 'Zend_Filter_LocalizedToNormalized',
        'd2b' => 'Zend_Filter_NormalizedToLocalized',
    );

    public function __construct(Lis_Blank_Abstract $blank, $blankFieldName, Lis_Document_Abstract $document, $documentFieldName, $locale = null)
    {
        parent::__construct($blank, $blankFieldName, $document, $documentFieldName);
        $this->setLocale($locale);
    }
    public function setLocale($locale = null)
    {
        require_once 'Zend/Locale.php';
        $this->_locale = Zend_Locale::findLocale($locale);
        return $this;
    }
    public function getLocale()
    {
        return $this->_locale;
    }
    protected function _getFilter($direction = 'all')
    {
        if (!isset($this->_filters[$direction])) {
            throw new Lis_Blank_Reflecytor_Exception('Try to use undeclared filter!');
        }
        if (is_string($this->_filters[$direction])) {
            try {
                $this->_filters[$direction] = new $this->_filters[$direction]();
            } catch (Exception $e) {
                throw new Lis_Blank_Reflecytor_Exception('Ca not instantiate class of filter for blank reflector. ' . $e->getMessage());
            }
        }

        if ($this->_filters[$direction] instanceof Zend_Filter_Interface) {
            return $this->_filters[$direction];
        } else {
            throw new Lis_Blank_Reflecytor_Exception('Fatal error in declaration of filters in blank refelctor. Try to use not filter.');
        }
    }
    protected function _b2d($value)
    {
        return $this->_doFilter($value, $this->_getFilter('b2d'));
    }
    protected function _d2b($value)
    {
        return $this->_doFilter($value, $this->_getFilter('d2b'));
    }
    protected function _doFilter($value, Zend_Filter_Interface $filter)
    {
        $filter->setOptions(array('locale'=>$this->_locale, 'date_format'=>'-'));
        return $filter->filter($value);
    }
}