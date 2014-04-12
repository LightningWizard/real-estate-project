<?php
class RealEstate_Blank_Street extends Lis_Blank_Abstract {
    public function init() {
        $this->setAttrib('id', 'blank-street')
             ->setMethod('post');
        $this->addDocument(new RealEstate_Document_Street(), 'Street');

        $streetTitle = new Zend_Form_Element_Text('streetTitle');
        $streetTitle->setLabel('Title')
                    ->setDecorators(array('CompositeElement'));

        $streetTypeId = new Zend_Form_Element_Hidden('streetTypeId');
        $streetTypeId->setDecorators(array('ViewHelper'));

        $streetType = new Zend_Form_Element_Text('streetType');
        $streetType->setLabel('StreetType')
                   ->setAttrib('readonly', 'readonly')
                   ->setDecorators(array('CompositeElement'));

        $settlementId = new Zend_Form_Element_Hidden('settlementId');
        $settlementId->setDecorators(array('ViewHelper'));

        $settlementTitle = new Zend_Form_Element_Text('settlement');
        $settlementTitle->setLabel('Settlement')
             ->setAttrib('readonly', 'readonly')
             ->setDecorators(array('CompositeElement'));

        $mainElements = array($streetTitle, $streetTypeId, $streetType,
                              $settlementId, $settlementTitle);
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
            array('connect' => array($streetTitle->getName(), array('Street', 'STREET_TITLE')),),
            array('connect' => array($streetTypeId->getName(), array('Street', 'STREETTYPE_ID')),),
            array('connect' => array($settlementId->getName(), array('Street', 'SETTLEMENT_ID')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($note->getName(), array('Street', 'STREET_NOTICE')),),
        );

    }

    public function update($blankWithDocuments = true) {
        parent::update($blankWithDocuments);
        if($blankWithDocuments == true) {
            $document = $this->getDocument('Street');
            if($document->STREET_ID !== null) {
                if($document->SETTLEMENT_ID !== null) {
                    $settlement = new RealEstate_Document_Settlement();
                    $settlement->load($document->SETTLEMENT_ID);
                    $this->getElement('settlement')->setValue($settlement->SETTLEMENT_TITLE);
                }
                if($document->STREETTYPE_ID !== null) {
                    $streetType = new RealEstate_Document_StreetType();
                    $streetType->load($document->STREETTYPE_ID);
                    $this->getElement('streetType')->setValue($streetType->STREETTYPE_TITLE);
                }
            } else {

            }
        }
    }
}