<?php

class RealEstate_Blank_RealEstatePurpose extends Lis_Blank_Abstract {

      public function init() {
        $this->setAttrib('id', 'blank-real-estate-purpose')
                ->setMethod('post');

        $blankDocument = new RealEstate_Document_RealEstatePurpose();
        $this->addDocument($blankDocument, 'RealEstatePurpose');

        $realEstatePurposeTitle = new Zend_Form_Element_Text('realEstatePurpose');
        $realEstatePurposeTitle->setLabel('Title')
                ->setRequired(true)
                ->setDecorators(array('CompositeElement'));

        $realEstateTypeId = new Zend_Form_Element_Hidden('realEstateTypeId');
        $realEstateTypeId->setDecorators(array('ViewHelper'));

        $realEstateTypeTitle = new Zend_Form_Element_Text('realEstateType');
        $realEstateTypeTitle->setLabel('RealEstateType')
                            ->setRequired(true)
                            ->setDecorators(array('CompositeElement'));

        $mainElements = array($realEstatePurposeTitle, $realEstateTypeId, $realEstateTypeTitle);
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
            array('connect' => array($realEstatePurposeTitle->getName(), array('RealEstatePurpose', 'PURPOSE_TITLE')),),
            array('connect' => array($realEstateTypeId->getName(), array('RealEstatePurpose', 'REALESTATETYPE_ID')),),
            array('connect' => array($note->getName(), array('RealEstatePurpose', 'PURPOSE_NOTICE')),),
        );
      }

      public function update($blankWithDocuments = true) {
          parent::update($blankWithDocuments);
          if($blankWithDocuments === true) {
              $document = $this->getDocument('RealEstatePurpose');
              if($document->REALESTATETYPE_ID !== null) {
                  $realEstateType = new RealEstate_Document_RealEstateType();
                  $realEstateType->load($document->REALESTATETYPE_ID);
                  $this->getElement('realEstateType')->setValue($realEstateType->REALESTATETYPE_TITLE);
                  unset($realEstateType);
              }
          }
      }
}