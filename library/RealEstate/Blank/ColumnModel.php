<?php

class RealEstate_Blank_ColumnModel extends Lis_Blank_Abstract {

    public function init() {
        $this->setAttrib('id', 'blank-column-model')
                ->setMethod('post');

        $blankDocument = new RealEstate_Document_ColumnModel();
        $this->addDocument($blankDocument, 'ColumnModel');

        $colModelAlias = new Zend_Form_Element_Text('colModelAlias');
        $colModelAlias->setLabel('Alias')
                ->setRequired(true)
                ->setDecorators(array('CompositeElement'));

        $dataSourceType = new Zend_Form_Element_Select('dataSourceType');
        $dataSourceType->setLabel('DataSourceType')
                     ->setRequired(true)
                     ->setDecorators(array('CompositeElement'))
                     ->addMultiOption(1, 'DbTableOrView')
                     ->addMultiOption(2, 'Query')
                     ->addMultiOption(3, 'Array');

        $dataSource = new Zend_Form_Element_Textarea('dataSource');
        $dataSource->setLabel('DataSource')
                   ->setRequired(true)
                   ->setDecorators(array('CompositeElement'));

        $mainElements = array($colModelAlias, $dataSourceType, $dataSource);
        $mainElementNames = array();
        foreach ($mainElements as $element) {
            $this->addElement($element);
            $mainElementNames[] = $element->getName();
        }

        $this->addDisplayGroup($mainElementNames, 'main-data', array('legend' => 'MainData', 'class' => 'fieldset-visible'));

        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'));
        $this->addElement($note);
        $this->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array($colModelAlias->getName(), array('ColumnModel', 'COLMODEL_ALIAS')),),
            array('connect' => array($dataSourceType->getName(), array('ColumnModel', 'DATASOURCE_TYPE')),),
            array('connect' => array($dataSource->getName(), array('ColumnModel', 'DATASOURCE')),),
            array('connect' => array($note->getName(), array('ColumnModel', 'COLMODEL_NOTICE')),),
        );
    }

}
