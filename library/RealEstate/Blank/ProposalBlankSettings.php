<?php

class RealEstate_Blank_ProposalBlankSettings extends Lis_Blank_Abstract {
    public function init() {
       $this->setAttrib('id', 'proposal-blank-settings')
            ->setMethod('post');

        $blankDocument = new RealEstate_Document_ProposalBlankSettings();
        $this->addDocument($blankDocument, 'ProposalBlankSettings');

        $realEstateTypeId = new Zend_Form_Element_Hidden('realEstateTypeId');
        $realEstateTypeId->setDecorators(array('ViewHelper'));
        
        $realEstateTypeTitle = new Zend_Form_Element_Text('realEstateType');
        $realEstateTypeTitle->setLabel('RealEstateType')
                ->setAttrib('readonly', 'readonly')
                ->setRequired(true)
                ->setDecorators(array('CompositeElement'));

        $mainElements = array($realEstateTypeId, $realEstateTypeTitle);
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
             ->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));
        
        $this->_reflactions = array(
            array('connect' => array(
                    $realEstateTypeId->getName(),
                    array('ProposalBlankSettings', 'REALESTATETYPE_ID')),
                    'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'
                ),
            array('connect' => array($note->getName(), array('ProposalBlankSettings', 'SETTINGSGROUP_NOTICE')),),
        );
    }
    
    public function update($blankWithDocuments = true) {
        parent::update($blankWithDocuments);
        if($blankWithDocuments === true) {
            $document = $this->getDocument('ProposalBlankSettings');
            if($document->REALESTATETYPE_ID){
                $this->getElement('realEstateType')->setValue($document->REALESTATETYPE_TITLE);
            }
        }
    }
}