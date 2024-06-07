<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\DependencyInjection\ContainerInterface;
use App\Lev\CRMBundle\Entity\Customer;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\Call;
use App\Lev\CRMBundle\Entity\Staff;

/**
 * Staff Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class StaffService
{

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var historyService
     */
    protected $historyService;

    /**
     * Constructor
     *
     * @param Registry           $doctrine
     * @param SecurityContext    $securityContext
     * @param ContainerInterface $container
     */
    public function __construct(Registry $doctrine, HistoryService $historyService)
    {
        $this->doctrine       = $doctrine;
        $this->historyService = $historyService;
    }

    public function deleteStaff(Staff $user)
    {
        $em = $this->doctrine->getManager();
        $userTo = $em->getRepository('LevCRMBundle:Staff')
                  ->findOneBy(array('id' => 1));

        $this->changeCustomersOwner($user, $userTo);
        $this->changeAppointmentsOwner($user, $userTo);
        $this->changeSalesOwner($user, $userTo);
        $this->changeProjectsOwner($user, $userTo);
        $this->changeHistoriesOwner($user, $userTo);
        $this->changeCallsOwner($user, $userTo);
        $this->changeCallRecordsOwner($user, $userTo);

        $em->remove($user);
        $em->flush();
    }

    protected function changeCustomersOwner(Staff $userFrom, Staff $userTo)
    {
        $conn = $this->doctrine->getManager()->getConnection();
        $qb = $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Customer')
            ->createQueryBuilder('c')
            ->where('c.createdBy = :userfrom')
            ->setParameter('userfrom', $userFrom->getId());
        $records = $qb->getQuery()->execute();
        $message = "Change Customer created by from {$userFrom->getFullName()} to {$userTo->getFullName()}";
        foreach ($records as $record) {
            $this->historyService->addHistory($record, 3, $message, false);
        }
        $this->doctrine->getManager()->flush();
        $conn = $this->doctrine->getManager()->getConnection();
        $sql = "UPDATE customer SET created_by = {$userTo->getId()} WHERE created_by = {$userFrom->getId()}";
        $totalAffected = $conn->exec($sql);

        return $totalAffected;
    }

    protected function changeAppointmentsOwner(Staff $userFrom, Staff $userTo)
    {
      $conn = $this->doctrine->getManager()->getConnection();
      $qb = $this->doctrine->getManager()
          ->getRepository('LevCRMBundle:Appointment')
          ->createQueryBuilder('a')
          ->leftJoin('a.customer', 'c');
      $qb->where($qb->expr()->orX(
              $qb->expr()->eq('a.createdBy', ':userfrom'),
              $qb->expr()->eq('a.salesRep', ':userfrom'),
              $qb->expr()->eq('a.marketingRep', ':userfrom'),
              $qb->expr()->eq('a.lockedBy', ':userfrom')
              )
          )
          ->setParameter('userfrom', $userFrom->getId());
      $records = $qb->getQuery()->execute();
      $message = "Change Appointment created by from {$userFrom->getFullName()} to {$userTo->getFullName()}";
      foreach ($records as $record) {
          $this->historyService->addHistory($record, 3, $message, false);
      }
      $this->doctrine->getManager()->flush();
      $conn = $this->doctrine->getManager()->getConnection();
      $sql = "UPDATE appointment SET created_by = {$userTo->getId()} WHERE created_by = {$userFrom->getId()}";
      $conn->exec($sql);
      $sql = "UPDATE appointment SET sales_rep_id = {$userTo->getId()} WHERE sales_rep_id = {$userFrom->getId()}";
      $conn->exec($sql);
      $sql = "UPDATE appointment SET marketing_rep_id = {$userTo->getId()} WHERE marketing_rep_id = {$userFrom->getId()}";
      $conn->exec($sql);
      $sql = "UPDATE appointment SET belongs_to = {$userTo->getId()} WHERE belongs_to = {$userFrom->getId()}";
      $conn->exec($sql);
      $sql = "UPDATE appointment SET locked_by = {$userTo->getId()} WHERE locked_by = {$userFrom->getId()}";
      $conn->exec($sql);

      return count($records);
    }

    protected function changeSalesOwner(Staff $userFrom, Staff $userTo)
    {
      $conn = $this->doctrine->getManager()->getConnection();
      $sql = "UPDATE sale SET created_by = {$userTo->getId()} WHERE created_by = {$userFrom->getId()}";
      $totalAffected = $conn->exec($sql);

      return $totalAffected;
    }

    protected function changeProjectsOwner(Staff $userFrom, Staff $userTo)
    {
      $conn = $this->doctrine->getManager()->getConnection();
      $qb = $this->doctrine->getManager()
          ->getRepository('LevCRMBundle:Project')
          ->createQueryBuilder('p');
      $qb->where($qb->expr()->orX(
              $qb->expr()->eq('p.createdBy', ':userfrom'),
              $qb->expr()->eq('p.jobManager', ':userfrom')
              )
          )
          ->setParameter('userfrom', $userFrom->getId());
      $records = $qb->getQuery()->execute();

      $conn = $this->doctrine->getManager()->getConnection();
      $sql = "UPDATE project SET created_by = {$userTo->getId()} WHERE created_by = {$userFrom->getId()}";
      $conn->exec($sql);
      $sql = "UPDATE project SET job_manager_id = {$userTo->getId()} WHERE job_manager_id = {$userFrom->getId()}";
      $conn->exec($sql);

      return count($records);
    }

    protected function changeHistoriesOwner(Staff $userFrom, Staff $userTo)
    {
      $conn = $this->doctrine->getManager()->getConnection();
      $sql = "UPDATE history SET created_by = {$userTo->getId()} WHERE created_by = {$userFrom->getId()}";
      $totalAffected = $conn->exec($sql);

      return $totalAffected;
    }

    protected function changeCallsOwner(Staff $userFrom, Staff $userTo)
    {
      $conn = $this->doctrine->getManager()->getConnection();
      $sql = "UPDATE customer_call SET created_by = {$userTo->getId()} WHERE created_by = {$userFrom->getId()}";
      $totalAffected = $conn->exec($sql);

      return $totalAffected;
    }

    protected function changeCallRecordsOwner(Staff $userFrom, Staff $userTo)
    {
      $conn = $this->doctrine->getManager()->getConnection();
      $sql = "UPDATE twilio_call_record SET created_by = {$userTo->getId()} WHERE created_by = {$userFrom->getId()}";
      $totalAffected = $conn->exec($sql);

      return $totalAffected;
    }

}
