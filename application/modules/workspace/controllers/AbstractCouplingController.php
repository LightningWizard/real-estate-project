<?php

abstract class Workspace_AbstractCouplingController extends Zend_Controller_Action {

    protected $_resource = null;
    protected $_headTitle = null;

    abstract public function executeAction();

    public function mainAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'read')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $this->_listInterfaceAction('main-scripts');
    }

    public function mainScriptsAction() {
        $this->render('main-scripts', 'scripts');
    }

    protected function _listInterfaceAction($clientScripts = '') {
        if ($clientScripts !== '') {
            $this->_helper->ActionStack('main-scripts');
        }
        $list = new Lis_Grid_Meta();
        $list->attachColumnModel($this->_getColumnModel())
                ->setGridParam('url', $this->_helper->url('list'));
        $this->view->list = $list;
        $filterForm = $this->_configGridFilterForm($this->_getColumnModel());
        if ($filterForm instanceof Lis_Grid_FilterForm) {
            $this->view->filter = $filterForm;
        }
        $this->view->acl = $this->_helper->acl;
        $this->view->aclResource = $this->_resource;
        $this->_helper->layout()->setLayout('application');
        $this->view->headTitle($this->view->translate($this->_headTitle), 'SET');
    }

    public function listAction() {
        $gridData = $this->_buildGridData();
        $this->_setFiltersForGridData($gridData);
        $this->view->list = $gridData;
        $this->_helper->layout()->setLayout('json');
        $this->render('main');
    }

    protected function _buildGridData() {
        $request = $this->getRequest();
        $gridData = new Lis_Grid_Data_DbTable();
        $listDataSource = $this->_getGridDataSource();
        $columnModel = $this->_getColumnModel();
        $this->_configGridFilterForm($columnModel);
        $gridData->attachSource($listDataSource)
                ->attachColumnModel($columnModel)
                ->extractFromRequest($request);
        return $gridData;
    }

    protected function _getGridDataSource() {
        return $this->_getCollection();
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_CouplingUnit');
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'COUPLINGUNIT_TEXT',
                    'alias' => 'Announcement',
                    'width' => 300
                ))
                ->addColumn(array(
                    'name' => 'COUPLINGUNIT_DATE',
                    'alias' => 'CouplingDate',
                    'width' => 90
                ))
                ->addColumn(array(
                    'name' => 'SOURCE_CODE',
                    'alias' => 'DataSource',
                    'width' => 150,
                    'reflector' => 'RealEstate_Grid_Reflector_CouplingSource'
                ))
                ->addColumn(array(
                    'name' => 'COUPLINGUNIT_STATUS',
                    'alias' => 'Status',
                    'width' => 200,
                    'reflector' => 'RealEstate_Grid_Reflector_AnnoucementStatus'
                ))
                ->addColumn(array(
                    'name' => 'COUPLINGUNIT_DESCRIPTION',
                    'alias' => 'Description',
                    'width' => 200
                ));
        return $columnModel;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();

        $announcementFilter = new Zend_Form_Element_Text('announcement');
        $announcementFilter->setLabel('Announcement');

        $dateFrom = new Zend_Form_Element_Text('dateFrom');
        $dateFrom->setLabel('CouplingDateFrom');

        $dateTo = new Zend_Form_Element_Text('dateTo');
        $dateTo->setLabel('CouplingDateTo');

        $announcementStatusFilter = new RealEstate_Form_Element_Select('annStatusFilter');
        $availableStatuses = RealEstate_Coupling_Annoucement::getAvailableStatuses();
        $reflector = new RealEstate_Grid_Reflector_AnnoucementStatus();
        $announcementStatusFilter->setLabel('Status')
                ->addMultiOption(0, 'All')
                ->addReflectedOptions($availableStatuses, $reflector)
                ->setValue(RealEstate_Coupling_Annoucement::PHONE_NOT_EXIST);

        $filterForm
                ->addElement($dateFrom)
                ->addElement($dateTo)
                ->addElement($announcementFilter)
                ->addElement($announcementStatusFilter);

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement($announcementFilter->getName(), 'Announcement', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($dateFrom->getName(), 'CouplingDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_GREATER)
                ->attachFilterToElement($dateTo->getName(), 'CouplingDate', Lis_Grid_Filter::TYPE_EQUAL | Lis_Grid_Filter::TYPE_LESS)
                ->attachFilterToElement($announcementStatusFilter->getName(), 'Status', Lis_Grid_Filter::TYPE_EQUAL);
        return $filterForm;
    }

    public function deleteAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'remove')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $items = (array) $this->getRequest()->getPost('items');
        if (count($items)) {
            $this->_getCollection()->deleteItems($items);
        }
        return;
    }

    public function truncateAction() {
        if (!$this->_helper->acl->assert($this->_resource, 'remove')) {
            $this->_forward('permission-denied', 'error', 'system');
            return;
        }
        $this->_getCollection()->delete();
        return;
    }

    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {
        $request = $this->getRequest();
        $filter = (array) $request->getParam('filter');
        $columnModel = $gridData->getColumnModel();
        $filterForm = $columnModel->getFilterForm();
        $annStatusFilter = $filterForm->getElement('annStatusFilter');
        if (!array_key_exists('annStatusFilter', $filter)) {
            if ($annStatusFilter->getValue() !== null) {
                $request->setParam('filter', array('annStatusFilter' => $annStatusFilter->getValue()));
            }
        } else {
            if ($filter['annStatusFilter'] == 0) {
                unset($filter['annStatusFilter']);
                $request->setParam('filter', $filter);
            }
        }
    }

    public function printAction() {

        require_once 'XML/Query2XML.php';
        require_once 'MDB2.php';

        mb_internal_encoding('UTF-8');
        $gridData = $this->_buildGridData();
        $this->_setFiltersForGridData($gridData);
        $query = $gridData->getQuery(false)->assemble();

        $query2XML = RealEstate_Query2XML_Factory::factory();

        $columnModel = $this->_getColumnModel();
        $elements = $this->_extractElementsFromColumnModel($columnModel);
        $request = $this->getRequest();
        $filter = (array) $request->getParam('filter');

        $dateFrom = array_key_exists('dateFrom', $filter) ? $filter['dateFrom'] : '______';
        $dateTo = array_key_exists('dateTo', $filter) ? $filter['dateTo'] : '______';
        $annStatusFilter = array_key_exists('annStatusFilter', $filter) ? $filter['annStatusFilter'] : 0;
        $dataSourceFilter = array_key_exists('dataSourceFilter', $filter) ? $filter['dataSourceFilter'] : RealEstate_Document_CouplingUnit::TEXT_FILE;

        $options = array(
            'rootTag' => 'root',
            'rowTag' => 'row',
            'idColumn' => 'couplingunit_id',
            'elements' => $elements
        );

        $xml = $query2XML->generateReport($query, $options);
        $filters = $xml->createElement('filters');
        $dateFromEl = $xml->createElement('dateFrom');
        $dateFromEl->appendChild($xml->createTextNode($dateFrom));
        $filters->appendChild($dateFromEl);

        $dateToEl = $xml->createElement('dateTo');
        $dateToEl->appendChild($xml->createTextNode($dateTo));
        $filters->appendChild($dateToEl);

        $reflector = new RealEstate_Grid_Reflector_CouplingSource();
        $dataSourceEl = $xml->createElement('dataSource');
        $dataSourceEl->appendChild($xml->createTextNode($reflector->execute($dataSourceFilter)));
        $filters->appendChild($dataSourceEl);
        unset($reflector);

        $reflector = new RealEstate_Grid_Reflector_AnnoucementStatus();
        $annStatusEl = $xml->createElement('annStatus');
        $annStatusEl->appendChild($xml->createTextNode($annStatusFilter != 0 ? $reflector->execute($annStatusFilter): $this->view->translate('All')));
        $filters->appendChild($annStatusEl);
        unset($reflector);


        $root = $xml->getElementsByTagName('root')->item(0);
        $root->appendChild($filters);


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

    protected function _extractElementsFromColumnModel(Lis_Grid_ColumnModel $columnModel) {
        require_once 'Lis/Query2XML/Callback/GridReflector.php';
        $elements = array();
        $columns = $columnModel->getColumns();
        foreach ($columns as $column) {
            $isHidden = $column->getOption('hidden');
            if (!$isHidden) {
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
        }
        return $elements;
    }

}