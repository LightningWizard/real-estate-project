<?php

class RealEstate_Grid_Reflector_ProposalType extends Lis_Grid_Reflector_Translate {
    public function execute($value) {
        switch ($value){
            case RealEstate_Document_RealEstateProposal::PROPOSAL_TYPE_BUYING:
                $value = 'Buying';
                break;
            case RealEstate_Document_RealEstateProposal::PROPOSAL_TYPE_SELLING:
                $value = 'Selling';
                break;
            case RealEstate_Document_RealEstateProposal::PROPOSAL_TYPE_EXCHANGE:
                $value = 'Exchange';
                break;
            case RealEstate_Document_RealEstateProposal::PROPOSAL_TYPE_RENTAL:
                $value = 'Rental';
                break;
            default:
                $value = 'Unknown proposal type';
        }
        return parent::execute($value);
    }
}