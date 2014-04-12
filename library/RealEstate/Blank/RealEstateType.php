<?php

class RealEstate_Blank_RealEstateType extends Lis_Blank_Abstract {

    public function init() {
        $this->setAttrib('id', 'blank-real-estate-type')
                ->setMethod('post');

        $blankDocument = new RealEstate_Document_RealEstateType();
        $this->addDocument($blankDocument, 'RealEstateType');

        $realEstateTypeTitle = new Zend_Form_Element_Text('realEstateTypeTitle');
        $realEstateTypeTitle->setLabel('Title')
                ->setRequired(true)
                ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'REALESTATETYPE_TITLE'))
                ->setDecorators(array('CompositeElement'));

        $mainElements = array($realEstateTypeTitle);
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
            array('connect' => array($realEstateTypeTitle->getName(), array('RealEstateType', 'REALESTATETYPE_TITLE')),),
            array('connect' => array($note->getName(), array('RealEstateType', 'REALESTATETYPE_NOTICE')),),
        );
    }
}