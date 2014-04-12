<?php

include 'AbstractController.php';

class Directories_HotWaterSuppliesController extends Directories_AbstractController {

    protected $_resource = 'directories::real-estate::hot-water-supplies';
    protected $_blankClass = 'RealEstate_Blank_HotWaterSupply';
    protected $_documentAlias = 'HotWaterSupplyType';
    protected $_documentPrefix = 'HOTWATERSUPPLY';
    
    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
            'alias' => 'Title',
            'name' => 'HOTWATERSUPPLY_TITLE',
            'width' => 250
        ));
        return $columnModel;
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_HotWaterSupply');
    }

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->HOTWATERSUPPLY_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }
    
    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('hotWaterSupplyType');
        $titleFilter->setLabel('Title');
        
        $filterForm->addElement($titleFilter);
        
        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('hotWaterSupplyType', 'Title', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }
}