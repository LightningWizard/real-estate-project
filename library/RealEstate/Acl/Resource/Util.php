<?php
class RealEstate_Acl_Resource_Util
{
    /**
     * Extracts Resources from Navigation and add some admixture for special resources and returns single global resource object
     *
     * @param Zend_Navigation_Container
     * @return RealEstate_Acl_Resource
     */
    static public function extractFromNavigation(Zend_Navigation_Container $navigation)
    {
        $globalResource = new RealEstate_Acl_Resource('[GLOBAL_RESOURCE]', 'GlobalResource', array('read' => true));

        $iterator = new RecursiveIteratorIterator($navigation, RecursiveIteratorIterator::SELF_FIRST);
        $stack = array(-1 => $globalResource);
        foreach ($iterator as $page) {
            $depth = $iterator->getDepth();
            $stack = array_slice($stack, 0, $depth + 1, true);
            $resource = self::_constructResource($page);
            $stack[$depth] = $resource;
            $resource->setParent($stack[$depth - 1]);
            unset($resource);
        }
        return $globalResource;
    }
    /**
     * Construct resource based on page definition from navigation object
     *
     * @param Zend_Navigation_Page $page
     * @return RealEstate_Acl_Resource
     */
    static protected function _constructResource(Zend_Navigation_Page $page)
    {
        $resource = $page->getResource();
        if (!$resource) {
            throw new RealEstate_Acl_Resource_Exception('Fail to create resource: no identifire is set');
        }
        $title = $page->getLabel();
        if (!$title) {
            throw new RealEstate_Acl_Resource_Exception('Fail to create resource: no title is set');
        }
        $properties = $page->getCustomProperties();
        if (!array_key_exists('privileges', $properties)) {
            throw new RealEstate_Acl_Resource_Exception('Fail to create resource: no modes is set');
        }
        $modes = $properties['privileges'];

        $resource = new RealEstate_Acl_Resource($resource, $title, $modes);
        return $resource;
    }
}