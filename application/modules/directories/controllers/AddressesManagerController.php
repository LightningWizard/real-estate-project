<?php
class Directories_AddressesManagerController extends Zend_Controller_Action {
    public function init() {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            $this->_forward('error', 'error', 'system');
            return;
        }
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
    }

    public function settlementsAction() {
        $addressManager = RealEstate_AddressManager::getInstance();
        $settlements = $addressManager->getSettlements();
        echo Zend_Json::encode($settlements);
    }

    public function geographicalDistrictsAction() {
        $addressManager = RealEstate_AddressManager::getInstance();
        $settlements = $addressManager->getGeographicalDistricts();
        echo Zend_Json::encode($settlements);
    }

    public function settlementStreetsAction() {
        $request = $this->getRequest();
        $settlement = $request->getParam('settlement', null);
        $streets = array();
        if(!empty($settlement)) {
            $addressManager = RealEstate_AddressManager::getInstance();
            $streets = $addressManager->getStreetsForSettlement($settlement);
        }
        echo Zend_Json::encode($streets);
    }

    public function streetTypesAction() {
        $addressManager = RealEstate_AddressManager::getInstance();
        $streetTypes = $addressManager->getStreetTypes();
        echo Zend_Json::encode($streetTypes);
    }
}

