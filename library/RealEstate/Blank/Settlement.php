<?php

class RealEstate_Blank_Settlement extends Lis_Blank_Abstract {

    public function init() {
        $this->setAttrib('id', 'blank-settlement')
             ->setMethod('post');

        $blankDocument = new RealEstate_Document_Settlement();
        $this->addDocument($blankDocument, 'Settlement');

        $settlementTitle = new Zend_Form_Element_Text('settlement');
        $settlementTitle->setLabel('Settlement')
                ->setRequired(true)
                ->setDecorators(array('CompositeElement'));

        $mainElements = array($settlementTitle);
        $mainElementNames = array();

        foreach ($mainElements as $element) {
            $this->addElement($element);
            $mainElementNames[] = $element->getName();
        }

        $this->addDisplayGroup($mainElementNames, 'main-data', array('legend' => 'MainData', 'class' => 'fieldset-visible'));

        //notice
        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'));

        $this->addElement($note)
                ->addDisplayGroup(array('note'), 'notice', array('legend' => 'Notice', 'class' => 'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array($settlementTitle->getName(), array('Settlement', 'SETTLEMENT_TITLE')),),
            array('connect' => array($note->getName(), array('Settlement', 'SETTLEMENT_NOTICE')),),
        );
    }
}