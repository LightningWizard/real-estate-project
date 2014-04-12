<?php

include 'AbstractController.php';

class Directories_RealEstateTypesController extends Directories_AbstractController {

    protected $_resource = 'directories::real-estate::types';
    protected $_blankClass = 'RealEstate_Blank_RealEstateType';
    protected $_documentAlias = 'RealEstateType';
    protected $_documentPrefix = 'REALESTATETYPE';

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->REALESTATETYPE_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'REALESTATETYPE_ID',
                    'alias' => 'id',
                    'hidden' => true,
                    'key' => true
                ))
                ->addColumn(array(
                    'name' => 'REALESTATETYPE_TITLE',
                    'alias' => 'Title',
                    'width' => 200
                ));
        return $columnModel;
    }

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_RealEstateType');
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filterForm = new Lis_Grid_FilterForm();
        $titleFilter = new Zend_Form_Element_Text('realEstateFilter');
        $titleFilter->setLabel('Title');

        $filterForm->addElement($titleFilter);

        $filterForm->attachToColumnModel($columnModel);
        $filterForm->attachFilterToElement('realEstateFilter', 'Title', Lis_Grid_Filter::TYPE_LIKE);
        return $filterForm;
    }
}