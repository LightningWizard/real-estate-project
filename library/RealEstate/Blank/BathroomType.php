<?php

class RealEstate_Blank_BathroomType extends Lis_Blank_Abstract{

    public function init() {
        $this->setAttrib('id', 'blank-bathroom-type')
             ->setMethod('post');

        $blankDocument = new RealEstate_Document_BathroomType();
        $this->addDocument($blankDocument, 'BathroomType');

        $bathroomTypeTitle = new Zend_Form_Element_Text('bathroomType');
        $bathroomTypeTitle->setLabel('BathroomType')
                          ->setRequired(true)
                          ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'BATHROOMTYPE_TITLE'))
                          ->setDecorators(array('CompositeElement'));

        $mainElements = array(
            $bathroomTypeTitle
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
            array('connect' => array($bathroomTypeTitle->getName(), array('BathroomType', 'BATHROOMTYPE_TITLE')),),
            array('connect' => array($note->getName(), array('BathroomType', 'BATHROOMTTYPE_NOTICE')),),
        );
    }
}