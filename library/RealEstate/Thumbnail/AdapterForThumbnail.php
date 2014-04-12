<?php

require_once realpath(APPLICATION_PATH . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'library') . DIRECTORY_SEPARATOR . 'Thumbnail.php';

class RealEstate_Thumbnail_AdapterForThumbnail implements RealEstate_Thumbnail_GeneratorInterface {

    /**
     * @var Thumbnail
     */
    protected $adaptee;

    /**
     * @var array
     */
    protected $options;

    /**
     * @param Thumbnail $adaptee
     * @param  array   $options Thumbnail options
     *         <pre>
     *         width   int    Width of thumbnail
     *         height  int    Height of thumbnail
     *         percent number Size of thumbnail per size of original image
     *         method  int    Method of thumbnail creating
     *         halign  int    Horizontal align
     *         valign  int    Vertical align
     *         </pre>
     */
    public function __construct(Thumbnail $adaptee, array $options = array())
    {
        $this->adaptee = $adaptee;
        $this->options = $options;
    }

    /**
     * @param string $filePath Path to target file
     * @param string $thumbnailPath Path to thumbnail
     * @return boolean TRUE on success or FALSE on failure.
     */
    public function createThumbnail($filePath, $thumbnailPath)
    {
        return $this->adaptee->output($filePath, $thumbnailPath, $this->options);
    }

    /**
     * @param array $options Options for adaptee
     * @return self
     */
    public function setOptions(array $options)
    {
        $this->options = $options;
        return $this;
    }

}