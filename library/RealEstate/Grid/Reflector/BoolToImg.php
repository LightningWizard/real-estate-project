<?php
class RealEstate_Grid_Reflector_BoolToImg implements Lis_Grid_Reflector_Interface
{
    public function execute($value)
    {
        if (null === $value)
            return '';

        $src   = '/img/icons/16x16/';
        $class = 'bool-';
        if ($value) {
            $src .= 'tick.gif';
            $class .= 'true';
        } else {
            $src .= 'cross.gif';
            $class .= 'false';
        }
        return '<img src="' . $src . '" class="' . $class . '" />';
    }
}