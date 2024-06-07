<?php

namespace App\Lev\CRMBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Abstract Calculator Product Entity
 *
 * @ORM\HasLifecycleCallbacks
 * @category Entity
 * @package  Lev\CRMBundle\Entity
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
abstract class AbstractCalculatorProduct
{

    /**
     * @ORM\Column(name="notes", type="text", nullable=true)
     */
    protected $notes;

    /**
     * @ORM\Column(name="options", type="array", nullable=true)
     */
    protected $options;

    /**
     * @ORM\Column(name="extras", type="array", nullable=true)
     */
    protected $extras;

    /**
     * @ORM\Column(name="cost", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $cost;

    /**
     * @ORM\Column(name="quantity", type="decimal", precision=10, scale=2, nullable=true)
     */
    protected $quantity;

    public function toArray()
    {
        return array(
          'id'       => $this->getId(),
          'product'  => $this->getProduct()->toArray(),
          'options'  => $this->getOptions(),
          'extras'   => !empty($this->getExtras()) ? $this->getExtras() :  array(),
          'notes'    => $this->getNotes(),
          'cost'     => $this->getCost(),
          'quantity' => $this->getQuantity(),
        );
    }

    /**
     * @ORM\PreUpdate()
     * @ORM\PrePersist()
     */
    public function updatePersistRecord()
    {
      $itemCost   = $this->getProduct()->getBaseCost();
      $extrasCost = 0;
      $totalPct   = 0;

      foreach ($this->getOptions() as $option) {
          if (array_key_exists('value', $option)) {
              $itemCost += $option['value']['cost'];
          }
      }
      foreach ($this->getExtras() as $extra) {
          if ($extra['type'] === 'flat') {
            $extrasCost += $extra['cost'];
          } else if ($extra['type'] === 'pct') {
            $totalPct += $extra['cost'];
          }
      }
      if ($totalPct) {
        $extrasCost += ($totalPct / 100 * $itemCost);
      }

      switch ($this->getProduct()->getType()) {
          case 'sqft':
              $itemCost = $itemCost * $this->getQuantity() + $extrasCost;
              break;

          default:
              $itemCost += $extrasCost;
              break;
      }

      $this->setCost($itemCost);

    }

    /**
     * Set notes
     *
     * @param string $notes
     *
     * @return SaleProduct
     */
    public function setNotes($notes)
    {
        $this->notes = $notes;

        return $this;
    }

    /**
     * Get notes
     *
     * @return string
     */
    public function getNotes()
    {
        return $this->notes;
    }

    /**
     * Set options
     *
     * @param array $options
     *
     * @return SaleProduct
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Set extras
     *
     * @param array $extras
     *
     * @return SaleProduct
     */
    public function setExtras($extras)
    {
        $this->extras = $extras;

        return $this;
    }

    /**
     * Get extras
     *
     * @return array
     */
    public function getExtras()
    {
        return $this->extras;
    }

    /**
     * Set cost
     *
     * @param string $cost
     *
     * @return SaleProduct
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
     * Set quantity
     *
     * @param string $quantity
     *
     * @return SaleProduct
     */
    public function setQuantity($quantity)
    {
        $this->quantity = !empty($quantity) && $quantity !== 0 ? $quantity : 1;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return string
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

}
