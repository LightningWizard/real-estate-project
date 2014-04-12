<?php

class RealEstate_Blank_ProposalStatus extends Lis_Blank_Abstract {

    public function init() {
        $this->setAttrib('id', 'blank-proposal-status')
                ->setMethod('post');

        $blankDocument = new RealEstate_Document_ProposalStatus();
        $this->addDocument($blankDocument, 'ProposalStatus');

        $proposalStatusTitle = new Zend_Form_Element_Text('proposalStatus');
        $proposalStatusTitle->setLabel('Title')
                ->setRequired(true)
                ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'PROPOSALSTATUS_TITLE'))
                ->setDecorators(array('CompositeElement'));

        $mainElements = array($proposalStatusTitle);
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
            array('connect' => array($proposalStatusTitle->getName(), array('ProposalStatus', 'PROPOSALSTATUS_TITLE')),),
            array('connect' => array($note->getName(), array('ProposalStatus', 'PROPOSALSTATUS_NOTICE')),),
        );
    }
}