<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use App\Lev\CRMBundle\Entity\Product;
use App\Lev\CRMBundle\Entity\ProductOption;
use App\Lev\CRMBundle\Entity\ProductOptionValue;
use Swagger\Annotations as SWG;
/**
 * @RouteResource("Product")
 */
class ProductController extends AbstractAPICRMController
{

    /**
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="Get list of product price list")
     * @Get("/products/pricelist", name="products_pricelist")
     */
    public function getProductPricelist(Request $request)
    {
      try {
          if (!$request->isMethod('GET')) {
              throw new \Exception(
                  'Method not allowed (expected GET)',
                  Response::HTTP_METHOD_NOT_ALLOWED
              );
          }

          $cats = array('windows', 'roofing', 'siding', 'doors', 'gutters', 'trim');
          $data = array();
          foreach ($cats as $cat) {
              $dataCat = array(
                  'category' => $cat,
                  'items'    => array(),
                  'extras'   => array(),
              );
              $qb = $this->getManager()
                ->getRepository('LevCRMBundle:Product')
                ->createQueryBuilder('p')
                ->leftJoin('p.options', 'o')
                ->leftJoin('o.values', 'v')
                ->where("p.category = '$cat'");
              $results = $qb->getQuery()->execute();
              foreach($results as $product) {
                  $dataCat['items'][] = $product->toArray();
              }

              $qb = $this->getManager()
                ->getRepository('LevCRMBundle:ProductExtra')
                ->createQueryBuilder('pe')
                ->where("pe.category = '$cat'");
              $results = $qb->getQuery()->execute();
              foreach($results as $productExtra) {
                  $dataCat['extras'][] = $productExtra->toArray();
              }

              if (count($dataCat['items']) !== 0) {
                  $data[] = $dataCat;
              }
          }


      } catch (\Exception $e) {
          return $this->renderError($e);
      }

      return $this->renderJsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Product';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'category', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'name', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'type', 'exposed' => true, 'saved' => true, 'filter' => 'string_search', 'search' => true),
            array('name' => 'baseCost', 'exposed' => true, 'saved' => true),
            array('name' => 'options', 'exposed' => true, 'saved' => false),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('product')
            ->setQuerySort(array(
                'name' => 'ASC'
            ));
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(Request $request)
    {
      $qb = $this->getRepository()->createQueryBuilder('e');
      $qb
          ->leftJoin('e.options', 'options')
          ->leftJoin('options.values', 'values')
          ->leftJoin('e.createdBy', 'createdBy');

        return $qb;
    }

    /**
     * @inheritdoc
     * @SWG\Tag(name="Product")
     * @SWG\Response(
     *     response=200,
     *     description="update product price list")
     */
    protected function updateRecord($record, Request $request)
    {
        /** @var $record \App\Lev\CRMBundle\Entity\Sale */
        $record = parent::updateRecord($record, $request);
        $data = $request->request->all();

        $toRemoveOption = array();
        foreach ($record->getOptions() as $option) {
            $toRemoveOption[$option->getId()] = $option;
        }

        if (array_key_exists('options', $data) && !empty($data['options'])) {
            foreach ($data['options'] as $opt) {
                if (is_object($opt)){
                    $prd = get_object_vars($opt);
                }

                $option = (array_key_exists('id', $opt) && !empty($opt['id']))
                    ? $this->getProductOptionById($opt['id'])
                    : new ProductOption();
                if ($option->getId()) {
                    unset($toRemove[$option->getId()]);
                }
                $option
                    ->setProduct($record)
                    ->setName($opt['name']);

                $toRemoveValue = array();
                foreach ($option->getValues() as $value) {
                    $toRemoveValue[$value->getId()] = $value;
                }

                if (array_key_exists('values', $opt) && !empty($opt['values'])) {
                    foreach ($opt['values'] as $val) {
                        if (is_object($val)){
                            $val = get_object_vars($val);
                        }

                        $value = (array_key_exists('id', $val) && !empty($val['id']))
                            ? $this->getProductOptionValueById($val['id'])
                            : new ProductOption();
                        if ($value->getId()) {
                            unset($toRemoveValue[$value->getId()]);
                        }
                        $value
                            ->setProductOption($option)
                            ->setName($val['name'])
                            ->setCost($val['cost']);

                        if ($value->getId()) {
                            $this->getManager()->persist($value);
                        } else {
                            $record->addOption($value);
                        }
                    }
                }

                if ($option->getId()) {
                    $this->getManager()->persist($option);
                } else {
                    $record->addOption($option);
                }

                foreach($toRemoveValue as $value) {
                    $this->getManager()->remove($value);
                    $record->removeOption($value);
                }
            }
        }

        foreach($toRemoveOption as $option) {
            $this->getManager()->remove($option);
            $record->removeOption($option);
        }

        return $record;
    }


}
