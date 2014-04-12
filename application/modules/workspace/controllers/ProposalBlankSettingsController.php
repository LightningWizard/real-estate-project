<?php

include 'AbstractController.php';

class Workspace_ProposalBlankSettingsController extends Workspace_AbstractController {

    protected $_resource = 'workspace::proposal-blank-settings';
    protected $_blankClass = 'RealEstate_Blank_ProposalBlankSettings';
    protected $_documentAlias = 'ProposalBlankSettings';
    protected $_documentPrefix = 'SETTINGSGROUP';

    protected $_gridDataSource = null;
    
    protected function _listInterfaceAction($clientScripts = '') {
        if ($clientScripts !== '') {
            $this->_helper->ActionStack('main-scripts');
        }
        $list = new Lis_Grid_Meta();
        $list->attachColumnModel($this->_getColumnModel())
                ->setGridParam('url', $this->_helper->url('list'));
        $this->view->list = $list;
        $filterForm = $this->_configGridFilterForm($this->_getColumnModel());
        if($filterForm instanceof Lis_Grid_FilterForm){
            $this->view->filter = $filterForm;
        }
        $this->view->acl = $this->_helper->acl;
        $this->view->aclResource = $this->_resource;
        $this->_helper->layout()->setLayout('application');
        $this->view->headTitle($this->view->translate('ProposalBlankSettings'), 'SET');
    }
    
    protected function _getGridDataSource() {
        if($this->_gridDataSource == null) {
            $this->_gridDataSource = new Lis_Db_Table(array(
                Lis_Db_Table::NAME => 'VW_SYS_MAINBLANK_SETTINGS',
                Lis_Db_Table::PRIMARY => array('SETTINGSGROUP_ID')
            ));
        }
        return $this->_gridDataSource;
    }
    
    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ProposalBlankSettings');
    }
    
    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                        'name' => 'SETTINGSGROUP_ID',
                        'alias' => 'id',
                        'hidden' => true,
                        'key' => true
                    ))
                    ->addColumn(array(
                        'name' => 'REALESTATETYPE_ID',
                        'alias' => 'RealEstateTypeId',
                        'hidden' => true
                    ))
                    ->addColumn(array(
                        'name' => 'REALESTATETYPE_TITLE',
                        'alias' => 'RealEstateType',
                        'width' => 250
                    ));
        return $columnModel;
    }
    
    protected function _getPageTitle($document) {
        $title = '';
        if($document->{$this->_documentPrefix . '_ID'} !== null){
            $title = $this->view->translate('Settings')
                     . ' ' . $this->view->translate('ForRealEstateTypeInContext') 
                     . ' "' . $document->REALESTATETYPE_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }
    
    protected function _setSpecificViewParams() {
        $this->view->fieldsSettings = $this->_getSettingsGridData(); 
    }

    protected function _setSpecificFields() {
        $request = $this->getRequest();
        $hiddenFields = (array) $request->getParam('hfields', null);
        $document = $this->_blank->getDocument($this->_documentAlias);
        $document->attachHiddenFields($hiddenFields);
    }

    protected function _getSettingsGridData(){
        $proposalBlank = new RealEstate_Blank_RealEstateProposal();
        $settingsGroup = $this->_blank->getDocument($this->_documentAlias);
        $proposalBlankManager = RealEstate_ProposalBlankManager::getInstance($proposalBlank, $settingsGroup);
        $blankFieldsSettings = $proposalBlankManager->getBlankFieldsSettings();
        return $blankFieldsSettings;
    }
}