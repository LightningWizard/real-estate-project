<?php

include 'AbstractController.php';

class Directories_RealEstatePurposesController extends Directories_AbstractController {

    protected $_resource = 'directories::real-estate::purposes';
    protected $_blankClass = 'RealEstate_Blank_RealEstatePurpose';
    protected $_documentAlias = 'RealEstatePurpose';
    protected $_documentPrefix = 'PURPOSE';
    protected $_gridDataSource;

    protected function _listInterfaceAction($clientScripts = '') {
        if ($clientScripts !== '') {
            $this->_helper->ActionStack('main-scripts');
        }
        $request = $this->getRequest();
        $forTypeFilter = $request->getParam('forType', null);
        $list = new Lis_Grid_Meta();
        $gridParams = array('url' => $this->_helper->url('list'));
        if($forTypeFilter !== null) {
            $gridParams['postData'] = array('forType' => $forTypeFilter);
        }
        $list->attachColumnModel($this->_getColumnModel())
             ->setGridParams($gridParams);
        $this->view->list = $list;
        $filterForm = $this->_configGridFilterForm($this->_getColumnModel());
        if($filterForm instanceof Lis_Grid_FilterForm){
            $this->view->filter = $filterForm;
        }
        $this->view->acl = $this->_helper->acl;
        $this->view->aclResource = $this->_resource;
        $this->_helper->layout()->setLayout('application');
        if(substr($this->_documentAlias, -1) == 'y') {
            $title = substr($this->_documentAlias, 0, -1) . 'ies';
        } else {
            $title = $this->_documentAlias . 's';
        }
        $this->view->headTitle($this->view->translate($title), 'SET');
    }

    protected function _setFiltersForGridData(Lis_Grid_Data_Abstract $gridData) {
        $request = $this->getRequest();
        $forTypeFilter = $request->getParam('forType', null);
        if($forTypeFilter !== null) {
            $gridData->addFilter('REALESTATETYPE_ID', Lis_Grid_Filter::TYPE_EQUAL, $forTypeFilter);
        }
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_RealEstatePurpose');
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'PURPOSE_TITLE',
                    'alias' => 'Title',
                    'width' => 200
                ))
                ->addColumn(array(
                    'name' => 'REALESTATETYPE_TITLE',
                    'alias' => 'RealEstateType',
                    'width' => 200
                ));
        return $columnModel;
    }

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->PURPOSE_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function _getGridDataSource() {
        if($this->_gridDataSource == null) {
            $this->_gridDataSource = new Lis_Db_Table(array(
                Lis_Db_Table::NAME => 'VW_DIR_PURPOSE_LIST',
                Lis_Db_Table::PRIMARY => array('PURPOSE_ID')
            ));
        }
        return $this->_gridDataSource;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('purpose');
        $titleFilter->setLabel('Title');

        $filterForm->addElement($titleFilter);

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('purpose', 'Title', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }
}