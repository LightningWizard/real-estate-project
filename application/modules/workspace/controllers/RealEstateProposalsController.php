<?php

include 'AbstractController.php';

class Workspace_RealEstateProposalsController extends Workspace_AbstractController
{

    protected $_resource = 'workspace::real-estate-proposals';
    protected $_blankClass = 'RealEstate_Blank_RealEstateProposal';
    protected $_documentAlias = 'RealEstateProposal';
    protected $_documentPrefix = 'PROPOSAL';
    protected $_mainTitle = 'RealEstateObjects';
    protected $_colModelId = 17;
    protected $_gridDataSource = null;

    public function mainAction()
    {
        if (!$this->_helper->acl->assert($this->_resource, 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $this->_listInterfaceAction('main-scripts');
        $this->view->colModelId = $this->_colModelId;
    }

    public function listAction()
    {
        $gridData = $this->_buildGridData();
        $this->_setFiltersForGridData($gridData);
        $this->view->list = $gridData;
        $this->_helper->layout()->setLayout('json');
    }

    protected function _getCollection()
    {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_RealEstateProposal');
    }

    protected function _getColumnModel()
    {
        $columnModel = new Lis_Grid_ColumnModel();
        $colModelDoc = new RealEstate_Document_ColumnModel();
        $colModelDoc->load($this->_colModelId);

        $currentUser = Zend_Registry::get('currentUser');
        $userSettingsManager = new RealEstate_ColumnModel_UserSettingsManager($currentUser, $colModelDoc);

        $savedColsOptions = $colModelDoc->getColumnsSettings();
        $savedUserSettings = $userSettingsManager->getUserSettings();
        if (empty($savedUserSettings)) {
            $settingsForUser = $savedColsOptions;
        } else {
            $settingsForUser = RealEstate_ColumnModel_Facade::getSettingsForUser($savedColsOptions, $savedUserSettings);
        }
        RealEstate_ColumnModel_Facade::buildColumnModel($columnModel, $settingsForUser);
        return $columnModel;
    }

    protected function _getPageTitle($document)
    {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $realEstateType = $this->_blank->getElement('realEstateType')->getValue();
            $title = $this->view->translate($this->_documentAlias) . ' "' . $realEstateType . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function _getGridDataSource()
    {
        if ($this->_gridDataSource == null) {
            $this->_gridDataSource = new Lis_Db_Table(array(
                        Lis_Db_Table::NAME => 'VW_DOC_PROPOSAL_LIST',
                        Lis_Db_Table::PRIMARY => array('PROPOSAL_ID')
                    ));
        }
        return $this->_gridDataSource;
    }

    protected function _buildGridMeta()
    {
        $list = new Lis_Grid_Meta(array(), 'main-grid');
        $list->attachColumnModel($this->_getColumnModel())
                ->setGridParam('url', $this->_helper->url('list'));
        return $list;
    }

    protected function _setSpecificViewParams()
    {
        $blank = $this->_blank;
        $document = $blank->getDocument($this->_documentAlias);
        $this->view->documentType = get_class($document);
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel)
    {
        $filterForm = new Lis_Grid_FilterForm();

        $proposalTypes = RealEstate_Document_RealEstateProposal::getProposalTypes();
        $reflector = new RealEstate_Grid_Reflector_ProposalType();

        $proposalTypeFilter = new RealEstate_Form_Element_Select('proposalTypeFilter');
        $proposalTypeFilter->setLabel('RealEstateProposalType')
                ->setDecorators(array('CompositeElement'))
                ->addMultiOption('', '')
                ->addReflectedOptions($proposalTypes, $reflector);

        $realEstateTypeId = new Zend_Form_Element_Hidden('realEstateTypeIdFilter');
        $realEstateType = new Zend_Form_Element_Text('realEstateTypeFilter');
        $realEstateType->setLabel('RealEstateType')
                ->setAttrib('readonly', 'readonly');

        $planningTypeId = new Zend_Form_Element_Hidden('planningTypeIdFilter');
        $planningType = new Zend_Form_Element_Text('planningTypeFilter');
        $planningType->setLabel('PlanningType')
                ->setAttrib('readonly', 'readonly');

        $districtId = new Zend_Form_Element_Hidden('districtIdFilter');
        $districtTitle = new Zend_Form_Element_Text('districtTitleFilter');
        $districtTitle->setLabel('GeographicalDistrict')
                ->setAttrib('readonly', 'readonly');

        $streetIdFilter = new Zend_Form_Element_Hidden('streetIdFilter');
        $streetTitleFilter = new Zend_Form_Element_Text('streetTitleFilter');
        $streetTitleFilter->setLabel('Street');

        $priceFromFilter = new Zend_Form_Element_Text('priceFromFilter');
        $priceFromFilter->setLabel('PriceFromFilter');

        $priceToFilter = new Zend_Form_Element_Text('priceToFilter');
        $priceToFilter->setLabel('PriceToFilter');

        $realtorId = new Zend_Form_Element_Hidden('realtorIdFilter');
        $realtorName = new Zend_Form_Element_Text('realtorNameFilter');
        $realtorName->setLabel('Realtor')
                ->setAttrib('readonly', 'readonly');

        $contractCodeFilter = new Zend_Form_Element_Text('contractCodeFilter');
        $contractCodeFilter->setLabel('ContractCode');

        $contragentType = new Zend_Form_Element_Select('isAgencyFilter');
        $contragentType->setLabel('ContragentType');
        $contragentType->addMultiOption('', '')
                ->addMultiOption(0, 'PhysicalPerson')
                ->addMultiOption(1, 'RealEstateAgency');

        $proposalStatusId = new Zend_Form_Element_Hidden('proposalStatusIdFilter');
        $proposalStatus = new Zend_Form_Element_Text('proposalStatusFilter');
        $proposalStatus->setLabel('Status')
                ->setAttrib('readonly', 'readonly');

        // total area filter
        $totalAreaFilterFrom = new Zend_Form_Element_Text('totalAreaFilterFrom');
        $totalAreaFilterFrom->setLabel('TotalAreaFrom');

        $totalAreaFilterTo = new Zend_Form_Element_Text('totalAreaFilterTo');
        $totalAreaFilterTo->setLabel('TotalAreaTo');

        //living area filter
        $livingAreaFilterFrom = new Zend_Form_Element_Text('livingAreaFilterFrom');
        $livingAreaFilterFrom->setLabel('LivingAreaFrom');

        $livingAreaFilterTo = new Zend_Form_Element_Text('livingAreaFilterTo');
        $livingAreaFilterTo->setLabel('LivingAreaTo');

        //kitchen area filter
        $kitchenAreaFilterFrom = new Zend_Form_Element_Text('kitchenAreaFilterFrom');
        $kitchenAreaFilterFrom->setLabel('KitchenAreaFrom');

        $kitchenAreaFilterTo = new Zend_Form_Element_Text('kitchenAreaFilterTo');
        $kitchenAreaFilterTo->setLabel('KitchenAreaTo');

        $creationDateFromFilter = new ZendX_JQuery_Form_Element_DatePicker('creationDateFromFilter');
        $creationDateFromFilter->setLabel('CreationDateFrom')
                ->setJQueryParams(array(
                    'showOn' => 'button',
                    'buttonImage' => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat' => 'dd.mm.yy',
                        )
        );

        $creationDateToFilter = new ZendX_JQuery_Form_Element_DatePicker('creationDateToFilter');
        $creationDateToFilter->setLabel('CreationDateTo')
                ->setJQueryParams(array(
                    'showOn' => 'button',
                    'buttonImage' => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat' => 'dd.mm.yy',
                        )
        );

        $lastCallingDateFromFilter = new ZendX_JQuery_Form_Element_DatePicker('lastCallingDateFromFilter');
        $lastCallingDateFromFilter->setLabel('LastCallingDateFrom')
                ->setJQueryParams(array(
                    'showOn' => 'button',
                    'buttonImage' => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat' => 'dd.mm.yy',
                        )
        );

        $lastCallingDateToFilter = new ZendX_JQuery_Form_Element_DatePicker('lastCallingDateToFilter');
        $lastCallingDateToFilter->setLabel('LastCallingDateTo')
                ->setJQueryParams(array(
                    'showOn' => 'button',
                    'buttonImage' => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat' => 'dd.mm.yy',
                        )
        );

        $ownerFilter = new Zend_Form_Element_Text('ownerFilter');
        $ownerFilter->setLabel('Owner');

        $ownerPhonesFilter = new Zend_Form_Element_Text('ownerPhonesFilter');
        $ownerPhonesFilter->setLabel('OwnerPhones');

        $storeyNumberFilter = new Zend_Form_Element_Text('storeyNumberFilter');
        $storeyNumberFilter->setLabel('Storey');

        $storeyCountFilter = new Zend_Form_Element_Text('storeyCountFilter');
        $storeyCountFilter->setLabel('StoreyCount');

        $noticeFilter = new Zend_Form_Element_Text('noticeFilter');
        $noticeFilter->setLabel('Notice');

        $landlotAreaFilter = new Zend_Form_Element_Text('landlotAreaFilter');
        $landlotAreaFilter->setLabel('LandlotArea');

        $purposeIdFilter = new Zend_Form_Element_Hidden('purposeIdFilter');
        $purposeTitleFilter = new Zend_Form_Element_Text('purposeTitleFilter');
        $purposeTitleFilter->setLabel('Purpose')
                ->setAttrib('readonly', 'readonly');

        $exploitYearFromFilter = new Zend_Form_Element_Text('exploitYearFromFilter');
        $exploitYearFromFilter->setLabel('ExploitYearFrom');

        $exploitYearToFilter = new Zend_Form_Element_Text('exploitYearToFilter');
        $exploitYearToFilter->setLabel('ExploitYearTo');

        $isPrivatizationFilter = new Zend_Form_Element_Select('isPrivatizationFilter');
        $isPrivatizationFilter->setLabel('Privatization')
                ->addMultiOption('', '')
                ->addMultiOption(1, 'Yes')
                ->addMultiOption(0, 'No');

        $isWaterFilter = new Zend_Form_Element_Select('isWaterFilter');
        $isWaterFilter->setLabel('Water')
                ->addMultiOption('', '')
                ->addMultiOption(1, 'Yes')
                ->addMultiOption(0, 'No');

        $isElectricityFilter = new Zend_Form_Element_Select('isElectricityFilter');
        $isElectricityFilter->setLabel('Electricity')
                ->addMultiOption('', '')
                ->addMultiOption(1, 'Yes')
                ->addMultiOption(0, 'No');

        $isGasFilter = new Zend_Form_Element_Select('isGasFilter');
        $isGasFilter->setLabel('Gas')
                ->addMultiOption('', '')
                ->addMultiOption(1, 'Yes')
                ->addMultiOption(0, 'No');

        $viewOnSeaFilter = new Zend_Form_Element_Select('viewOfTheSeaFilter');
        $viewOnSeaFilter->setLabel('ViewOfTheSea')
                ->addMultiOption('', '')
                ->addMultiOption(1, 'Yes')
                ->addMultiOption(0, 'No');

        $contractDateFromFilter = new ZendX_JQuery_Form_Element_DatePicker('contractDateFromFilter');
        $contractDateFromFilter->setLabel('ContractDateFrom')
                ->setJQueryParams(array(
                    'showOn' => 'button',
                    'buttonImage' => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat' => 'dd.mm.yy',
                        )
        );

        $contractDateToFilter = new ZendX_JQuery_Form_Element_DatePicker('contractDateToFilter');
        $contractDateToFilter->setLabel('ContractDateTo')
                ->setJQueryParams(array(
                    'showOn' => 'button',
                    'buttonImage' => '/img/icons/16x16/calendar.gif',
                    'buttonImageOnly' => true,
                    'dateFormat' => 'dd.mm.yy',
                        )
        );

        $heatingTypeIdFilter = new Zend_Form_Element_Hidden('heatingTypeIdFilter');
        $heatingTypeTitleFilter = new Zend_Form_Element_Text('heatingTypeFilter');
        $heatingTypeTitleFilter->setLabel('HeatingType')
                ->setAttrib('readonly', 'readonly');

        $hotWaterSupplyIdFilter = new Zend_Form_Element_Hidden('hotWaterSupplyIdFilter');
        $hotWaterTitleFilter = new Zend_Form_Element_Text('hotWaterSupplyFilter');
        $hotWaterTitleFilter->setLabel('HotWaterSupplyType')
                ->setAttrib('readonly', 'readonly');

        $addressFilter = new Zend_Form_Element_Text('addressFilter');
        $addressFilter->setLabel('Address');

        $exchangeForFilter = new Zend_Form_Element_Text('exchangeForFilter');
        $exchangeForFilter->setLabel('ExchangeFor');

        $contragentTitleFilter = new Zend_Form_Element_Text('contragentTitleFilter');
        $contragentTitleFilter->setLabel('RealEstateAgency');


        $proposalElements = array($proposalTypeFilter);
        $elementNames = array($creationDateFromFilter, $creationDateToFilter, $lastCallingDateFromFilter,
            $lastCallingDateToFilter, $proposalTypeFilter, $realEstateTypeId, $realEstateType,
            $totalAreaFilterFrom, $totalAreaFilterTo, $livingAreaFilterFrom, $livingAreaFilterTo,
            $kitchenAreaFilterFrom, $kitchenAreaFilterTo, $purposeIdFilter, $purposeTitleFilter,
            $planningTypeId, $planningType, $priceFromFilter, $priceToFilter,
            $storeyNumberFilter, $storeyCountFilter, $districtId, $districtTitle, $ownerPhonesFilter,
            $addressFilter, $ownerFilter, $streetIdFilter, $streetTitleFilter, $exchangeForFilter,
            $contragentType, $contragentTitleFilter,
        );
        foreach ($proposalElements as $element) {
            $filterForm->addElement($element);
            $elementNames[] = $element->getName();
        }
        $filterForm->addDisplayGroup($elementNames, 'proposal', array(
            'legend' => 'MainData',
            'class' => 'fieldset-visible'
        ));
        unset($proposalElements, $elementNames);

        $realEstateObjectElements = array($noticeFilter, $landlotAreaFilter,
            $exploitYearFromFilter, $exploitYearToFilter, $isPrivatizationFilter,
            $isElectricityFilter, $isWaterFilter, $viewOnSeaFilter, $isGasFilter,
            $heatingTypeIdFilter, $heatingTypeTitleFilter, $hotWaterSupplyIdFilter,
            $hotWaterTitleFilter
        );
        $elementNames = array();
        foreach ($realEstateObjectElements as $element) {
            $filterForm->addElement($element);
            $elementNames[] = $element->getName();
        }
        $filterForm->addDisplayGroup($elementNames, 'real-estate-object', array(
            'legend' => 'ObjectParams',
            'class' => 'fieldset-visible'
        ));
        unset($realEstateObjectElements);
        unset($elementNames);

        $contractElements = array($contractCodeFilter, $contragentType, $contractDateFromFilter,
            $contractDateToFilter, $realtorId, $realtorName, $proposalStatusId,
            $proposalStatus
        );
        $elementNames = array();
        foreach ($contractElements as $element) {
            $filterForm->addElement($element);
            $elementNames[] = $element->getName();
        }

        $filterForm->addDisplayGroup($elementNames, 'contract', array(
            'legend' => 'Contract',
            'class' => 'fieldset-visible'
        ));
        unset($contractElements);
        unset($elementNames);

        $filterForm->setDisplayGroupDecorators(array(
            'FormElements',
            array(array('ElementsWrapper' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-elements')),
            'Description',
            'Fieldset',
            array('HtmlTag', array('tag' => 'div', 'class' => 'fieldset-wrapper')),
        ));

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('proposalTypeFilter', 'RealEstateProposalType', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement('realEstateTypeIdFilter', 'RealEstateTypeId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement('planningTypeIdFilter', 'PlanningTypeId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement('districtIdFilter', 'DistrictId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement('priceFromFilter', 'OwnerPrice', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement('priceToFilter', 'OwnerPrice', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement('realtorIdFilter', 'RealtorId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement('contractCodeFilter', 'ContractCode', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement('isAgencyFilter', 'IsAgency', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement('proposalStatusIdFilter', 'ProposalStatusId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($ownerPhonesFilter->getName(), 'OwnerPhones', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($totalAreaFilterFrom->getName(), 'TotalArea', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement($totalAreaFilterTo->getName(), 'TotalArea', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement($livingAreaFilterFrom->getName(), 'LivingArea', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement($livingAreaFilterTo->getName(), 'LivingArea', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement($kitchenAreaFilterFrom->getName(), 'KitchenArea', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement($kitchenAreaFilterTo->getName(), 'KitchenArea', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement($creationDateFromFilter->getName(), 'EnteringDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement($creationDateToFilter->getName(), 'EnteringDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement($lastCallingDateFromFilter->getName(), 'LastCallingDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement($lastCallingDateToFilter->getName(), 'LastCallingDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement($storeyNumberFilter->getName(), 'Storey', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($storeyCountFilter->getName(), 'StoreyCount', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($ownerFilter->getName(), 'Owner', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($noticeFilter->getName(), 'Notice', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($landlotAreaFilter->getName(), 'Notice', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($purposeIdFilter->getName(), 'PurposeId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($isPrivatizationFilter->getName(), 'Privatization', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($isWaterFilter->getName(), 'Water', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($isElectricityFilter->getName(), 'Electricity', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($isGasFilter->getName(), 'Gas', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($viewOnSeaFilter->getName(), 'ViewOfTheSea', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($contractDateFromFilter->getName(), 'ContractDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement($contractDateToFilter->getName(), 'ContractDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement($heatingTypeIdFilter->getName(), 'HeatingTypeId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($hotWaterSupplyIdFilter->getName(), 'HotWaterSupplyId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($streetIdFilter->getName(), 'StreetId', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($addressFilter->getName(), 'Address', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($exchangeForFilter->getName(), 'ExchangeNote', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($contragentTitleFilter->getName(), 'RealEstateAgency', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }

    public function blankFieldsSettingsAction()
    {
        $request = $this->getRequest();
        if (!$request->isXmlHttpRequest()) {
            $this->_forward('error', 'error', 'system');
            return;
        }
        $realEstateType = $request->getParam('realEstateType');
        $collection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ProposalBlankSettings');
        $hiddenFields = array();
        if (!empty($realEstateType)) {
            $settingsGroup = $collection->fetchForRealEstateType($realEstateType);
            if ($settingsGroup !== null) {
                $hiddenFields = $settingsGroup->getHiddenFields();
            }
        }
        echo Zend_Json::encode($hiddenFields);
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->getHelper('layout')->disableLayout();
    }

    protected function _setDocumentDefaults(Lis_Document_Abstract $document)
    {
        $request = $this->getRequest();
        $couplingUnitId = $request->getParam('coupling-unit-id');
        if ($document->PROPOSAL_ID === null && $couplingUnitId !== null) {
            $couplingUnit = new RealEstate_Document_CouplingUnit();
            $couplingUnit->load($couplingUnitId);
            $document->COUPLINGUNIT_ID = $couplingUnit->COUPLINGUNIT_ID;
            $document->PROPOSAL_NOTICE = $couplingUnit->COUPLINGUNIT_TEXT;
            $document->OWNER_PHONES = implode(',', $couplingUnit->getSavedPhones());
        }
        if (empty($document->PROPOSAL_DATE)) {
            $document->PROPOSAL_DATE = date('d.m.Y');
        }
        if ($document->PROPOSALSTATUS_ID === null) {
            $defaultStatusId = 18;
            $statusesCollection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ProposalStatus');
            $statusExist = $statusesCollection->fetchRow($statusesCollection->select()->where('PROPOSALSTATUS_ID = ?', $defaultStatusId));
            if ($statusExist) {
                $document->PROPOSALSTATUS_ID = $defaultStatusId;
            }
        }
        if (empty($document->REALTOR_ID)) {
            $user = Zend_Registry::get('currentUser');
            $document->REALTOR_ID = $user->getId();
        }
        if ($document->IS_GAS === null) {
            $document->IS_GAS = 1;
        }
        if ($document->IS_ELECTRICITY === null) {
            $document->IS_ELECTRICITY = 1;
        }
        if ($document->IS_WATER === null) {
            $document->IS_WATER = 1;
        }
        if ($document->IS_SEWERAGE === null) {
            $document->IS_SEWERAGE = 1;
        }
    }

    public function printAction()
    {

        require_once 'XML/Query2XML.php';
        require_once 'MDB2.php';

        mb_internal_encoding('UTF-8');
        $gridData = $this->_buildGridData();
        $this->_setFiltersForGridData($gridData);

        $query2XML = RealEstate_Query2XML_Factory::factory();

        $columnModel = new Lis_Grid_ColumnModel();
        $colModelDoc = new RealEstate_Document_ColumnModel();
        $colModelDoc->load($this->_colModelId);

        $currentUser = Zend_Registry::get('currentUser');
        $userSettingsManager = new RealEstate_ColumnModel_UserSettingsManager($currentUser, $colModelDoc);

        $savedColsOptions = $colModelDoc->getColumnsSettings();
        $savedUserSettings = $userSettingsManager->getUserSettings();
        if (empty($savedUserSettings)) {
            $settingsForUser = $savedColsOptions;
        } else {
            $settingsForUser = RealEstate_ColumnModel_Facade::getSettingsForUser($savedColsOptions, $savedUserSettings);
        }
        RealEstate_ColumnModel_Facade::buildColumnModelForPrint($columnModel, $settingsForUser);

        $elements = $this->_extractElementsFromColumnModel($columnModel);
        $options = array(
            'rootTag' => 'root',
            'rowTag' => 'row',
            'idColumn' => 'proposal_id',
            'elements' => $elements
        );
        $headers = array();
        foreach ($columnModel->getColumns() as $column) {
            $columnAlias = $column->getOption('alias');
            $headers[] = $this->view->translate($columnAlias);
        }

        $query = $gridData->getQuery(false)->assemble();
        $xml = $query2XML->generateReport($query, $options);

        $theaders = $xml->createElement('headers');
        foreach ($headers as $header) {
            $theader = $xml->createElement('header');
            $theader->appendChild($xml->createTextNode($header));
            $theaders->appendChild($theader);
        }
        $root = $xml->getElementsByTagName('root')->item(0);
        $root->appendChild($theaders);


        $response = $this->getResponse();
        $templateDir = $this->view->getScriptPaths();
        $templateDir = $templateDir[0];
        $template = $templateDir . $this->getRequest()->getControllerName()
                . DIRECTORY_SEPARATOR . 'template.xsl';

        $xsl = new DOMDocument();
        $xsl->load($template);
        $xslProc = new XSLTProcessor();
        $xslProc->importStylesheet($xsl);

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
//        $response->setHeader("Content-Type", "application/xml");
//        echo $xml->saveXML();
        $response->setHeader("Content-Type", "text/html");
        echo $xslProc->transformToXml($xml);
    }

    protected function _extractElementsFromColumnModel(Lis_Grid_ColumnModel $columnModel)
    {
        require_once 'Lis/Query2XML/Callback/GridReflector.php';
        $elements = array();
        $columns = $columnModel->getColumns();
        foreach ($columns as $column) {
            $columnName = mb_convert_case($column->getOption('name'), MB_CASE_LOWER);
            $reflectorClass = $column->getOption('reflector');
            if ($reflectorClass !== null) {
                $reflectorFile = str_replace('_', DIRECTORY_SEPARATOR, $reflectorClass);
                $reflectorFile = realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library')
                        . DIRECTORY_SEPARATOR . $reflectorFile . '.php';
                $reflector = new $reflectorClass();
                $elements[$columnName] = new Lis_Query2XML_Callback_GridReflector($columnName, $reflector);
            } else {
                $elements[] = $columnName;
            }
        }
        return $elements;
    }

    protected function _afterDocumentCreate()
    {
        $request = $this->getRequest();
        $couplingUnitId = $request->getParam('coupling-unit-id');
        if ($couplingUnitId !== null) {
            $collection = Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_CouplingUnit');
            $collection->deleteItems(array($couplingUnitId));
        }
        parent::_afterDocumentCreate();
    }
}