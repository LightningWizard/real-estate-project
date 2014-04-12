<?php
include 'AbstractController.php';

class Directories_SettlementsController  extends Directories_AbstractController{
    protected $_resource = 'directories::settlements';
    protected $_blankClass = 'RealEstate_Blank_Settlement';
    protected $_documentAlias = 'Settlement';
    protected $_documentPrefix = 'SETTLEMENT';
    protected $_gridDataSource = null;

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_Settlement');
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                'alias' => 'Title',
                'name' => 'SETTLEMENT_TITLE',
                'width' => 250));
        return $columnModel;
    }

    protected function _getPageTitle($document) {
        $title = '';
        if($document->{$this->_documentPrefix . '_ID'} !== null){
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->SETTLEMENT_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }
}