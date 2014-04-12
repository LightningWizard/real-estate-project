<?php

class RealEstate_Grid_Reflector_ContragentType extends Lis_Grid_Reflector_Translate {
    public function execute($value) {
        switch($value) {
            case RealEstate_Document_Contragent::CONTRAGENT_TYPE_SELLER:
                $value = 'Seller';
                break;
            case RealEstate_Document_Contragent::CONTRAGENT_TYPE_AGENCY:
                $value = 'RealEstateAgency';
                break;
            case RealEstate_Document_Contragent::CONTRAGENT_TYPE_REALTOR:
                $value = 'Realtor';
                break;
            case RealEstate_Document_Contragent::CONTRAGENT_TYPE_BUILDER:
                $value = 'Builder';
                break;
        }
        return parent::execute($value);
    }
}