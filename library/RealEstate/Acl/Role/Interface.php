<?php
interface RealEstate_Acl_Role_Interface extends Zend_Acl_Role_Interface
{
    /**
     * Returns the string title of the Role
     *
     * @return string
     */
    public function getTitle();
    /**
     * Returns the string indetifire of the Department for which Role has been created
     *
     * @return string
     */
    public function getDepartmentId();
    /**
     * Returns the object of the Department for which Role has been created
     *
     * @return TestCenter_Document_Department
     */
    public function getDepartment();
    /**
     * Returns the string indetifire of the User's Specialization for which Role has been created
     *
     * @return string
     */
    public function getSpecializationId();
    /**
     * Returns the object of the User's Specialization for which Role has been created
     *
     * @return TestCenter_Document_Specialization
     */
    public function getSpecialization();
}