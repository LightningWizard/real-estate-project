<?php
class RealEstate_Blank_Specialization extends Lis_Blank_Abstract
{
    public function init()
    {
        $this->setAttrib('id', 'blank-specialization')
             //->setAttrib('onsubmit', 'return false;')
             ->setMethod('post');
        $this->addDocument(new RealEstate_Document_Specialization(), 'Specialization');

        $title = new Zend_Form_Element_Textarea('title');
        $title->setRequired(true)
              ->setLabel('Title')
              ->setDecorators(array('CompositeElement'));

        $titleShort = new Zend_Form_Element_Text('titleShort');
        $titleShort->setLabel('ShortTitle')
                   ->setDecorators(array('CompositeElement'));

        $parentSpecializationId = new Zend_Form_Element_Hidden('parentSpecializationId');
        $parentSpecializationId->setDecorators(array('ViewHelper'));

        $parentSpecializationTitle = new Zend_Form_Element_Textarea('parentSpecializationTitle');
        $parentSpecializationTitle->setLabel('ParentSpecialization')
                              ->setDecorators(array('CompositeElement'));

        $this->addElement($title)
             ->addElement($titleShort)
             ->addElement($parentSpecializationId)
             ->addElement($parentSpecializationTitle)
             ->addDisplayGroup(array('title', 'titleShort',  'parentSpecializationId', 'parentSpecializationTitle'), 'main', array('legend'=>'MainData', 'class'=>'fieldset-visible'));

        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'));
        $this->addElement($note);
        $this->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array('title', array('Specialization', 'SPECIALIZATION_TITLE')),),
            array('connect' => array('titleShort', array('Specialization', 'SPECIALIZATION_TITLE_SHORT')),),
            array('connect' => array('parentSpecializationId', array('Specialization', 'SPECIALIZATION_ID_PARENT')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('note', array('Specialization', 'SPECIALIZATION_NOTICE')),),
         );
    }
    public function update($blankWithDocuments = true)
    {
        parent::update($blankWithDocuments);
        if (true == $blankWithDocuments) {
            if (null !== ($parentSpecializationId = $this->getDocument('Specialization')->SPECIALIZATION_ID_PARENT)) {
                $parentSpecialization = new RealEstate_Document_Specialization();
                $parentSpecialization->load($parentSpecializationId);
                $this->getElement('parentSpecializationTitle')->setValue($parentSpecialization->SPECIALIZATION_TITLE);
                unset($parentSpecialization);
            } else {
                $this->getElement('parentSpecializationTitle')->setValue('');
            }
        }
    }
}