<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Lev\CRMBundle\Entity\SaleProduct;
use Swagger\Annotations as SWG;
/**
 * @RouteResource("Sale")
 */
class SaleController extends AbstractAPICRMController
{

    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Sale';
    }

    /**
     * @inheritdoc
     */
    public function configure(APIConfig $config)
    {
        $fields = array(
            array('name' => 'id', 'exposed' => true, 'saved' => false),
            array('name' => 'office', 'exposed' => true, 'saved' => false, 'filter' => 'objectid',
                'object' => '\App\Lev\CRMBundle\Entity\Office'),
            array('name' => 'customer.primaryFirstName', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.primaryLastName', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.phone1Number', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.phone2Number', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.phone3Number', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer.email', 'exposed' => false, 'saved' => false, 'search' => true),
            array('name' => 'customer', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Customer'),
            array('name' => 'appointment', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Appointment'),
            array('name' => 'appointment.salesRep', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Appointment'),

            array('name' => 'soldPercentage', 'exposed' => true, 'saved' => false, 'filter' => 'floatrange'),
            array('name' => 'salesTax', 'exposed' => true, 'saved' => true, 'filter' => 'floatrange'),
            array('name' => 'amountDue', 'exposed' => true, 'saved' => false, 'filter' => 'floatrange'),
            array('name' => 'amountOwned', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'paymentType', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'discount', 'exposed' => true, 'saved' => true, 'filter' => 'floatrange'),
            array('name' => 'jobCeiling', 'exposed' => true, 'saved' => false, 'filter' => 'floatrange'),
            array('name' => 'soldPrice', 'exposed' => true, 'saved' => false, 'filter' => 'floatrange'),
            array('name' => 'jobCeilingOverride', 'exposed' => true, 'saved' => true, 'filter' => 'floatrange'),
            array('name' => 'soldPriceOverride', 'exposed' => true, 'saved' => true, 'filter' => 'floatrange'),
            array('name' => 'notes', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'status', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'createdAt', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'createdBy', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'netOnDate', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'soldOnDate', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'paidDate', 'exposed' => true, 'saved' => true, 'filter' => 'string', 'date' => true),
            array('name' => 'products', 'exposed' => true, 'saved' => false),
            array('name' => 'histories', 'exposed' => true, 'saved' => false),
            array('name' => 'historyNote', 'exposed' => false, 'saved' => false),
            array('name' => 'jobFloor', 'exposed' => true, 'saved' => false),
            array('name' => 'projectId', 'exposed' => true, 'saved' => false),
            array('name' => 'attachments', 'exposed' => true, 'saved' => false),
            array('name' => 'deletedAttachments', 'exposed' => false, 'saved' => false),
            array('name' => 'insertedAttachments', 'exposed' => false, 'saved' => false),
            array('name' => 'discountMethod', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'discountPercentage', 'exposed' => true, 'saved' => true, 'filter' => 'floatrange'),
            array('name' => 'financing', 'exposed' => true, 'saved' => true, 'filter' => 'boolean', 'boolean' => true),
            array('name' => 'totalLiquid', 'exposed' => false, 'saved' => false),
            array('name' => 'downPayment', 'exposed' => true, 'saved' => true, 'filter' => 'floatRange'),
        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('sale')
            ->setQuerySort(array(
                'soldOnDate' => 'DESC'
            ));
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(Request $request)
    {
        $qb = parent::getQueryBuilder($request);
        $qb->leftJoin('e.products', 'products')
            ->leftJoin('products.product', 'product')
            ->innerJoin('e.customer', 'customer')
            ->leftJoin('e.project', 'project')
            ->innerJoin('e.appointment', 'appointment')
            ->leftJoin('appointment.salesRep', 'salesRep');

           

        return $qb;
    }

    /**
     * @inheritdoc
     * @SWG\Tag(name="Sale")
     * @SWG\Response(
     *     response=200,
     *     description="update sale record")
     */
    protected function updateRecord($record, Request $request)
    {
        /** @var $record \App\Lev\CRMBundle\Entity\Sale */
        $record = parent::updateRecord($record, $request);
        $data = $request->request->all();

        if (null === $record->getAppointment()) {
            throw new \Exception(
                'Appointment invalid to create Sale: id ' . print_r($data['appointment'], true),
                self::ERR_VALIDATE
            );
        }

        $record->setCustomer($record->getAppointment()->getCustomer());
        $productCalculatorService = $this->get('lev_crm.service.productcalculator');
        $record = $productCalculatorService->updateProducts($record, $data);

        return $record;
    }

}
