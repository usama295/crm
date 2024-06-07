<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="product_extra")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ProductExtra
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
     * @ORM\Column(name="name", type="string", length=50)
     * @Assert\NotBlank(message="Required")
     * @Assert\Length(max=50)
     */
    protected $name;

    /**
     * @ORM\Column(name="category", type="string")
     * @Assert\Choice(
     *   choices = {"windows", "roofing", "siding", "trim", "gutters", "doors"},
     *   message = "Invalid category."
     * )
     */
    protected $category;

    /**
     * @ORM\Column(name="type", type="string",length=5)
     * @Assert\NotBlank(message="Required")
     * @Assert\Choice(
     *   choices = {"fixed", "pct"},
     *   message = "Invalid type {{value}} (expected: fixed, pct)."
     * )
     * @Assert\Length(max=5)
     */
    protected $type;

    /**
     * @ORM\Column(name="cost", type="decimal", precision=11, scale=2)
     * @Assert\NotBlank(message="Required")
     */
    protected $cost;

    public function toArray()
    {
        return array(
            'id'       => $this->getId(),
            'name'     => $this->getName(),
            'category' => $this->getCategory(),
            'type'     => $this->getType(),
            'cost'     => $this->getCost(),
          );
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
     * Set name
     *
     * @param string $name
     *
     * @return ProductExtra
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
     * @return ProductExtra
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
     * Set cost
     *
     * @param string $cost
     *
     * @return ProductExtra
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return string
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set category
     *
     * @param string $category
     *
     * @return ProductExtra
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
}
