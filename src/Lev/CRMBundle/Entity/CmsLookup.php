<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * CmsLookup
 *
 * @ORM\Table(name="cms_lookup")
 * @ORM\Entity
 */
class CmsLookup
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=35, nullable=true)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="file", type="string", length=25, nullable=true)
     */
    private $file;

    /**
     * @var int|null
     *
     * @ORM\Column(name="accesslevel", type="integer", nullable=true)
     */
    private $accesslevel;

    /**
     * @var string|null
     *
     * @ORM\Column(name="groupname", type="string", length=25, nullable=true)
     */
    private $groupname;

    /**
     * @var int|null
     *
     * @ORM\Column(name="priority", type="integer", nullable=true)
     */
    private $priority;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ts", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private $ts = 'CURRENT_TIMESTAMP';

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    public function setFile(?string $file): self
    {
        $this->file = $file;

        return $this;
    }

    public function getAccesslevel(): ?int
    {
        return $this->accesslevel;
    }

    public function setAccesslevel(?int $accesslevel): self
    {
        $this->accesslevel = $accesslevel;

        return $this;
    }

    public function getGroupname(): ?string
    {
        return $this->groupname;
    }

    public function setGroupname(?string $groupname): self
    {
        $this->groupname = $groupname;

        return $this;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getTs(): ?\DateTimeInterface
    {
        return $this->ts;
    }

    public function setTs(\DateTimeInterface $ts): self
    {
        $this->ts = $ts;

        return $this;
    }


}
