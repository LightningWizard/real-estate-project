<?php

class RealEstate_Blank_HotWaterSupply extends Lis_Blank_Abstract {

    public function init(){
        $this->setAttrib('id', 'blank-hot-water-supply')
             ->setMethod('post');
        $blankDocument = new RealEstate_Document_HotWaterSupply();
        $this->addDocument($blankDocument, 'HotWaterSupplyType');

        $title = new Zend_Form_Element_Text('title');
        $title->setLabel('Title')
              ->setRequired(true)
              ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'HOTWATERSUPPLY_TITLE'))
              ->setDecorators(array('CompositeElement'));

        $mainElements = array($title);
        $mainElementNames = array();
        foreach ($mainElements as $element) {
            $this->addElement($element);
            $mainElementNames[] = $element->getName();
        }

        $this->addDisplayGroup($mainElementNames, 'main-data',
                array('legend' => 'MainData', 'class' => 'fieldset-visible'));

        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'));
        $this->addElement($note);
        $this->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array($title->getName(), array('HotWaterSupplyType', 'HOTWATERSUPPLY_TITLE')),),
            array('connect' => array($note->getName(), array('HotWaterSupplyType', 'HOTWATERSUPPLY_NOTICE')),),
        );

    }
}