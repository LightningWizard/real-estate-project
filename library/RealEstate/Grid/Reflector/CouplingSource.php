<?php

class RealEstate_Grid_Reflector_CouplingSource extends Lis_Grid_Reflector_Translate {
    public function execute($value) {
        if(empty($value)){
            return;
        }
        switch($value) {
            case RealEstate_Document_CouplingUnit::KURER_BUY:
                $value = 'SiteKurerSectionBuy';
                break;
            case RealEstate_Document_CouplingUnit::KURER_SELL:
                $value = 'SiteKurerSectionSell';
                break;
            case RealEstate_Document_CouplingUnit::KURER_EXCHANGE:
                $value = 'SiteKurerSectionExchange';
                break;
            case RealEstate_Document_CouplingUnit::TEXT_FILE:
                $value = 'TextFile';
                break;
        }
        return parent::execute($value);
    }
}