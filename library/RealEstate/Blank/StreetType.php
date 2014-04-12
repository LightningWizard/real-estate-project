<?php

class RealEstate_Blank_StreetType extends Lis_Blank_Abstract {
    public function init(){
        $this->setAttrib('id', 'blank-street-type')
             ->setMethod('post');
        $blankDocument = new RealEstate_Document_StreetType();
        $this->addDocument($blankDocument, 'StreetType');

        $streetTypeTitle = new Zend_Form_Element_Text('streetTypeTitle');
        $streetTypeTitle->setLabel('StreetType')
                        ->setRequired(true)
                        ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'STREETTYPE_TITLE'))
                        ->setDecorators(array('CompositeElement'));

        $streetTypeTitleShort = new Zend_Form_Element_Text('streetTypeTilte');
        $streetTypeTitleShort->setLabel('StreetTypeShort')
                             ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'STREETTYPE_SHORTTITLE'))
                             ->setDecorators(array('CompositeElement'));

        $mainElements = array(
            $streetTypeTitle, $streetTypeTitleShort
        );
        $mainElementNames = array();
        foreach ($mainElements as $element) {
            $this->addElement($element);
            $mainElementNames[] = $element->getName();
        }

        $this->addDisplayGroup($mainElementNames, 'main-data',
                array('legend' => 'MainData', 'class' => 'fieldset-visible'));

        //notice
        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'));

        $this->addElement($note)
             ->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array($streetTypeTitle->getName(), array('StreetType', 'STREETTYPE_TITLE')),),
            array('connect' => array($streetTypeTitleShort->getName(), array('StreetType', 'STREETTYPE_SHORTTITLE')),),
            array('connect' => array($note->getName(), array('StreetType', 'STREETTYPE_NOTICE')),),
        );
    }
}