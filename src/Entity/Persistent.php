<?php

namespace Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

/**
 * Base class for entities that are to be identified not only by
 * an (insecure) primary key but also by a (more secure) UUID.
 * 
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks
 */
class Persistent
{
    /**
     * The unique auto incremented primary key.
     *
     * @var int|null
     *
     * @ORM\Id
     * @ORM\Column(type="integer", options={"unsigned": true})
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * The internal primary identity key which is unique, too.
     *
     * @var string
     *
     * @ORM\Column(type="guid", unique=true)
     */
    protected $uuid;


    /**
     * Generates a new UUID when the entity is persisted.
     * 
     * @ORM\PrePersist()
     */
    public function initUuid(): void
    {
        $this->uuid = Uuid::uuid4()->toString();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }
    
}
