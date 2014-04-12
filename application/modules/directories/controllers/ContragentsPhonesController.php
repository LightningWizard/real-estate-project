<?php

class Directories_ContragentsPhonesController extends Zend_Controller_Action {

    protected $_resource = 'directories::contragents-phones';
    protected $_gridDataSource = null;
    protected $_headTitle = 'ContragentsPhones';

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
        if ($this->_gridDataSource === null) {
            $this->_gridDataSource = new Lis_Db_Table(array(
                        Lis_Db_Table::NAME => 'VW_DIR_CONRAGENTS_PHONES',
                        Lis_Db_Table::PRIMARY => array('PHONE_ID'),
                    ));
        }
        return $this->_gridDataSource;
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'PHONE_ID',
                    'alias' => 'PhoneId',
                    'hidden' => true,
                    'key' => true
                ))
                ->addColumn(array(
                    'name' => 'CONTRAGENT_ID',
                    'alias' => 'ContragentId',
                    'hidden' => true
                ))
                ->addColumn(array(
                    'name' => 'CONTRAGENT_PHONE',
                    'alias' => 'Phone',
                    'width' => 100
                ))
                ->addColumn(array(
                    'name' => 'CONTRAGENT_TITLE',
                    'alias' => 'Contragent',
                    'width' => 180
                ))
                ->addColumn(array(
                    'alias' => 'ContragentType',
                    'name' => 'CONTRAGENT_TYPE',
                    'width' => 150,
                    'reflector' => 'RealEstate_Grid_Reflector_ContragentType'
                ));
        return $columnModel;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();

        $phoneFilter = new Zend_Form_Element_Text('phone');
        $phoneFilter->setLabel('Phone');

        $contragentIdFilter = new Zend_Form_Element_Hidden('contragentIdFilter');

        $contragentTitleFilter = new Zend_Form_Element_Text('contragentTitleFilter');
        $contragentTitleFilter->setLabel('Contragent');

        $reflector = new RealEstate_Grid_Reflector_ContragentType();
        $types = RealEstate_Document_Contragent::getContragentTypes();
        $contragentTypeFilter = new RealEstate_Form_Element_Select('contragentTypeFilter');
        $contragentTypeFilter->setLabel('ContragentType')
                    ->setDecorators(array('CompositeElement'))
                    ->addMultiOption('','')
                    ->addReflectedOptions($types, $reflector);
        unset($reflector);

        $filterForm->addElement($phoneFilter)
                ->addElement($contragentIdFilter)
                ->addElement($contragentTitleFilter)
                ->addElement($contragentTypeFilter);

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement($phoneFilter->getName(), 'Phone', Lis_Grid_Filter::TYPE_LIKE)
                ->attachFilterToElement($contragentTitleFilter->getName(), 'Contragent', Lis_Grid_Filter::TYPE_EQUAL)
                ->attachFilterToElement($contragentTypeFilter->getName(), 'ContragentType', Lis_Grid_Filter::TYPE_EQUAL);
        return $filterForm;
    }

    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {

    }

}