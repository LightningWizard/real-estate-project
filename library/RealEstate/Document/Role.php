<?php
class RealEstate_Document_Role extends Lis_Document_Abstract
{
    protected $_permissions = array();

    public function  __construct($config = null, $primary = null)
    {
        parent::__construct($config, $primary);
        $this->_permissions = array();
    }
    public function  instantiate()
    {
        parent::instantiate();
        $this->_permissions = array();
        return $this;
    }
    public function load($primary, $refresh = false)
    {
        parent::load($primary, $refresh);
        $this->loadPermissions();
        return $this;
    }
    public function loadPermissions()
    {
        if ($this->ROLE_ID) {
            $db = $this->getCollection()->getAdapter();
            $select = $db->select()
                         ->from('TBL_SEC_ROLE_PERM', array('RESOURCE_ID', 'PERM_READ', 'PERM_EDIT', 'PERM_REMOVE', 'PERM_EXECUTE', 'PERM_EXTRIM'))
                         ->where('ROLE_ID=?', $this->ROLE_ID);
            foreach ($db->fetchAll($select) as $perm) {
                $this->_permissions[$perm['RESOURCE_ID']] = array(
                    'PERM_READ' => (bool) $perm['PERM_READ'],
                    'PERM_EDIT' => (bool) $perm['PERM_EDIT'],
                    'PERM_REMOVE' => (bool) $perm['PERM_REMOVE'],
                    'PERM_EXECUTE' => (bool) $perm['PERM_EXECUTE'],
                    'PERM_EXTRIM' => (bool) $perm['PERM_EXTRIM'],
                );
            }
        } else {
            $this->_permissions = array();
        }
    }
    public function toArray()
    {
        $data = parent::toArray();
        $data['PERMISSIONS'] = $this->_permissions;
        return $data;
    }
    public function save()
    {
        $db = $this->getCollection()->getAdapter();
        try {
            $db->beginTransaction();
            parent::save();
            $db->delete('TBL_SEC_ROLE_PERM', array('ROLE_ID=?' => $this->ROLE_ID));
            foreach ($this->_permissions as $resourceHashId => $perm) {
                $db->insert('TBL_SEC_ROLE_PERM', array(
                    'ROLE_ID' => $this->ROLE_ID,
                    'RESOURCE_ID' => $resourceHashId,
                    'PERM_READ' => (int) $perm['PERM_READ'],
                    'PERM_EDIT' => (int) $perm['PERM_EDIT'],
                    'PERM_REMOVE' => (int) $perm['PERM_REMOVE'],
                    'PERM_EXECUTE' => (int) $perm['PERM_EXECUTE'],
                    'PERM_EXTRIM' => (int) $perm['PERM_EXTRIM'],
                ));
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    public function resetPermissions()
    {
        $this->_permissions = array();
        return $this;
    }
    public function setPermission($resourceIdHash, $canRead = false, $canEdit = false, $canRemove = false, $canExecute = false, $canExtrimalEdit = false)
    {
        $this->_permissions[$resourceIdHash] = array(
            'PERM_READ' => (bool) $canRead,
            'PERM_EDIT' => (bool) $canEdit,
            'PERM_REMOVE' => (bool) $canRemove,
            'PERM_EXECUTE' => (bool) $canExecute,
            'PERM_EXTRIM' => (bool) $canExtrimalEdit
        );
    }

    public function canRead($resourceIdHash)
    {
        return array_key_exists($resourceIdHash, $this->_permissions) && $this->_permissions[$resourceIdHash]['PERM_READ'];
    }
    public function canEdit($resourceIdHash)
    {
        return array_key_exists($resourceIdHash, $this->_permissions) && $this->_permissions[$resourceIdHash]['PERM_EDIT'];
    }
    public function canRemove($resourceIdHash)
    {
        return array_key_exists($resourceIdHash, $this->_permissions) && $this->_permissions[$resourceIdHash]['PERM_REMOVE'];
    }
    public function canExecute($resourceIdHash)
    {
        return array_key_exists($resourceIdHash, $this->_permissions) && $this->_permissions[$resourceIdHash]['PERM_EXECUTE'];
    }
    public function canExtrimalEdit($resourceIdHash)
    {
        return array_key_exists($resourceIdHash, $this->_permissions) && $this->_permissions[$resourceIdHash]['PERM_EXTRIM'];
    }
}