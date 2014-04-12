<?php

class RealEstate_Grid_Reflector_BoolToString extends Lis_Grid_Reflector_Translate{
    public function execute($value)
    {
        if (null === $value)
            return '';

        if ($value) {
            $value = 'Yes';
        } else {
            $value = 'No';
        }
        return parent::execute($value);
    }
}

?>
