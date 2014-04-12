<?php
interface RealEstate_Acl_Resource_Interface extends Zend_Acl_Resource_Interface
{
    /**
     * Returns the string title of the Resource
     *
     * @return string
     */
    public function getTitle();

    /**
     * Check Resource type is readable or not
     *
     * @return bool
     */
    public function hasReadMode();
    /**
     * Check Resource type can be edited (create or update) or not
     *
     * @return bool
     */
    public function hasEditMode();
    /**
     * Check Resource type can be removed or not
     *
     * @return bool
     */
    public function hasRemoveMode();
    /**
     * Check Resource type can be executed or not
     *
     * @return bool
     */
    public function hasExecuteMode();
    /**
     * Check has can Resource can be edited in extrim mode or not
     *
     * @return bool
     */
    public function hasExtrimMode();
    /**
     * Returns parent Resource
     *
     * @return RealEstate_Acl_Resource_Interface
     */
    
    public function getParent();
    /**
     * Returns array of slave Resources
     *
     * @return array of RealEstate_Acl_Resource_Interface
     */
    public function getSubResources();
}