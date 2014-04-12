<?php
class RealEstate_Blank_Role extends Lis_Blank_Abstract
{
    public function init()
    {
        $this->setAttrib('id', 'blank-role')
             ->setMethod('post')
             ->addDocument(new RealEstate_Document_Role, 'Role');

        $title = new Zend_Form_Element_Textarea('title');
        $title->setRequired(true)
              ->addValidator(new Zend_Validate_StringLength(0, 250))
              ->setLabel('Title')
              ->setDecorators(array('CompositeElement'));
        $departmentId = new Zend_Form_Element_Hidden('departmentId');
        $departmentId->setDecorators(array('ViewHelper'));
        $departmentTitle = new Zend_Form_Element_Textarea('departmentTitle');
        $departmentTitle->setRequired(true)
                        ->setLabel('Department')
                        ->setDecorators(array('CompositeElement'));
        $specializationId = new Zend_Form_Element_Hidden('specializationId');
        $specializationId->setDecorators(array('ViewHelper'));
        $specializationTitle = new Zend_Form_Element_Textarea('specializationTitle');
        $specializationTitle->setRequired(true)
                            ->setLabel('Specialization')
                            ->setDecorators(array('CompositeElement'));
        $this->addElement($title)
             ->addElement($departmentId)
             ->addElement($departmentTitle)
             ->addElement($specializationId)
             ->addElement($specializationTitle)
             ->addDisplayGroup(array(
                 $title->getName(),
                 $departmentId->getName(),
                 $departmentTitle->getName(),
                 $specializationId->getName(),
                 $specializationTitle->getName()), 'main', array('legend'=>'MainData', 'class'=>'fieldset-visible'));

        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'));
        $this->addElement($note)
             ->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array($title->getName(), array('Role', 'ROLE_TITLE')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($departmentId->getName(), array('Role', 'DEPARTMENT_ID')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($specializationId->getName(), array('Role', 'SPECIALIZATION_ID')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array($note->getName(), array('Role', 'ROLE_NOTICE')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
        );
    }
    public function update($blankWithDocuments = true)
    {
        parent::update($blankWithDocuments);
        if ($blankWithDocuments) {
            $document = $this->getDocument('Role');
            if ($document->DEPARTMENT_ID) {
                $department = new RealEstate_Document_Department();
                $department->load($document->DEPARTMENT_ID);
                $this->getElement('departmentTitle')->setValue($department->DEPARTMENT_TITLE)
                                                    ->setAttrib('href', '/administration/departments/item/id/' . $document->DEPARTMENT_ID);
                unset($department);
            } else {
                $this->getElement('departmentTitle')->setValue('')
                                                    ->setAttrib('href', null);
            }
            if ($document->SPECIALIZATION_ID) {
                $spacialization = new RealEstate_Document_Specialization();
                $spacialization->load($document->SPECIALIZATION_ID);
                $this->getElement('specializationTitle')->setValue($spacialization->SPECIALIZATION_TITLE)
                                                        ->setAttrib('href', '/administration/specializations/item/id/' . $document->SPECIALIZATION_ID);
                unset($spacialization);
            } else {
                $this->getElement('specializationTitle')->setValue('')
                                                        ->setAttrib('href', null);
            }
        }
    }
}