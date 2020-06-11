<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * A file whose content, name, MIME type and size are persisted.
 * 
 * @ORM\Entity
 * @ORM\Table(name="files")
  */
class File extends Persistent
{
    /**
     * @ORM\Column(type="blob") 
     */
    protected $content;

    /** 
     * @ORM\Column(type="string") 
     */
    // Unable to get this to work as advertised: @Assert\NotBlank
    protected $mimetype;

    /**
     * File name.
     * 
     * @ORM\Column(type="string") 
     */
    // Unable to get this to work as advertised: @Assert\NotBlank
    protected $name;

    /** 
     * File size in bytes.
     * 
     * @ORM\Column(type="integer")
     */
    protected $size;


    public function __construct($file)
    {
        $this->content = fopen($file['tmp_name'], 'rb');
        $this->size = $file['size'];
        $this->name = $file['name'];
        $this->mimetype = mime_content_type($file['tmp_name']);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function getMimetype(): string
    {
        return $this->mimetype;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSize(): int
    {
        return $this->size;
    }
    
}
