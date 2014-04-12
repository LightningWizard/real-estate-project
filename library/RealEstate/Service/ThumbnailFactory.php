<?php

class RealEstate_Service_ThumbnailFactory
{
    private static  $generatorOptions = array(
        'AdapterForThumbnail' => array()
    );

    /**
     * @param string $generatorType
     * @return \RealEstate_Thumbnail_GeneratorInterface
     */
    public static function createGenerator($generatorType) {
        if($generatorType == 'AdapterForThumbnail') {
            require_once realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library') . DIRECTORY_SEPARATOR . 'Thumbnail.php';
            $thumbNailAdaptee = new Thumbnail();
            $generatorOptions = self::_getOptionsForGenerator($generatorType);
            $generator = new RealEstate_Thumbnail_AdapterForThumbnail($thumbNailAdaptee, $generatorOptions);
            return $generator;
        }
    }

    /**
     * @param string $generatorType
     * @return array
     */
    private static function _getOptionsForGenerator($generatorType) {
        if(array_key_exists($generatorType, self::$generatorOptions)) {
            return self::$generatorOptions[$generatorType];
        }
        return array();
    }
}