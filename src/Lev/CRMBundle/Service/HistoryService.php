<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Lev\CRMBundle\Entity\Customer;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\Sale;
use App\Lev\CRMBundle\Entity\Project;
use App\Lev\CRMBundle\Entity\Call;
use App\Lev\CRMBundle\Entity\History;

/**
 * History Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class HistoryService
{

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param  History $history
     * @param  boolean $flush
     * @return $this
     */
    protected function persist(History $history, $flush = true)
    {
        $this->doctrine->getManager()->persist($history);
        if ($flush) {
            $this->doctrine->getManager()->flush();
        }
        return $this;
    }

    /**
     * Add History by Customer
     * @param Customer $customer
     * @param integer  $messageCode
     * @param string   $message
     * @param boolean  $flush
     * @return $this
     */
    public function addHistoryByCustomer(Customer $customer, $messageCode, $message = null, $flush = true)
    {
        $history = new History;
        $history
            ->setSubject('customer')
            ->setCustomer($customer)
            ->setMessageCode($messageCode);
        if (!empty($message)) {
            $history->setMessage($message);
        }
        return $this->persist($history, $flush);
    }

    /**
     * Add History by Appointment
     * @param Appointment $appointment
     * @param integer     $messageCode
     * @param string      $message
     * @param boolean     $flush
     * @return $this
     */
    public function addHistoryByAppointment(Appointment $appointment, $messageCode, $message = null, $flush = true)
    {
        $history = new History;
        $history
            ->setSubject('appointment')
            ->setAppointment($appointment)
            ->setCustomer($appointment->getCustomer())
            ->setMessageCode($messageCode);
        if (!empty($message)) {
            $history->setMessage($message);
        }
        return $this->persist($history, $flush);
    }

    /**
     * Add History by Sale
     * @param Sale    $sale
     * @param integer $messageCode
     * @param string  $message
     * @param boolean $flush
     * @return $this
     */
    public function addHistoryBySale(Sale $sale, $messageCode, $message = null, $flush = true)
    {
        $history = new History;
        $history
            ->setSubject('sale')
            ->setSale($sale)
            ->setAppointment($sale->getAppointment())
            ->setCustomer($sale->getCustomer())
            ->setMessageCode($messageCode);
        if (!empty($message)) {
            $history->setMessage($message);
        }
        return $this->persist($history, $flush);
    }

    /**
     * Add History by Project
     * @param Project $project
     * @param integer $messageCode
     * @param string  $message
     * @param boolean $flush
     * @return $this
     */
    public function addHistoryByProject(Project $project, $messageCode, $message = null, $flush = true)
    {
        $history = new History;
        $history
            ->setSubject('project')
            ->setProject($project)
            ->setSale($project->getSale())
            ->setAppointment($project->getSale()->getAppointment())
            ->setCustomer($project->getCustomer())
            ->setMessageCode($messageCode);
        if (!empty($message)) {
            $history->setMessage($message);
        }
        return $this->persist($history, $flush);
    }

    /**
     * Add History by Call
     * @param Call    $call
     * @param integer $messageCode
     * @param string  $message
     * @param boolean $flush
     * @return $this
     */
    public function addHistoryByCall(Call $call, $messageCode, $message = null, $flush = true)
    {
        $history = new History;
        $history
            ->setSubject('call')
            ->setAppointment($call->getAppointment())
            ->setCustomer($call->getCustomer())
            ->setCall($call)
            ->setMessageCode($messageCode);
        if (!empty($message)) {
            $history->setMessage($message);
        }
        return $this->persist($history, $flush);
    }

    /**
     * Add History
     *
     * @param object  $object
     * @param integer $messageCode
     * @param string  $message
     * @param boolean $flush
     * @return HistoryService
     * @throws \Exception
     */
    public function addHistory($object, $messageCode, $message, $flush = true)
    {
        if ($object instanceof Customer) {
            return $this->addHistoryByCustomer($object, $messageCode, $message, $flush);
        }

        if ($object instanceof Appointment) {
            return $this->addHistoryByAppointment($object, $messageCode, $message, $flush);
        }

        if ($object instanceof Sale) {
            return $this->addHistoryBySale($object, $messageCode, $message, $flush);
        }

        if ($object instanceof Project) {
            return $this->addHistoryByProject($object, $messageCode, $message, $flush);
        }

        if ($object instanceof Call) {
            return $this->addHistoryByCall($object, $messageCode, $message, $flush);
        }
    }
}
