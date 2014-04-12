<?php
class RealEstate_Blank_Department extends Lis_Blank_Abstract
{
    public function init()
    {
        $this->setAttrib('id', 'blank-department')
             //->setAttrib('onsubmit', 'return false;')
             ->setMethod('post');
        $this->addDocument(new RealEstate_Document_Department(), 'Department');

        $title = new Zend_Form_Element_Textarea('title');
        $title->setRequired(true)
              ->setLabel('Title')
              ->setDecorators(array('CompositeElement'));

        $titleShort = new Zend_Form_Element_Text('titleShort');
        $titleShort->setLabel('ShortTitle')
                   ->setDecorators(array('CompositeElement'));

        $parentDepartmentId = new Zend_Form_Element_Hidden('parentDepartmentId');
        $parentDepartmentId->setDecorators(array('ViewHelper'));

        $parentDepartmentTitle = new Zend_Form_Element_Textarea('parentDepartmentTitle');
        $parentDepartmentTitle->setLabel('ParentDepartment')
                              ->setDecorators(array('CompositeElement'));

        $this->addElement($title)
             ->addElement($titleShort)
             ->addElement($parentDepartmentId)
             ->addElement($parentDepartmentTitle)
             ->addDisplayGroup(array('title', 'titleShort',  'parentDepartmentId', 'parentDepartmentTitle'), 'main', array('legend'=>'MainData', 'class'=>'fieldset-visible'));

        $note = new Zend_Form_Element_Textarea('note');
        $note->setDecorators(array('ViewHelper'));
        $this->addElement($note);
        $this->addDisplayGroup(array('note'), 'notice', array('legend'=>'Notice', 'class'=>'fieldset-visible'));

        $this->_reflactions = array(
            array('connect' => array('title', array('Department', 'DEPARTMENT_TITLE')),),
            array('connect' => array('titleShort', array('Department', 'DEPARTMENT_TITLE_SHORT')),),
            array('connect' => array('parentDepartmentId', array('Department', 'DEPARTMENT_ID_PARENT')), 'reflector'=>'Lis_Blank_Reflector_ZeroInBlankIsNullInDb'),
            array('connect' => array('note', array('Department', 'DEPARTMENT_NOTICE')),),
         );
    }
    public function update($blankWithDocuments = true)
    {
        parent::update($blankWithDocuments);
        if (true == $blankWithDocuments) {
            if (null !== ($parentDepartmentId = $this->getDocument('Department')->DEPARTMENT_ID_PARENT)) {
                $parentDepartment = new RealEstate_Document_Department();
                $parentDepartment->load($parentDepartmentId);
                $this->getElement('parentDepartmentTitle')->setValue($parentDepartment->DEPARTMENT_TITLE);
            }
        }
    }
}