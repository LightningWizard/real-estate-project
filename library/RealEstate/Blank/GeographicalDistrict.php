<?php

class RealEstate_Blank_GeographicalDistrict extends Lis_Blank_Abstract {
    public function init() {
        $this->setAttrib('id', 'blank-geographical-district')
             ->setMethod('post');
        $blankDocument = new RealEstate_Document_GeographicalDistrict();
        $this->addDocument($blankDocument, 'GeographicalDistrict');
        
        $districtTitle = new Zend_Form_Element_Text('districtTitle');
        $districtTitle->setLabel('DistrictTitle')
                      ->setRequired(true)
                      ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'DISTRICT_TITLE'))
                      ->setDecorators(array('CompositeElement'));
        
        $mainElements = array($districtTitle);
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
            array('connect' => array($districtTitle->getName(), array('GeographicalDistrict', 'DISTRICT_TITLE')),),
            array('connect' => array($note->getName(), array('GeographicalDistrict', 'DISTRICT_NOTICE')),),
        );
        
    }
}