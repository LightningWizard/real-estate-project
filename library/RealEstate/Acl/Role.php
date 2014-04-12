<?php
class RealEstate_Acl_Role extends Zend_Acl_Role implements RealEstate_Acl_Role_Interface
{
    /**
     * Role Card (system document on which role is based)
     * @var RealEstate_Document_Role
     */
    protected $_card;
    /**
     * Department for which Role has been created
     * @var RealEstate_Document_Department
     */
    protected $_department;
    /**
     * User's Specialization for which Role has been created
     * @var RealEstate_Document_Specialization
     */
    protected $_specialization;

    /**
     * Sets the Role Card (system document on which role is based)
     *
     * @param  RealEstate_Document_Role $roleCard
     * @return void
     */
    public function __construct(RealEstate_Document_Role $roleCard)
    {
        parent::__construct($roleCard->ROLE_ID);
        if (empty($roleCard->ROLE_TITLE)) {
            throw new RealEstate_Acl_Role_Exception('Try to create Role without title');
        }
        if (empty($roleCard->DEPARTMENT_ID)) {
            throw new RealEstate_Acl_Role_Exception('Try to create Role without department');
        }
        if (empty($roleCard->SPECIALIZATION_ID)) {
            throw new RealEstate_Acl_Role_Exception('Try to create Role without specialization');
        }
        $this->_card = $roleCard;
        $this->_department = null;
        $this->_specialization = null;
    }

    /**
     * Returns the string title of the Role
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_card->ROLE_TITLE;
    }
    /**
     * Returns the string indetifire of the Department for which Role has been created
     *
     * @return string
     */
    public function getDepartmentId()
    {
        return $this->_card->DEPARTMENT_ID;
    }
    /**
     * Returns the object of the Department for which Role has been created
     *
     * @return RealEstate_Document_Department
     */
    public function getDepartment()
    {
        if (null === $this->_department) {
            $department = new RealEstate_Document_Department();
            $department->load($this->getDepartmentId());
            $this->_department = $department;
        }
        return $this->_department;
    }
    /**
     * Returns the string indetifire of the User's Specialization for which Role has been created
     *
     * @return string
     */
    public function getSpecializationId()
    {
        return $this->_card->SPECIALIZATION_ID;
    }
    /**
     * Returns the object of the User's Specialization for which Role has been created
     *
     * @return RealEstate_Document_Specialization
     */
    public function getSpecialization()
    {
        if (null === $$this->_specialization) {
            $specialization = new RealEstate_Document_Specialization();
            $specialization->load($this->getSpecializationId());
            $this->_specialization = $specialization;
        }
        return $this->_specialization;
    }

    public function  __call($name, $arguments)
    {
        return call_user_func_array(array($this->_card, $name), $arguments);
    }
}