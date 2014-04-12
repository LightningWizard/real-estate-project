<?php
class Lis_Controller_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Default ACL for using in helper
     * @var Zend_Acl
     */
    static protected $_defaultAcl = null;
    static protected $_defaultRole = null;
    /**
     * Set default helper ACL
     * @param Zend_Acl $acl
     * @return void
     */
    static public function setDefaultAcl(Zend_Acl $acl)
    {
        if ($acl instanceof Zend_Acl) {
            self::$_defaultAcl = $acl;
        } else {
            throw new Exception('Invalid parameter');
        }
    }
    /**
     * Returns default ACL for helper
     * @return Zend_Acl|null
     */
    static public function getDefaultAcl()
    {
        if (null !== self::$_defaultAcl) {
            return self::$_defaultAcl;
        } else if (Zend_Registry::isRegistered('Zend_Acl')) {
            return Zend_Registry::get('Zend_Acl');
        } else {
            return null;
        }
    }
    /**
     * Set default ACL Role for helper
     * @param string|null $role
     */
    static public function setDefaultRole($role)
    {
        if (is_string($role) || $role === null) {
            self::$_defaultRole = $role;
        } else {
            throw new Exception('Invalid parameter');
        }
    }
    /**
     * Returns default ACL Role
     * @return string|null
     */
    static public function getDefaultRole()
    {
        return self::$_defaultRole;
    }

    /**
     * ACL
     * @var Zend_Acl;
     */
    protected $_acl = null;
    /**
     * Role
     * @var string
     */
    protected $_role = null;
    /**
     * Set ACL
     * @param Zend_Acl $acl
     */
    public function setAcl(Zend_Acl $acl)
    {
        if ($acl instanceof Zend_Acl) {
            $this->_acl = $acl;
        } else {
            throw new Exception('Invalid parameter');
        }
    }
    /**
     * Returns ACL
     * @return Zend_Acl|Null
     */
    public function getAcl()
    {
        if (null !== $this->_acl) {
            return $this->_acl;
        } else {
            return self::getDefaultAcl();
        }
    }
    /**
     * Set ACL Role
     * @param string|null $role
     */
    public function setRole($role)
    {
        if (is_string($role) || $role === null) {
            $this->_role = $role;
        } else {
            throw new Exception('Invalid parameter');
        }
    }
    /**
     * Returns ACL Role
     * @return string|null
     */
    public function getRole()
    {
        if (null !== $this->_role) {
            return $this->_role;
        } else {
            return self::getDefaultRole();
        }
    }
    /**
     * Test is $resource is allowed for role with $privilege
     *
     * @param Zend_Acl_Resource_Interface|string|null $resource
     * @param string|null $privilege
     * @return bool
     */
    public function assert($resource = null, $privilege = null)
    {
        $acl = $this->getAcl();
        if (null === $acl) {
            throw new Exception('ACL does not specified');
        }

        $isAllowed = false;
        try {
            $isAllowed = $acl->isAllowed($this->getRole(), $resource, $privilege);
        } catch (Exception $e) {
            $isAllowed = false;
        }
        return $isAllowed;
    }
    /**
     * Is an alias for Lis_Controller_Action_Helper_Acl::assert
     *
     * Used by HelperBrocker for accessing to helper as method
     *
     * Test is $resource is allowed for role with $privilege
     *
     * @param Zend_Acl_Resource_Interface|string|null $resource
     * @param string|null $privilege
     * @return bool
     */
    public function direct($resource = null, $privilege = null)
    {
        return $this->assert($resource, $privilege);
    }
}