<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="product_option_value")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ProductOptionValue
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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\ProductOption", inversedBy="values")
     * @ORM\JoinColumn(name="product_option_id", referencedColumnName="id")
     * @Assert\NotBlank(message="Product Option is required")
     **/
    protected $productOption;

    /**
     * @ORM\Column(name="cost", type="decimal", precision=11, scale=2)
     * @Assert\NotBlank(message="Required")
     */
    protected $cost;

    public function toArray()
    {
        return array(
            'id'   => $this->getId(),
            'name' => $this->getName(),
            'cost' => $this->getCost(),
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
     * @return ProductOptionValue
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
     * Set cost
     *
     * @param string $cost
     *
     * @return ProductOptionValue
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
     * Set productOption
     *
     * @param \App\Lev\CRMBundle\Entity\ProductOption $productOption
     *
     * @return ProductOptionValue
     */
    public function setProductOption(\App\Lev\CRMBundle\Entity\ProductOption $productOption = null)
    {
        $this->productOption = $productOption;

        return $this;
    }

    /**
     * Get productOption
     *
     * @return \App\Lev\CRMBundle\Entity\ProductOption
     */
    public function getProductOption()
    {
        return $this->productOption;
    }
}
