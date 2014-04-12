<?php

class RealEstate_Document_RealEstateProposal extends Lis_Document_Abstract {

    const PROPOSAL_TYPE_BUYING = 1;
    const PROPOSAL_TYPE_SELLING = 2;
    const PROPOSAL_TYPE_EXCHANGE = 3;
    const PROPOSAL_TYPE_RENTAL = 4;

    public static function getProposalTypes()
    {
        return array(self::PROPOSAL_TYPE_BUYING, self::PROPOSAL_TYPE_SELLING,
            self::PROPOSAL_TYPE_EXCHANGE, self::PROPOSAL_TYPE_RENTAL);
    }

    public function __get($key)
    {
        if ($key == 'COMPLEX_AREA') {
            $areas = array();
            if ($this->TOTAL_AREA !== null
                    && $this->LIVING_AREA !== null
                    && $this->KITCHEN_AREA !== null) {
                $areas = array($this->TOTAL_AREA, $this->LIVING_AREA, $this->KITCHEN_AREA);
            }
            return implode('/', $areas);
        } else if ($key == 'STOREY_INFO') {
            $storeyInfo = array();
            if ($this->STOREY_NUMBER !== null && $this->STOREY_COUNT !== null) {
                $storeyInfo = array($this->STOREY_NUMBER, $this->STOREY_COUNT);
            }
            return implode('/', $storeyInfo);
        } elseif ($key == 'BALCONIES_AND_LOGGIAS_COUNT') {
            $data = array();
            if ($this->BALCONIES_COUNT !== null && $this->LOGGIAS_COUNT !== null) {
                $data = array($this->BALCONIES_COUNT, $this->LOGGIAS_COUNT);
            }
            return implode('/', $data);
        }
        return parent::__get($key);
    }

    public function __set($key, $value)
    {

        if ($key == 'COMPLEX_AREA') {
            if (preg_match('/^[0-9]+\/[0-9]+\/[0-9]+$/', $value)) {
                $areas = explode('/', $value);
                list ($totalArea, $livingArea, $kitchenArea) = $areas;
                $this->TOTAL_AREA = $totalArea;
                $this->LIVING_AREA = $livingArea;
                $this->KITCHEN_AREA = $kitchenArea;
            }
        } elseif ($key == 'STOREY_INFO') {
            if (preg_match('/^[0-9]+\/[0-9]+$/', $value)) {
                $storeyInfo = explode('/', $value);
                list ($storeyNumber, $storeyCount) = $storeyInfo;
                $this->STOREY_NUMBER = $storeyNumber;
                $this->STOREY_COUNT = $storeyCount;
            }
        } elseif ($key == 'BALCONIES_AND_LOGGIAS_COUNT') {
            if (preg_match('/^[0-9]+\/[0-9]+$/', $value)) {
                list($balconiesCount, $loggiasCount) = explode('/', $value);
                $this->BALCONIES_COUNT = $balconiesCount;
                $this->LOGGIAS_COUNT = $loggiasCount;
            }
        } else {
            parent::__set($key, $value);
        }
    }

}