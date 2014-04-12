<?php

class RealEstate_Blank_PlanningType extends Lis_Blank_Abstract {
    public function init() {
        $this->setAttrib('id', 'blank-planning-type')
                ->setMethod('post');

        $blankDocument = new RealEstate_Document_PlanningType();
        $this->addDocument($blankDocument, 'PlanningType');

        $planningTypeTitle = new Zend_Form_Element_Text('planningTypeTitle');
        $existsValidator = new RealEstate_Validate_NoDbRecordExists($blankDocument, 'PLANNINGTYPE_TITLE');
        $planningTypeTitle->setLabel('Title')
                ->setRequired(true)
                ->addValidator($existsValidator)
                ->setDecorators(array('CompositeElement'));

        $mainElements = array($planningTypeTitle);
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
            array('connect' => array($planningTypeTitle->getName(), array('PlanningType', 'PLANNINGTYPE_TITLE')),),
            array('connect' => array($note->getName(), array('PlanningType', 'PLANNINTGTYPE_NOTICE')),),
        );
    }
}