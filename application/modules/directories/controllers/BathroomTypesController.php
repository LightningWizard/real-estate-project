<?php

include 'AbstractController.php';

class Directories_BathroomTypesController extends Directories_AbstractController {

    protected $_resource = 'directories::real-estate::bathroom-types';
    protected $_blankClass = 'RealEstate_Blank_BathroomType';
    protected $_documentAlias = 'BathroomType';
    protected $_documentPrefix = 'BATHROOMTYPE';

    protected function _getCollection() {
        return Lis_Document_Collection_Factory::factory('RealEstate_Document_Collection_BathroomType');
    }

    protected function _getColumnModel() {
        $columnModel = new Lis_Grid_ColumnModel();
        $columnModel->addColumn(array(
                    'name' => 'BATHROOMTYPE_ID',
                    'alias' => 'id',
                    'hidden' => true,
                    'key' => true
                ))
                ->addColumn(array(
                    'name' => 'BATHROOMTYPE_TITLE',
                    'alias' => 'Title',
                    'width' => 250
                ));
        return $columnModel;
    }

    protected function _configGridFilterForm(Lis_Grid_ColumnModel $columnModel) {
        $filter = new Lis_Grid_FilterForm();
        $columnModel->attachFilterForm($filter);

        $filterElement = new Zend_Form_Element_Text('bathroomTypeFilter');
        $filterElement->setLabel('BathroomType');

        $filter->addElement($filterElement)
               ->attachFilterToElement($filterElement->getName(), 'Title', Lis_Grid_Filter::TYPE_LIKE);
        return $filter;
    }

    protected function _getPageTitle($document) {
        $title = '';
        if ($document->{$this->_documentPrefix . '_ID'} !== null) {
            $title = $this->view->translate($this->_documentAlias) . ' "' . $document->BATHROOMTYPE_TITLE . '"';
        } else {
            $title = $this->view->translate('New' . $this->_documentAlias);
        }
        return $title;
    }

}