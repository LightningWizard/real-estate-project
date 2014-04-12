<?php

interface RealEstate_Thumbnail_GeneratorInterface
{
    public function createThumbnail($filePath, $thumbnailPath);
}