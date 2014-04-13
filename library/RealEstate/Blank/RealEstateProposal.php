<?php

class RealEstate_Blank_RealEstateProposal extends Lis_Blank_Abstract {

    public function init(){
        $this->setAttrib('id', 'blank-real-estate-proposal')
             ->setMethod('post');

        $this->addDocument(new RealEstate_Document_RealEstateProposal, 'RealEstateProposal');

        //Дата ввода
        $proposalDate = new ZendX_JQuery_Form_Element_DatePicker('createdDate');
        $proposalDate->setLabel('EnteringDate')
                ->setJQueryParams($this->getDatepickerParams())
                ->setDecorators(array('CompositeElement'));

        //Дата последнего прозвона
        $lastCallingDate = new ZendX_JQuery_Form_Element_DatePicker('lastCallingDate');
        $lastCallingDate->setLabel('LastCallingDate')
                ->setJQueryParams($this->getDatepickerParams())
                ->setDecorators(array('CompositeElement'));

        //Тип операции

        $proposalTypes = RealEstate_Document_RealEstateProposal::getProposalTypes();
        $reflector = new RealEstate_Grid_Reflector_ProposalType();

        $proposalType = new RealEstate_Form_Element_Select('proposalType');
        $proposalType->setLabel('RealEstateProposalType')
                     ->setRequired(true)
                     ->setDecorators(array('CompositeElement'))
                     ->addMultiOption('', '')
                     ->addReflectedOptions($proposalTypes, $reflector);
        unset($reflector);

        //Адрес
        $objectAddress = new Zend_Form_Element_Textarea('objectAddress');
        $objectAddress->setLabel('Address')
                      ->setAttrib('readonly', 'readonly')
                      ->setDecorators(array('CompositeElement'));

        // Идентификатор населенного пункта
        $settlementId = new Zend_Form_Element_Hidden('settlementId');
        $settlementId->setDecorators(array('ViewHelper'));

        // Идентификатор улицы
        $streetId = new Zend_Form_Element_Hidden('streetId');
        $streetId->setDecorators(array('ViewHelper'));

        // № дома
        $buildingNumber = new Zend_Form_Element_Hidden('buildingNumber');
        $buildingNumber->setDecorators(array('ViewHelper'));

        // № квартиры
        $flatNumber = new Zend_Form_Element_Hidden('flatNumber');
        $flatNumber->setDecorators(array('ViewHelper'));

        //Тип недвижимости
        $realEstateTypeId = new Zend_Form_Element_Hidden('realEstateTypeId');
        $realEstateTypeId->setDecorators(array('ViewHelper'));

        $realEstateType = new Zend_Form_Element_Text('realEstateType');
        $realEstateType->setLabel('RealEstateType')
                            ->setRequired(true)
                            ->setAttrib('readonly', 'readonly')
                            ->setDecorators(array('CompositeElement'));

        //Географический район
        $geoDistrictId = new Zend_Form_Element_Hidden('geoDistrictId');
        $geoDistrictId->setDecorators(array('ViewHelper'));

        $geoDistrictTitle = new Zend_Form_Element_Text('geoDistrictTitle');
        $geoDistrictTitle->setLabel('GeographicalDistrict')
                         ->setAttrib('readonly', 'readonly')
                         ->setDecorators(array('CompositeElement'));

        //Тип планировки
        $planningTypeId = new Zend_Form_Element_Hidden('planningTypeId');
        $planningTypeId->setDecorators(array('ViewHelper'));

        $planningType = new Zend_Form_Element_Text('planningType');
        $planningType->setLabel('PlanningType')
                     ->setAttrib('readonly', 'readonly')
                     ->setDecorators(array('CompositeElement'));

        //Обмен на
        $exchangeFor = new Zend_Form_Element_Textarea('exchangeFor');
        $exchangeFor->setLabel('ExchangeFor')
                    ->setDecorators(array('CompositeElement'));

        //Цена
        $price = new Zend_Form_Element_Text('price');
        $price->setLabel('Price')
                        ->setDecorators(array('CompositeElement'));
        // Цена агенства
        $agencyPrice = new Zend_Form_Element_Text('agencyPrice');
        $agencyPrice->setLabel('AgencyPrice')
                    ->setDecorators(array('CompositeElement'));

        // Общая площадь/Жилая площадь
        $complexArea = new Zend_Form_Element_Text('complexArea');
        $complexArea->setLabel('ComplexArea')
//                    ->setDescription('ComplexAreaDescription')
                    ->setDecorators(array('CompositeElement'));

        // Этаж/этажность
        $storeyInfo = new Zend_Form_Element_Text('storeyInfo');
        $storeyInfo->setLabel('StoreyInfo')
                   ->setDecorators(array('CompositeElement'));

        // Площади помещений
        $roomsAreas = new Zend_Form_Element_Text('roomsArea');
        $roomsAreas->setLabel('RoomsAreasU')
//                   ->setDescription('RoomsAreaDescription')
                   ->setDecorators(array('CompositeElement'));

        // Телефоны владельца
        $ownerPhones = new Zend_Form_Element_Textarea('ownerPhones');
        $ownerPhones->setLabel('OwnerPhones')
                    ->setRequired(true)
                    ->setDecorators(array('CompositeElement'));

        // Контрагент
        $contragentId = new Zend_Form_Element_Hidden('contragentId');
        $contragentId->setDecorators(array('ViewHelper'));

        $contragentTitle = new Zend_Form_Element_Text('contragentTitle');
        $contragentTitle->setLabel('RealEstateAgency')
                        ->setAttrib('readonly', 'readonly')
                        ->setDecorators(array('CompositeElement'));

        // Поиск контрагента
        $contragentSearch = new Zend_Form_Element_Text('searchByPhone');
        $contragentSearch->setLabel('ContragentSearch')
                         ->setDecorators(array('CompositeElement'));

        $ownerName = new Zend_Form_Element_Text('owner');
        $ownerName->setLabel('Owner')
                  ->setDecorators(array('CompositeElement'));

        //Целевое назначение
        $realEstatePurposeId = new Zend_Form_Element_Hidden('realEstatePurposeId');
        $realEstatePurposeId->setDecorators(array('ViewHelper'));

        $realEstatePurposeTitle = new Zend_Form_Element_Text('realEstatePurpose');
        $realEstatePurposeTitle->setLabel('RealEstatePurpose')
                              ->setAttrib('readonly', 'readonly')
                              ->setDecorators(array('CompositeElement'));


        $proposalElements = array($proposalDate, $lastCallingDate, $proposalType, $roomsAreas,
                $realEstateTypeId, $realEstateType, $complexArea, $realEstatePurposeId,
                $realEstatePurposeTitle, $price, $planningTypeId, $planningTypeId, $planningType,
                $agencyPrice, $storeyInfo, $ownerPhones, $geoDistrictId, $geoDistrictTitle,
                $ownerName, $objectAddress, $exchangeFor,
                $contragentSearch, $contragentId, $contragentTitle,
                $settlementId, $streetId, $buildingNumber, $flatNumber,
        );
        $elementNames = array();
        foreach ($proposalElements as $element) {
            $this->addElement($element);
            $elementNames[] = $element->getName();
        }
        $this->addDisplayGroup($elementNames, 'proposal', array(
            'legend' => 'MainData',
            'class'  => 'fieldset-visible'
        ));
        unset($proposalElements, $elementNames);

        // Площадь земельного участка
        $landlotArea = new Zend_Form_Element_Text('landlotArea');
        $landlotArea->setLabel('LandlotArea')
                    ->setDecorators(array('CompositeElement'));

        // Год  ввода в эксплуатацию
        $startExploitYear = new Zend_Form_Element_Text('startExploitYear');
        $startExploitYear->setLabel('StartExploitYear')
                                ->setDecorators(array('CompositeElement'));

        // Приватизация
        $isPrivatization = new Zend_Form_Element_Checkbox('isPrivatization');
        $isPrivatization->setLabel('Privatization')
                      ->setDecorators(array('CompositeElement'));
        // Жилое/нежилое
        $isForLiving = new Zend_Form_Element_Radio('isForLiving');
        $isForLiving->addMultiOption(1, 'ForLiving')
                    ->addMultiOption(0, 'NotForLiving')
                    ->setDecorators(array('CompositeElement'));

        // Балкон/лоджия
        $balconiesAndLoggiasCount = new Zend_Form_Element_Text('balconiesAndLoggiasCount');
        $balconiesAndLoggiasCount->setLabel('BalconiesAndLoggiasCount')
                       ->setDecorators(array('CompositeElement'));


        $hasGasWaterHeatingApparatus = new Zend_Form_Element_Checkbox('hasGasWaterHeatingApparatus');
        $hasGasWaterHeatingApparatus->setLabel('GWHA')
                    ->setDecorators(
                        array('CompositeElement', array('HtmlTag', array('tag' => 'div', 'id'=> 'gwha-wrapper', 'class' => 'inline-form-element'))
                    ));


        $roomsType = new Zend_Form_Element_Select('roomsType');
        $roomsType->addMultiOption(0, 'RoomsTypeNotDefined')
                  ->addMultioption(1, 'RoomsTypeIsolated')
                  ->addMultioption(2, 'RoomsTypeNeighborIsolated')
                  ->addMultioption(3, 'RoomsTypeNeighbor')
                  ->setValue(0)
                  ->setLabel('RoomsType')
                  ->setDecorators(array('CompositeElement'));

        // Вода
        $isWater = new Zend_Form_Element_Checkbox('isWater');
        $isWater->setLabel('Water')
                ->setDecorators(array('CompositeElement'));

        // Тип горячего водоснабжения
        $hotWaterSupplyId = new Zend_Form_Element_Hidden('hotWaterSupplyId');
        $hotWaterSupplyId->setDecorators(array('ViewHelper'));

        $hotWaterSupply = new Zend_Form_Element_Text('hotWaterSupply');
        $hotWaterSupply->setLabel('HotWaterSupplyType')
                       ->setAttrib('readonly', 'readonly')
                       ->setDecorators(array('CompositeElement'));

        // Газ
        $isGas = new Zend_Form_Element_Checkbox('isGas');
        $isGas->setLabel('Gas')
                   ->setDecorators(
                      array('CompositeElement', array('HtmlTag', array('tag' => 'div',  'id'=> 'gas-wrapper', 'class' => 'inline-form-element'))
                   ));

        // Вид на море
        $viewOfTheSea = new Zend_Form_Element_Checkbox('viewOfTheSea');
        $viewOfTheSea->setLabel('ViewOfTheSea')
                     ->setDecorators(array('CompositeElement'));

        // Бойлер
        $hasBoiler = new Zend_Form_Element_Checkbox('hasBoiler');
        $hasBoiler->setLabel('Boiler')
                  ->setDecorators(array('CompositeElement'));

        // Центральне опалення
        $hasCentralHeating = new Zend_Form_Element_Checkbox('hasCentralHeating');
        $hasCentralHeating->setLabel('CentralHeating')
                          ->setDecorators(array('CompositeElement'));

        // Сан. узел
        $bathroomTypeId = new Zend_Form_Element_Hidden('bathroomTypeId');
        $bathroomTypeId->setDecorators(array('ViewHelper'));

        $bathroomTypeTitle = new Zend_Form_Element_Text('bathroomType');
        $bathroomTypeTitle->setLabel('Bathroom')
                          ->setAttrib('readonly', 'readonly')
                          ->setDecorators(array('CompositeElement'));

        //notice
        $note = new Zend_Form_Element_Textarea('note');
        $note->setLabel('Notice')
             ->setDecorators(array('CompositeElement'));

        $realEstateObjectElements = array($note, $balconiesAndLoggiasCount, $landlotArea,
            $roomsType, $isPrivatization, $hotWaterSupplyId,$hotWaterSupply,
            $startExploitYear, $isPrivatization,$bathroomTypeId,
            $bathroomTypeTitle, $hasGasWaterHeatingApparatus, $isGas, $isWater,
            $hasBoiler, $isForLiving, $hasCentralHeating
        );
        $elementNames = array();
        foreach($realEstateObjectElements as $element) {
            $this->addElement($element);
            $elementNames[] = $element->getName();
        }
        $this->addDisplayGroup($elementNames, 'real-estate-object', array(
             'legend' => 'ObjectParams',
             'class'  => 'fieldset-visible'
        ));
        unset($realEstateObjectElements); unset($elementNames);

        //№ договора
        $contractCode = new Zend_Form_Element_Text('contractCode');
        $contractCode->setLabel('ContractCode')
                     ->setDecorators(array('CompositeElement'));

        // Риелтор
        $realtorId = new Zend_Form_Element_Hidden('realtorId');
        $realtorId->setDecorators(array('ViewHelper'));

        $realtorName = new Zend_Form_Element_Text('realtorName');
        $realtorName->setLabel('RealtorName')
                    ->setAttrib('readonly', 'readonly')
                    ->setDecorators(array('CompositeElement'));

        // Дата договора
        $contractDate = new ZendX_JQuery_Form_Element_DatePicker('contractDate');
        $contractDate->setLabel('ContractDate')
                ->setJQueryParams($this->getDatepickerParams())
                ->setDecorators(array('CompositeElement'));

        // Статус объекта недвижимости
        $proposalStatusId = new Zend_Form_Element_Hidden('proposalStatusId');
        $proposalStatusId->setDecorators(array('ViewHelper'));

        $proposalStatusTitle = new Zend_Form_Element_Text('proposalStatus');
        $proposalStatusTitle->setLabel('ProposalStatus')
                            ->setAttrib('readonly', 'readonly')
                            ->setDecorators(array('CompositeElement'));

        $contractElements = array($contractCode, $realtorId, $realtorName,
            $contractDate, $proposalStatusId, $proposalStatusTitle);
        $elementNames = array();
        foreach($contractElements as $element) {
            $this->addElement($element);
            $elementNames[] = $element->getName();
        }

        $this->addDisplayGroup($elementNames, 'contract', array(
            'legend' => 'Contract',
            'class'  => 'fieldset-visible'
        ));
        unset($contractElements); unset($elementNames);

        $this->_reflactions = array(
            array('connect' => array($proposalType->getName(), array('RealEstateProposal', 'PROPOSAL_TYPE')),),
            array('connect' => array($proposalDate->getName(), array('RealEstateProposal', 'PROPOSAL_DATE')),),
            array('connect' => array($lastCallingDate->getName(), array('RealEstateProposal', 'LASTCALLING_DATE')),),
            array('connect' => array($price->getName(), array('RealEstateProposal', 'PRICE')),),
            array('connect' => array($agencyPrice->getName(), array('RealEstateProposal', 'AGENCY_PRICE')),),
            array('connect' => array($contragentId->getName(), array('RealEstateProposal', 'CONTRAGENT_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($realEstateTypeId->getName(), array('RealEstateProposal', 'REALESTATETYPE_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($planningTypeId->getName(), array('RealEstateProposal', 'PLANNINGTYPE_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($isForLiving->getName(), array('RealEstateProposal', 'FOR_LIVING')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($hasGasWaterHeatingApparatus->getName(), array('RealEstateProposal', 'HAS_GWHA')),),
            array('connect' => array($hasBoiler->getName(), array('RealEstateProposal', 'HAS_BOILER')),),
            array('connect' => array($hasCentralHeating->getName(), array('RealEstateProposal', 'HAS_CENTRAL_HEATING')),),
            array('connect' => array($isWater->getName(), array('RealEstateProposal', 'IS_WATER')),),
            array('connect' => array($isGas->getName(), array('RealEstateProposal', 'IS_GAS')),),
            array('connect' => array($isPrivatization->getName(), array('RealEstateProposal', 'IS_PRIVATISATION')),),
            array('connect' => array($balconiesAndLoggiasCount->getName(), array('RealEstateProposal', 'BALCONIES_AND_LOGGIAS_COUNT')),),
            array('connect' => array($storeyInfo->getName(), array('RealEstateProposal', 'STOREY_INFO')),),
            array('connect' => array($roomsType->getName(), array('RealEstateProposal', 'ROOMS_TYPE')),),
            array('connect' => array($hotWaterSupplyId->getName(), array('RealEstateProposal', 'HOTWATERSUPPLY_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($realEstatePurposeId->getName(), array('RealEstateProposal', 'PURPOSE_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($bathroomTypeId->getName(), array('RealEstateProposal', 'BATHROOMTYPE_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($startExploitYear->getName(), array('RealEstateProposal', 'EXPLOIT_YEAR')),),
            array('connect' => array($contractCode->getName(), array('RealEstateProposal', 'CONTRACT_CODE')),),
            array('connect' => array($contractDate->getName(), array('RealEstateProposal', 'CONTRACT_DATE')),),
            array('connect' => array($landlotArea->getName(), array('RealEstateProposal', 'LANDLOT_AREA')),),
            array('connect' => array($exchangeFor->getName(), array('RealEstateProposal', 'EXCHANGE_NOTE')),),
            array('connect' => array($complexArea->getName(), array('RealEstateProposal', 'COMPLEX_AREA')),),
            array('connect' => array($realtorId->getName(), array('RealEstateProposal', 'REALTOR_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($geoDistrictId->getName(), array('RealEstateProposal', 'GEODISTRICT_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($settlementId->getName(), array('RealEstateProposal', 'SETTLEMENT_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($streetId->getName(), array('RealEstateProposal', 'STREET_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($proposalStatusId->getName(), array('RealEstateProposal', 'PROPOSALSTATUS_ID')),'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($buildingNumber->getName(), array('RealEstateProposal', 'BUILDING_NUMBER')),),
            array('connect' => array($flatNumber->getName(), array('RealEstateProposal', 'FLAT_NUMBER')),),
            array('connect' => array($roomsAreas->getName(), array('RealEstateProposal', 'ROOMS_AREAS')),),
            array('connect' => array($objectAddress->getName(), array('RealEstateProposal', 'OBJECT_ADDRESS')),),
            array('connect' => array($ownerName->getName(), array('RealEstateProposal', 'OWNER_NAME')),),
            array('connect' => array($ownerPhones->getName(), array('RealEstateProposal', 'OWNER_PHONES')),),
            array('connect' => array($note->getName(), array('RealEstateProposal', 'PROPOSAL_NOTICE')),),
        );
    }

    public function update($blankWithDocuments = true) {
        parent::update($blankWithDocuments);
        if($blankWithDocuments === true) {
            $document = $this->getDocument('RealEstateProposal');
            if($document->REALESTATETYPE_ID !== null) {
                $realEstateType = new RealEstate_Document_RealEstateType();
                $reflector = new RealEstate_Grid_Reflector_ProposalType();
                $realEstateType->load($document->REALESTATETYPE_ID);
                $this->getElement('realEstateType')->setValue($realEstateType->REALESTATETYPE_TITLE);
                unset($realEstateType, $reflector);
            }
            if($document->CONTRAGENT_ID !== null) {
                $contragent = new RealEstate_Document_Contragent();
                $contragent->load($document->CONTRAGENT_ID);
                $this->getElement('contragentTitle')->setValue($contragent->CONTRAGENT_TITLE);
                unset($contragent);
            }
            if($document->REALTOR_ID !== null) {
                $realtor = new RealEstate_Document_Account();
                $realtor->load($document->REALTOR_ID);
                $this->getElement('realtorName')->setValue($realtor->getFullName());
            }
            if($document->HEATINGTYPE_ID !== null) {
                $heatingType = new RealEstate_Document_HeatingType();
                $heatingType->load($document->HEATINGTYPE_ID);
                $this->getElement('heatingType')->setValue($heatingType->HEATINGTYPE_TITLE);
                unset($heatingType);
            }
            if($document->HOTWATERSUPPLY_ID !== null) {
                $hotWaterSupply = new RealEstate_Document_HotWaterSupply();
                $hotWaterSupply->load($document->HOTWATERSUPPLY_ID);
                $this->getElement('hotWaterSupply')->setValue($hotWaterSupply->HOTWATERSUPPLY_TITLE);
                unset($hotWaterSupply);
            }
            if($document->PLANNINGTYPE_ID !== null) {
                $planningType = new RealEstate_Document_PlanningType();
                $planningType->load($document->PLANNINGTYPE_ID);
                $this->getElement('planningType')->setValue($planningType->PLANNINGTYPE_TITLE);
                unset($planningType);
            }
            if($document->PURPOSE_ID !== null) {
                $purpose = new RealEstate_Document_RealEstatePurpose();
                $purpose->load($document->PURPOSE_ID);
                $this->getElement('realEstatePurpose')->setValue($purpose->PURPOSE_TITLE);
                unset($purpose);
            }
            if($document->GEODISTRICT_ID !== null) {
                $geoDistrict = new RealEstate_Document_GeographicalDistrict();
                $geoDistrict->load($document->GEODISTRICT_ID);
                $this->getElement('geoDistrictTitle')->setValue($geoDistrict->DISTRICT_TITLE);
                unset($geoDistrict);
            }
            if($document->PROPOSALSTATUS_ID !== null) {
                $proposalStatus = new RealEstate_Document_ProposalStatus();
                $proposalStatus->load($document->PROPOSALSTATUS_ID);
                $this->getElement('proposalStatus')->setValue($proposalStatus->PROPOSALSTATUS_TITLE);
                unset($proposalStatus);
            }
            if($document->BATHROOMTYPE_ID !== null) {
                $bathroomType = new RealEstate_Document_BathroomType();
                $bathroomType->load($document->BATHROOMTYPE_ID);
                $this->getElement('bathroomType')->setValue($bathroomType->BATHROOMTYPE_TITLE);
                unset($bathroomType);
            }
        }
    }
}