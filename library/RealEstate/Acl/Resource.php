<?php
class RealEstate_Acl_Resource extends Zend_Acl_Resource implements RealEstate_Acl_Resource_Interface, RecursiveIterator
{
    /**
     * Title of Resource
     *
     * @var string
     */
    protected $_title;
    /**
     * Array of available acces modes
     *
     * @var array
     */
    protected $_modes = array(
        'read' => false,
        'edit' => false,
        'remove' => false,
        'execute' => false,
        'extrim' => false,
    );

    /**
     * Parent Resource
     *
     * @var RealEstate_Acl_Resource
     */
    protected $_parent;
    /**
     * Array of children Resources
     *
     * @var array of RealEstate_Acl_Resource
     */
    protected $_children = array();

    /**
     * Sets the Resource identifier, title and available access modes
     *
     * @param  string $resourceId
     * @return void
     */
    public function __construct($resourceId, $title, $modes)
    {
        $modes = (array)$modes;

        $this->_resourceId = (string) $resourceId;
        $this->_title = (string) $title;
        $this->_modes = array(
            'read' => array_key_exists('read', $modes) ? (bool)$modes['read'] : false,
            'edit' => array_key_exists('edit', $modes) ? (bool)$modes['edit'] : false,
            'remove' => array_key_exists('remove', $modes) ? (bool)$modes['remove'] : false,
            'execute' => array_key_exists('execute', $modes) ? (bool)$modes['execute'] : false,
            'extrim' => array_key_exists('extrim', $modes) ? (bool)$modes['extrim'] : false,
        );
    }

    /**
     * Set parent resource
     *
     * @param RealEstate_Acl_Resource $parent
     * @return RealEstate_Acl_Resource
     */
    public function setParent(RealEstate_Acl_Resource $parent)
    {
        if (null !== $this->_parent) {
            throw new RealEstate_Acl_Resource_Exception('Changing resource parent is not allowed');
        }
        $this->_parent = $parent;
        if (null !== $this->_parent) {
            $this->_parent->addSubResource($this);
        }
        return $this;
    }
    /**
     * Add child Resource
     *
     * @param RealEstate_Acl_Resource $children
     * @return RealEstate_Acl_Resource
     */
    public function addSubResource(RealEstate_Acl_Resource $child)
    {
        if ($child->getParent() !== $this) {
            $child->setParent($this);
        } else if (!array_key_exists($child->getResourceId(), $this->_children)) {
            $this->_children[$child->getResourceId()] = $child;
        }
        return $this;
    }

    /**
     * Returns the string title of the Resource
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Check Resource type is readable or not
     *
     * @return bool
     */
    public function hasReadMode()
    {
        return $this->_modes['read'];
    }
    /**
     * Check Resource type can be edited (create or update) or not
     *
     * @return bool
     */
    public function hasEditMode()
    {
        return $this->_modes['edit'];
    }
    /**
     * Check Resource type can be removed or not
     *
     * @return bool
     */
    public function hasRemoveMode()
    {
        return $this->_modes['remove'];
    }
    /**
     * Check Resource type can be executed or not
     *
     * @return bool
     */
    public function hasExecuteMode()
    {
        return $this->_modes['execute'];
    }
    /**
     * Check has can Resource can be edited in extrim mode or not
     *
     * @return bool
     */
    public function hasExtrimMode()
    {
        return $this->_modes['extrim'];
    }
    /**
     * Returns parent Resource
     *
     * @return RealEstate_Acl_Resource_Interface
     */
    public function getParent()
    {
        return $this->_parent;
    }
    /**
     * Returns array of slave Resources
     *
     * @return array of RealEstate_Acl_Resource_Interface
     */
    public function getSubResources()
    {
        return $this->_children;
    }

    // RecursiveIterator interface:

    /**
     * Returns current slave resource
     *
     * Implements RecursiveIterator interface.
     *
     * @return RealEstate_Acl_Resource   current salave resource or null
     */
    public function current()
    {
        return current($this->_children);
    }

    /**
     * Returns hash code of current slave resource
     *
     * Implements RecursiveIterator interface.
     *
     * @return string  hash code of current salve resource
     */
    public function key()
    {
        return key($this->_children);
    }

    /**
     * Moves index pointer to next slave resource in the container
     *
     * Implements RecursiveIterator interface.
     *
     * @return void
     */
    public function next()
    {
        next($this->_children);
    }

    /**
     * Sets index pointer to first slave resource in the container
     *
     * Implements RecursiveIterator interface.
     *
     * @return void
     */
    public function rewind()
    {
        reset($this->_children);
    }

    /**
     * Checks if container index is valid
     *
     * Implements RecursiveIterator interface.
     *
     * @return bool
     */
    public function valid()
    {
        return current($this->_children) !== false;
    }

    /**
     * Implements RecursiveIterator interface.
     *
     * @return bool  whether container has any children resource
     */
    public function hasChildren()
    {
        return (bool)count($this->_children);
    }

    /**
     * Returns the child resource.
     *
     * Implements RecursiveIterator interface.
     *
     * @return Zend_Acl_Resource|null
     */
    public function getChildren()
    {
        return current($this->_children);
    }
}