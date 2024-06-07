<?php

namespace App\Lev\CRMBundle\Controller;

use App\Lev\APIBundle\Controller\ORM\AbstractAPIController;
use App\Lev\APIBundle\Config\APIConfig;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Lev\CRMBundle\QueryFilterSearch\ORM\QueryFilterSearch;
use App\Lev\CRMBundle\Entity\Customer;

class AbstractAPICRMController extends AbstractAPIController
{

    protected $customHistory = false;

    /**
     * @inheritdoc
     */
    public function getQueryFilterSearch()
    {
        return new QueryFilterSearch;
    }

    /**
     * @inheritdoc
     */
    protected function updateRecord($record, Request $request)
    {
        $record = parent::updateRecord($record, $request);
        if (
          (! $record instanceof Customer && method_exists($record, 'setOffice'))
          || ($record instanceof Customer && null === $record->getOffice())
        ) {
            $office = $this->getOffice();

            if ($this->getUser()->isGroupAdmin()
                && $request->get('officeid', false)
                && $request->getMethod('put')
            ) {
                $forceOffice = $this->getManager()
                    ->getRepository('LevCRMBundle:Office')
                    ->findOneBy(array('id' => $request->get('officeid', false)));

                $office = $forceOffice ? $forceOffice : $office;
            }
            $record->setOffice($office);
        }

        // History Handling

        $historyService = $this->get('lev_crm.service.history');

        $data = $request->request->all();
        if (array_key_exists('historyNote', $data) && !empty($data['historyNote'])) {
            $historyService->addHistory($record, 9, $data['historyNote'], false);
            $this->customHistory = true;
        }
        if ($this->customHistory) {
          switch ($request->getMethod()) {
              case 'POST': $messageCode = 1; break;
              case 'DELETE': $messageCode = 2; break;
              default:
              case 'PUT': $messageCode = 3; break;
          }
          $historyService->addHistory($record, $messageCode, null, false);
        }

        // Delete attachment handling
        if (array_key_exists('deletedAttachments', $data) && !empty($data['deletedAttachments'])) {
            $this->get('lev_crm.service.fileupload')->delete($data['deletedAttachments'], false);
        }

        return $record;
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(Request $request)
    {
        $qb = parent::getQueryBuilder($request);

        if (
            method_exists($this->getModelClass(), 'getOffice')
            && null !== $this->getOffice()
            && ! $request->get('ignoreOffice', false)
        ) {
            if ($this->getUser()->isGroupAdmin() && $request->get('officeid', false)) {
                $qb->innerJoin('e.office', 'office')
                    ->where('office.id = :office_id')
                    ->setParameter('office_id', $request->get('officeid', false));
            } else if (method_exists($this->getModelClass(), 'getSalesRep') && $this->getUser()->hasStaffrole('SALESREP')) {
                $qb->innerJoin('e.office', 'office')
                    ->leftJoin('e.salesRep', 'salesRep')
                    ->where($qb->expr()->orX(
                    		$qb->expr()->eq('salesRep.id', $this->getUser()->getId()),
                    		$qb->expr()->eq('office.id',':office_id')
                  	))
                    ->setParameter('office_id', $this->getOffice()->getId());
            } else {
                $qb->innerJoin('e.office', 'office')
                    ->where('office.id = :office_id')
                    ->setParameter('office_id', $this->getOffice()->getId());
            }
        }

        if (method_exists($this->getModelClass(), 'getHistories')) {
            $qb->leftJoin('e.histories', 'histories');
            $qb->leftJoin('histories.createdBy', 'createdby');
        }

        return $qb;
    }

    /**
     * Return the user office
     *
     * @return \Lev\CRMBundle\Entity\Office|null
     */
    protected function getOffice()
    {
        return null === $this->getUser()
            ? null
            : $this->getUser()->getOffice();
    }

    /**
     * @param $entityName
     * @param $value
     * @return null|object
     */
    public function getById($entityName, $value)
    {
        if(is_object($value)) {
            $value = $value->id;
        }
        if(is_array($value) && array_key_exists('id', $value)) {
            $value = $value['id'];
        }

        return $this->getManager()
            ->getRepository('LevCRMBundle:' . $entityName)
            ->findOneBy(array('id' => $value));
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Product
     */
    protected function getProductById($value)
    {
        return $this->getById('Product', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\ProductExtra
     */
    protected function getProductExtraById($value)
    {
        return $this->getById('ProductExtra', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\ProductExtra
     */
    protected function getProductOptionById($value)
    {
        return $this->getById('ProductOption', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\ProductExtra
     */
    protected function getProductOptionValueById($value)
    {
        return $this->getById('ProductOptionValue', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\ProjectActivity
     */
    protected function getProjectActivityById($value)
    {
        return $this->getById('ProjectActivity', $value);
    }

    /**
     * @param $value
     * @return null| \App\Lev\CRMBundle\Entity\ProjectLaborCost
     */
    protected function getProjectEstimateById($value)
    {
        return $this->getById('ProjectEstimate', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Staff
     */
    protected function getStaffById($value)
    {
        return $this->getById('Staff', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Sale
     */
    protected function getSaleById($value)
    {
        return $this->getById('Sale', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\SaleProduct
     */
    protected function getSaleProductById($value)
    {
        return $this->getById('SaleProduct', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Customer
     */
    protected function getCustomerById($value)
    {
        return $this->getById('Customer', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Appointment
     */
    protected function getAppointmentById($value)
    {
        return $this->getById('Appointment', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\AppointmentProduct
     */
    protected function getAppointmentProductById($value)
    {
        return $this->getById('AppointmentProduct', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Contractor
     */
    protected function getContractorById($value)
    {
        return $this->getById('Contractor', $value);
    }

    /**
     * @param $value
     * @return null| \Lev\CRMBundle\Entity\Institution
     */
    protected function getInstitutionById($value)
    {
        return $this->getById('Institution', $value);
    }

}
