<?php

include 'AbstractController.php';

class Directories_StreetTypesController extends Directories_AbstractController {
    protected $_resource = 'directories::street-types';
    protected $_blankClass = 'RealEstate_Blank_StreetType';
    protected $_documentAlias = 'StreetType';
    protected $_documentPrefix = 'STREETTYPE';

    protected function _getPageTitle($document) {
        $title = '';
        if($document->{$this->_documentPrefix . '_ID'} !== null){
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->STREETTYPE_TITLE . '"'; 
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                        'alias' => 'Title',
                        'name' => 'STREETTYPE_TITLE',
                        'width' => 250))
                    ->addColumn(array(
                        'alias' => 'ShortTitle',
                        'name' => 'STREETTYPE_SHORTTITLE',
                        'width' => 150));
        return $columnModel;
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_StreetType');
    }
    
    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('streetType');
        $titleFilter->setLabel('StreetType');
        
        $shortTitleFilter = new Zend_Form_Element_Text('streetTypeShort');
        $shortTitleFilter->setLabel('ShortTitle');
        
        $filterForm->addElement($titleFilter)
                   ->addElement($shortTitleFilter);
        
        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('streetType', 'Title', Lis_Grid_Filter::TYPE_LIKE)
                   ->attachFilterToElement('streetTypeShort', 'ShortTitle', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }
}
