<?php

class RealEstate_Grid_Reflector_RealEstatePurpose extends Lis_Grid_Reflector_Translate {

    public function execute($value) {
        switch ($value) {
            case RealEstate_Document_RealEstateType::PURPOSE_COMMERCIAL:
                $value = 'Commercial';
                break;
            case RealEstate_Document_RealEstateType::PURPOSE_NOTCOMMERCIAL:
                $value = 'NoCommercial';
                break;
        }
        return parent::execute($value);
    }

}
