<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * An image file which may be scaled 
 * @ORM\Entity
 * @ORM\Table(name="images")
 */
class Image extends File
{
    /** 
     * Image width in pixels.
     * 
     * @ORM\Column(type="integer")
     */
    protected $width;

    /** 
     * Image height in pixels.
     * 
     * @ORM\Column(type="integer")
     */
    protected $height;


    /**
     * Creates a new entity from an image file.
     * 
     * If $max_dimension is specified then the image is scaled so
     * that neither its width nor its height exceed $max_dimension.
     */
    public function __construct($file, int $max_dimension = null)
    {
        // Construct a plain file first
        parent::__construct($file);

        // Verify that we have an image
        if (strpos($this->mimetype, 'image/') !== 0)
        {
            throw new \DomainException('File ' . $this->name . ' is not an image; MIME type: ' . $this->mimetype);
        }

        // Process the file with Imagick
        $this->content = new \Imagick($file['tmp_name']);

        // Scale image if the maximum dimension is specified
        if ($max_dimension)
        {
            if (! $this->content->thumbnailImage($max_dimension, $max_dimension, true))
            {
                throw new \DomainException('File ' . $this->name . ' cannot be thumbnailed');
            }

            // We have to materialize the thumbnail image in order to find the 
            // actual image size. Materializing thumbnails like this is not
            // expected to cause memory exhaustion.
            strlen($this->content);
            $this->size = $this->content->getImageLength();
        }

        // Set the actual dimensions
        $this->width = $this->content->getImageWidth();
        $this->height = $this->content->getImageHeight();
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

}
