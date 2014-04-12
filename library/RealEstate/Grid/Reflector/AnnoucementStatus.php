<?php

class RealEstate_Grid_Reflector_AnnoucementStatus extends Lis_Grid_Reflector_Translate{

    public function execute($value) {
        if(empty($value)) {
            return '';
        }
        switch ($value){
            case RealEstate_Coupling_Annoucement::FROM_AGENCY:
                $value = 'AnnoucementFromAgency';
                break;
            case RealEstate_Coupling_Annoucement::PHONE_EXIST_CT:
                $value = 'AnnoucementPhonesExistInCouplingTable';
                break;
            case RealEstate_Coupling_Annoucement::PHONE_EXIST_MT:
                $value = 'AnnoucementPhonesExistInMainTable';
                break;
            case RealEstate_Coupling_Annoucement::PHONE_NOT_EXIST:
                $value = 'AnnoucementPhonesNotExistsInDb';
                break;
        }
        return parent::execute($value);
    }
}