<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="product")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class Product
{
    use Traits\BlameableCreateEntity;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="category", type="string")
     * @Assert\Choice(
     *   choices = {"windows", "roofing", "siding", "trim", "gutters", "doors"},
     *   message = "Invalid category."
     * )
     */
    protected $category;

    /**
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank(message="Required")
     * @Assert\Length(max=50)
     */
    protected $name;

    /**
     * @ORM\Column(name="type", type="string",length=5)
     * @Assert\NotBlank(message="Required")
     * @Assert\Choice(
     *   choices = {"fixed", "sqft", "pct"},
     *   message = "Invalid type {{value}} (expected: fixed, sqft, pct)."
     * )
     * @Assert\Length(max=5)
     */
    protected $type;

    /**
     * @ORM\Column(name="base_cost", type="decimal", precision=11, scale=2)
     * @Assert\NotBlank(message="Required")
     */
    protected $baseCost;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\ProductOption", mappedBy="product", cascade={"persist", "remove"})
     */
    protected $options;


    public function toArray()
    {
        $options = array();
        foreach($this->getOptions() as $option) {
            $options[] = $option->toArray();
        }

        return array(
            'id'        => $this->getId(),
            'category'  => $this->getCategory(),
            'name'      => $this->getName(),
            'type'      => $this->getType(),
            'baseCost'  => $this->getBaseCost(),
            'options'   => $options,
        );
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->options = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return Product
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set type
     *
     * @param string $type
     *
     * @return Product
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set baseCost
     *
     * @param string $baseCost
     *
     * @return Product
     */
    public function setBaseCost($baseCost)
    {
        $this->baseCost = $baseCost;

        return $this;
    }

    /**
     * Get baseCost
     *
     * @return string
     */
    public function getBaseCost()
    {
        return $this->baseCost;
    }

    /**
     * Add option
     *
     * @param \App\Lev\CRMBundle\Entity\ProductOption $option
     *
     * @return Product
     */
    public function addOption(\App\Lev\CRMBundle\Entity\ProductOption $option)
    {
        $this->options[] = $option;

        return $this;
    }

    /**
     * Remove option
     *
     * @param \App\Lev\CRMBundle\Entity\ProductOption $option
     */
    public function removeOption(\App\Lev\CRMBundle\Entity\ProductOption $option)
    {
        $this->options->removeElement($option);
    }

    /**
     * Get options
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Product
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set createdBy
     *
     * @param \App\Lev\CRMBundle\Entity\Staff $createdBy
     * @return Product
     */
    public function setCreatedBy(\App\Lev\CRMBundle\Entity\Staff $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \App\Lev\CRMBundle\Entity\Staff
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }
}
