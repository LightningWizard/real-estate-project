<?php

include 'AbstractController.php';

class Directories_HeatingTypesController extends Directories_AbstractController {

    protected $_resource = 'directories::real-estate::heating-types';
    protected $_blankClass = 'RealEstate_Blank_HeatingType';
    protected $_documentAlias = 'HeatingType';
    protected $_documentPrefix = 'HEATINGTYPE';
    
    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
            'name' => 'HEATINGTYPE_TITLE',
            'alias' => 'Title',
            'width' => 250
        ));
        return $columnModel;
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_HeatingType');
    }

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->HEATINGTYPE_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('heatingType');
        $titleFilter->setLabel('Title');
        
        $filterForm->addElement($titleFilter);
        
        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('heatingType', 'Title', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }
}