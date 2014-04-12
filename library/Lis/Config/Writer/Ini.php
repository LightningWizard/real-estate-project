<?php
class Lis_Config_Writer_Ini extends Zend_Config_Writer_Ini
{
    protected function _prepareValue($value)
    {
        if (is_integer($value) || is_float($value)) {
            return $value;
        } elseif (is_bool($value)) {
            return ($value ? 'true' : 'false');
        } else {
            return $value;
        }
    }
}