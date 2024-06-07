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
 * @ORM\Table(name="product_option")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ProductOption
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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Product", inversedBy="options")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Product is required")
     **/
    protected $product;

    /**
     * @ORM\OneToMany(targetEntity="App\Lev\CRMBundle\Entity\ProductOptionValue", mappedBy="productOption", cascade={"persist", "remove"})
     */
    protected $values;

    public function toArray()
    {
        $values = array();
        foreach($this->getValues() as $value) {
            $values[] = $value->toArray();
        }

        return array(
            'id'   => $this->getId(),
            'name' => $this->getName(),
            'values' => $values,
        );
    }

    public function toArrayShort()
    {
        return array(
            'id'   => $this->getId(),
            'name' => $this->getName(),
        );
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->values = new \Doctrine\Common\Collections\ArrayCollection();
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
     * @return ProductOption
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
     * Set product
     *
     * @param \App\Lev\CRMBundle\Entity\Product $product
     *
     * @return ProductOption
     */
    public function setProduct(\App\Lev\CRMBundle\Entity\Product $product = null)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return \App\Lev\CRMBundle\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Add value
     *
     * @param \App\Lev\CRMBundle\Entity\ProductOptionValue $value
     *
     * @return ProductOption
     */
    public function addValue(\App\Lev\CRMBundle\Entity\ProductOptionValue $value)
    {
        $this->values[] = $value;

        return $this;
    }

    /**
     * Remove value
     *
     * @param \App\Lev\CRMBundle\Entity\ProductOptionValue $value
     */
    public function removeValue(\App\Lev\CRMBundle\Entity\ProductOptionValue $value)
    {
        $this->values->removeElement($value);
    }

    /**
     * Get values
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getValues()
    {
        return $this->values;
    }
}
