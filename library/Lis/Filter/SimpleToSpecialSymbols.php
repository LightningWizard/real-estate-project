<?php
require_once 'Zend/Filter/Interface.php';
require_once 'Zend/Locale/Data.php';

/**
 * Encrypts a given string
 *
 * Some filters, provided by Zend Framework use special non typing symbols like non-breaking space
 * so this filter convert symple to typeing symbols into this special symbols
 *
 * @category   Lis
 * @package    Lis_Filter
 * @copyright  Copyright (c) 2010 Laboratory of Inforantion Systems Ltd. (http://www.lissoft.com.ua)
 * @license    this is apprpriated software, you can not use them
 */
class Lis_Filter_SimpleToSpecialSymbols implements Zend_Filter_Interface
{
    /**
     * Set options
     */
    protected $_options = array(
        'locale'      => null,
    );

    /**
     * Class constructor
     *
     * @param string|Zend_Locale $locale (Optional) Locale to set
     */
    public function __construct($options = null)
    {
        if (null !== $options) {
            $this->setOptions($options);
        }
    }

    /**
     * Returns the set options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->_options;
    }

    /**
     * Sets options to use
     *
     * @param  array $options (Optional) Options to use
     * @return Zend_Filter_LocalizedToNormalized
     */
    public function setOptions(array $options = null)
    {
        $this->_options = $options + $this->_options;
        if (empty($this->_options['locale']) && Zend_Registry::isRegistered('Zend_Locale')) {
            $this->_options['locale'] = Zend_Registry::get('Zend_Locale');
        }
        return $this;
    }

    /**
     * Defined by Zend_Filter_Interface
     *
     * Normalizes the given input
     *
     * @param  string $value Value to normalized
     * @return string
     */
    public function filter($value)
    {
        if (!is_string($value)) {
            return $value;
        }

        $localeSymbols = Zend_Locale_Data::getList($this->_options['locale'], 'symbols');

        if (isset($localeSymbols['group']) && $localeSymbols['group'] === ' ') {
            //if group symbols is non-breaking space then replace simple spaces to it
            $value = preg_replace('/(\d) (\d)/si', '${1} ${2}', $value);
        }

        return $value;
    }
}