<?php

class RealEstate_Blank_HeatingType extends Lis_Blank_Abstract{

    public function init() {
        $this->setAttrib('id', 'blank-heating-type')
             ->setMethod('post');

        $blankDocument = new RealEstate_Document_HeatingType();
        $this->addDocument($blankDocument, 'HeatingType');

        $heatingTypeTitle = new Zend_Form_Element_Text('heatingTypeTitle');
        $heatingTypeTitle->setLabel('Title')
                         ->setRequired(true)
                         ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'HEATINGTYPE_TITLE'))
                         ->setDecorators(array('CompositeElement'));

        $mainElements = array($heatingTypeTitle);
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
            array('connect' => array($heatingTypeTitle->getName(), array('HeatingType', 'HEATINGTYPE_TITLE')),),
            array('connect' => array($note->getName(), array('HeatingType', 'HEATINGTYPE_NOTICE')),),
        );
    }
}