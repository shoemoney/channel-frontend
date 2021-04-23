<?php

namespace App\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Smallest implements FilterInterface
{
    /**
     * Default size of filter effects
     */
    const DEFAULT_SIZE = 10;
    
    /**
     * Size of filter effects
     *
     * @var integer
     */
    private $size;

    /**
     * Creates new instance of filter
     *
     * @param integer $size
     */
    public function __construct($size = null)
    {
        $this->size = is_numeric($size) ? intval($size) : self::DEFAULT_SIZE;
    }

    public function applyFilter(Image $image)
    {
        return $image->fit(128, 72);
    }
}
