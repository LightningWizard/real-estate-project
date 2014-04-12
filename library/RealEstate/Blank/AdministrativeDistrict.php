<?php

class RealEstate_Blank_AdministrativeDistrict extends Lis_Blank_Abstract{
    
    public function init() {
        $this->setAttrib('id', 'blank-administrative-district')
             ->setMethod('post');
        $blankDocument = new RealEstate_Document_AdministrativeDistrict();
        $this->addDocument($blankDocument, 'AdministrativeDistrict');
        
        $districtTitle = new Zend_Form_Element_Text('districtTitle');
        $districtTitle->setLabel('DistrictTitle')
                      ->setRequired(true)
                      ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'DISTRICT_TITLE'))
                      ->setDecorators(array('CompositeElement'));
        
        $districtTitleShort = new Zend_Form_Element_Text('districtShortTitle');
        $districtTitleShort->setLabel('ShortTitle')
                      ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'DISTRICT_SHORTTITLE'))
                      ->setDecorators(array('CompositeElement'));
        
        $mainElements = array($districtTitle, $districtTitleShort);
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
            array('connect' => array($districtTitle->getName(), array('AdministrativeDistrict', 'DISTRICT_TITLE')),),
            array('connect' => array($districtTitleShort->getName(), array('AdministrativeDistrict', 'DISTRICT_SHORTTITLE')),),
            array('connect' => array($note->getName(), array('AdministrativeDistrict', 'DISTRICT_NOTICE')),),
        );
    }
}