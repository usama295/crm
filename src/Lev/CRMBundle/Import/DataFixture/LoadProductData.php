<?php
namespace App\Lev\CRMBundle\Import\DataFixture;

use App\Lev\CRMBundle\Entity\Product;
use App\Lev\CRMBundle\Entity\ProductOption;
use App\Lev\CRMBundle\Entity\ProductOptionValue;
use Doctrine\Common\Collections\ArrayCollection;

class LoadProductData extends AbstractDataFixture implements DataFixtureInterface
{

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $this->loadFromCSV();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 9;
    }

    // "windows", "Double Hung", "U.I.", "101-110", "fixed", 1008.00
    // 0          1              2       3          4        5
    public function loadFromCSV()
    {
        $manager = $this->getManager();
        try {
            $products = $this->getCSV();
            $this->progressStart('product', count($products));
            $createdBy = $this->getReferenceOrDefault('staff', 0);
            $productName = '';
            $itemName    = '';
            $optionName  = '';
            $valueName   = '';
            $addOption   = false;
            foreach ($products as $item) {

                if ($itemName !== $item[1]) {
                    $itemName = $item[1];
                    $optionName = '';
                    $product = new Product();
                    $product
                        ->setCreatedBy($createdBy)
                        ->setCreatedAt(new \DateTime())
                        ->setCategory($item[0])
                        ->setType($item[4])
                        ->setBaseCost($item[6])
                        ->setName($item[1]);
                    $manager->persist($product);
                }

                if ($optionName !== $item[2]) {
                    $optionName = $item[2];
                    $productOption = new ProductOption();
                    $productOption
                        ->setCreatedBy($createdBy)
                        ->setCreatedAt(new \DateTime())
                        ->setProduct($product)
                        ->setName($item[2]);
                    $manager->persist($productOption);
                }

                $productOptionValue = new ProductOptionValue();
                $productOptionValue
                    ->setCreatedBy($createdBy)
                    ->setCreatedAt(new \DateTime())
                    ->setProductOption($productOption)
                    ->setName($item[3])
                    ->setCost((float)$item[5]);
                $manager->persist($productOptionValue);
                $this->progressAdvance();
                $manager->flush();
            }
            $manager->clear();
            $this->progressFinish();
        } catch (\Exception $e) {
           echo $e->getMessage();
           echo $e->getTraceAsString();
           exit;
        }

    }

    protected function getCSV()
    {
        $file = file(__DIR__ . '/../../../../../importData/products_pricelist.csv');
        array_shift($file);
        return array_map('str_getcsv', $file);
    }
}
