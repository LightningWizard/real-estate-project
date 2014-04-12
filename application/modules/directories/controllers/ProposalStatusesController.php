<?php

include 'AbstractController.php';

class Directories_ProposalStatusesController extends Directories_AbstractController{

    protected $_resource = 'directories::real-estate::types';
    protected $_blankClass = 'RealEstate_Blank_ProposalStatus';
    protected $_documentAlias = 'ProposalStatus';
    protected $_documentPrefix = 'PROPOSALSTATUS';
    protected $_mainTitle = 'ProposalStatuses';

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_ProposalStatus');
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
           'name' => 'PROPOSALSTATUS_TITLE',
           'alias' => 'Title',
           'width' => 250
        ));
        return $columnModel;
    }

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->PROPOSALSTATUS_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }
}