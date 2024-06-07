<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Product Entity
 *
 * @ORM\Entity
 * @ORM\Table(name="sale_product")
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  App\Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class SaleProduct extends AbstractCalculatorProduct
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
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Sale", inversedBy="products")
     * @ORM\JoinColumn(name="sale_id", referencedColumnName="id")
     */
    protected $sale;

    /**
     * @ORM\ManyToOne(targetEntity="App\Lev\CRMBundle\Entity\Product", inversedBy="sales")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     */
    protected $product;

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
     * Set sale
     *
     * @param \App\Lev\CRMBundle\Entity\Sale $sale
     *
     * @return SaleProduct
     */
    public function setSale(\App\Lev\CRMBundle\Entity\Sale $sale = null)
    {
        $this->sale = $sale;

        return $this;
    }

    /**
     * Get sale
     *
     * @return \App\Lev\CRMBundle\Entity\Sale
     */
    public function getSale()
    {
        return $this->sale;
    }

    /**
     * Set product
     *
     * @param \App\Lev\CRMBundle\Entity\Product $product
     *
     * @return SaleProduct
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
}
