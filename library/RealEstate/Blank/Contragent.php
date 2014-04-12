<?php
class RealEstate_Blank_Contragent extends Lis_Blank_Abstract {
    public function init() {
        $this->setAttrib('id', 'blank-contragent')
             ->setMethod('post');

        $blankDocument = new RealEstate_Document_Contragent();
        $this->addDocument($blankDocument, 'Contragent');

        $contragentTitle = new Zend_Form_Element_Textarea('contragentTitle');
        $contragentTitle->setLabel('ContragentTitle')
                        ->setRequired(true)
                        ->addValidator(new RealEstate_Validate_NoDbRecordExists($blankDocument, 'CONTRAGENT_TITLE'))
                        ->setDecorators(array('CompositeElement'));

        $reflector = new RealEstate_Grid_Reflector_ContragentType();
        $types = RealEstate_Document_Contragent::getContragentTypes();
        $contragentType = new RealEstate_Form_Element_Select('contragentType');
        $contragentType->setLabel('ContragentType')
                       ->setDecorators(array('CompositeElement'))
                       ->addMultiOption('','')
                       ->addReflectedOptions($types, $reflector);
        unset($reflector);

        $contragentWebsite = new Zend_Form_Element_Text('website');
        $contragentWebsite->setLabel('WebSite')
                          ->setDecorators(array('CompositeElement'))
                          ->addValidator(new RealEstate_Validate_Uri());

        $contragentAddress = new Zend_Form_Element_Textarea('contragentAddress');
        $contragentAddress->setLabel('Address')
                          ->setDecorators(array('CompositeElement'));

        $contragentPhones = new Zend_Form_Element_Hidden('contragentPhone');
        $contragentPhones->setLabel('Phones')
                         ->setDecorators(array('CompositeElement'));

        $mainElements = array(
            $contragentTitle, $contragentType, $contragentWebsite, $contragentAddress, $contragentPhones
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
        $note->setDecorators(array('ViewHelper'))
             ->addValidator(new Zend_Validate_StringLength(array('min' => 0, 'max' => 250)));

        $this->addElement($note)
             ->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array($contragentTitle->getName(), array('Contragent', 'CONTRAGENT_TITLE')),),
            array('connect' => array($contragentType->getName(), array('Contragent', 'CONTRAGENT_TYPE')),),
            array('connect' => array($contragentWebsite->getName(), array('Contragent', 'CONTRAGENT_SITE')),),
            array('connect' => array($contragentAddress->getName(), array('Contragent', 'CONTRAGENT_ADDRESS')),),
            array('connect' => array($note->getName(), array('Contragent', 'CONTRAGENT_NOTICE')),),
        );

    }
}