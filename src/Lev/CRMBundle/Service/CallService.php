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
use Symfony\Component\Security\Core\SecurityContextInterface;

/**
 * Call Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class CallService
{

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var SecurityContext
     */
    protected $securityContext;

    /**
     * Constructor
     *
     * @param Registry           $doctrine
     * @param SecurityContext    $securityContext
     * @param ContainerInterface $container
     */
    // public function __construct(Registry $doctrine, SecurityContextInterface $securityContext, ContainerInterface $container)
    public function __construct(Registry $doctrine, ContainerInterface $container)
    {
        $this->doctrine        = $doctrine;
        // $this->securityContext = $securityContext;
        $this->container       = $container;
    }

    /**
     * Get query builder
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function getQueryBuilder($officeId = null)
    {
        $qb = $this->doctrine
            ->getManager()
            ->getRepository('LevCRMBundle:Appointment')
            ->createQueryBuilder('a')
            ->innerJoin('a.office', 'office')
            ->innerJoin('a.customer', 'customer')
            ->leftJoin('a.salesRep', 'salesRep')
            ->leftJoin('a.marketingRep', 'marketingRep')
            ->leftJoin('a.lockedBy', 'lockedBy')
            ->leftJoin('a.createdBy', 'createdBy');
        if (null !== $officeId) {
            $qb->where('office.id = :officeid')
                ->setParameter('officeid', $officeId);
        }

        return $qb;
    }

    /**
     * Get Connected User
     * @return \Lev\CRMBundle\Entity\Staff
     */
    public function getUser()
    {
        return $this->securityContext->getToken()->getUser();
      // return $this->get('security.token_storage')->getToken()->getUser();
    }

    /**
     * @param  object  $object
     * @param  boolean $flush
     * @return $this
     */
    public function persist ($object, $flush = true)
    {
        $this->doctrine->getManager()->persist($object);
        if ($flush) {
            $this->doctrine->getManager()->flush();
        }

        return $object;
    }

    /**
     * Do Add Call
     *
     * @param Appointment $appointment [description]
     * @param string      $outcome     [description]
     * @param boolean     $flush       [description]
     */
    public function addCall(Appointment $appointment, $outcome, $flush = true)
    {
        $call = new Call;
        $call
            ->setOffice($appointment->getOffice())
            ->setCustomer($appointment->getCustomer())
            ->setAppointment($appointment)
            ->setOutcome($outcome);
        $this->persist($call, $flush);

        return $call;
    }

    /**
     * Get Call
     * @param  integer $id
     * @return \Lev\CRMBundle\Entity\Call
     */
    public function getAppointment($id)
    {
        return $this->getQueryBuilder()
            ->andWhere('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Get User by Username
     * @param  integer $username
     * @return \Lev\CRMBundle\Entity\Staff
     */
    public function getUserByUsername($username)
    {
        return $this->doctrine
            ->getManager()
            ->getRepository('LevCRMBundle:Staff')
            ->findOneBy(array('username' => $username));
    }

    /**
     * Prepare QueryBuilder by queue type
     * @param  DoctrineORMQueryBuilder $qb
     * @param  string                  $type
     * @return DoctrineORMQueryBuilder                        [description]
     */
    protected function prepareQueryBuilderByType(\Doctrine\ORM\QueryBuilder $qb, $type)
    {
          // DONT_SHOW_QUERY = not `TCPA`, not `wrong_number`
          $qb
            ->andWhere('customer.tcpa = 0')
            ->andWhere('customer.wrongNumber = 0')
            ->andWhere($qb->expr()->orX(
                $qb->expr()->notIn('a.cancelReason', array('tcpa', 'wrong-number')),
                $qb->expr()->isNull('a.cancelReason')
            ));

          $startLimit = new \Datetime();
          switch ($type) {
              // confirming - appointment is scheduled to happened within the next 3 hours and is NOT confirmed
              case 'confirming':
                  $qb
                      ->andWhere($qb->expr()->orX(
                          $qb->expr()->andX(
                              $qb->expr()->eq('a.status', "'scheduled'"),
                              $qb->expr()->lte('a.callback', ':limit')
                          ),
                          $qb->expr()->andX(
                              $qb->expr()->eq('a.status', "'confirmed'"),
                              $qb->expr()->eq('a.confirmedSalesRep', 0),
                              $qb->expr()->gte('a.callback', ':limit')
                          )
                      ))
                      ->andWhere($qb->expr()->isNotNull('a.datetime'))
                      ->andWhere($qb->expr()->gte('a.datetime', ':limit'))
                      ->setParameter('limit', $startLimit->format('Y-m-d H:i:s'));
                  break;

              // resetting - DONT_SHOW_QUERY AND appointment was canceled
              case 'resetting':
                  $qb->andWhere($qb->expr()->in('a.status', array('canceled', 'no-pitch')))
                      ->andWhere($qb->expr()->lte('a.callback', ':limit'))
                      ->setParameter('limit', $startLimit->format('Y-m-d H:i:s'));
                  break;

              // rehash - DONT_SHOW_QUERY AND appointment went to _confirmed_, but did not sell (a few hours beyond the date/time)
              case 'rehashing':
                  $qb->andWhere($qb->expr()->eq('a.status', ':status'))
                      ->setParameter('status', 'pitch-miss')
                      ->andWhere($qb->expr()->lte('a.callback', ':limit'))
                      ->setParameter('limit', $startLimit->format('Y-m-d H:i:s'));
                  break;

              // unresulted - DONT_SHOW_QUERY AND appointment is confirmed but not resulted
              case 'unresulted':
                  $qb
                    ->andWhere($qb->expr()->in('a.status', array('pending', 'scheduled', 'confirmed')))
                    ->andWhere($qb->expr()->lte('a.callback', ':limit'))
                    ->andWhere($qb->expr()->lte('a.datetime', ':limit'))
                    ->setParameter('limit', $startLimit->format('Y-m-d H:i:s'));
                  break;

              // Today overview
              case 'today':
                  $startLimit->setTime(0, 0);
                  $endLimit = new \Datetime();
                  $endLimit->setTime(23, 59, 59);
                  $qb->andWhere($qb->expr()->gt('a.callback', ':startlimit'))
                      ->andWhere($qb->expr()->lt('a.callback', ':endlimit'))
                      ->setParameter('startlimit', $startLimit->format('Y-m-d H:i:s'))
                      ->setParameter('endlimit', $endLimit->format('Y-m-d H:i:s'));
                  break;

              // scheduling - DONT_SHOW_QUERY AND appointment does not have a date/sales rep set
              default:
              case 'scheduling':
                  $qb->andWhere($qb->expr()->eq('a.status', ':status'))
                      ->setParameter('status', 'pending')
                      ->andWhere($qb->expr()->orX(
                          $qb->expr()->isNull('a.datetime'),
                          $qb->expr()->isNull('a.salesRep')
                      ))
                      ->andWhere($qb->expr()->lte('a.callback', ':limit'))
                      ->setParameter('limit', $startLimit->format('Y-m-d H:i:s'));
                  break;
          };

          return $qb;
    }

    /**
     * [getOutboundCalls description]
     *
     * DONT_SHOW_QUERY = not `not_qualified:*`, not `TCPA`, not `wrong_number`
     * scheduling - DONT_SHOW_QUERY AND appointment does not have a date/sales rep set
     * confirming - appointment is scheduled to happened within the next 3 hours and is NOT confirmed
     * resetting - DONT_SHOW_QUERY AND appointment was canceled
     * rehash - DONT_SHOW_QUERY AND appointment went to _confirmed_, but did not sell (a few hours beyond the date/time)
     * unresulted - DONT_SHOW_QUERY AND appointment is confirmed but not resulted
     *
     * @param  [type] $type   [description]
     * @param  [type] $userId [description]
     * @return [type]         [description]
     */
    public function getOutboundCalls($type, $userId)
    {
        $qb = $this->getQueryBuilder();
        $qb = $this->prepareQueryBuilderByType($qb, $type);

        if (null !== $userId) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('a.lockedBy')
                , $qb->expr()->eq('lockedBy.id', ':userid')
                , $qb->expr()->lt('a.dueBy', ':start')
            ))
            ->setParameter('userid', $userId);
        } else {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('a.lockedBy')
                , $qb->expr()->lt('a.dueBy', ':start')
            ));
        }

        // Sort, limit, dueBy info
        $start = new \Datetime();
        $qb->setParameter('start', $start->format('Y-m-d H:i:s'))
            ->addOrderBy('a.callback', 'ASC')
            ->setMaxResults(100);
        $results = $qb->getQuery()->execute();

        $appointments = array();
        foreach ($results as $appointment) {
            $data = $appointment->toArrayCallcenter();
            $conn = $this->doctrine->getManager()->getConnection();
            $sql = "SELECT h.*, s.id as sid, s.username, s.first_name, s.last_name FROM history h INNER JOIN staff s ON h.created_by = s.id"
                 . " WHERE customer_id = {$appointment->getCustomer()->getId()}"
                 . " ORDER BY created_at ASC";
            $historyResults = $conn->query($sql)->fetchAll();
            $data['histories'] = array();
            foreach($historyResults as $history) {

              $data['histories'][] = array(
                  'id'            => $history['id'],
                  'subject'       => $history['subject'],
                  'messageCode'   => $history['message_code'],
                  'message'       => $history['message'],
                  'createdAt'     => $history['created_at'],
                  'createdBy'     => array(
                      'id'        => $history['sid'],
                      'username'  => $history['username'],
                      'firstName' => $history['first_name'],
                      'lastName'  => $history['last_name'],
                      'fullName'  => $history['first_name'] . ' ' . $history['last_name'],
                  )
              );
            }
            $appointments[] = $data;
        }

        // return array( $qb->getQuery()->getSQl(), $qb->getQuery()->getParameters(),);
        return $appointments;
    }

    /**
     * Lock Call
     *
     * @param  Call  $call
     * @param  Staff $lockedby
     * @return Call
     */
    public function lockCall(Appointment $appointment, Staff $lockedby)
    {

        $user = $this->getUserByUsername($lockedby->getUsername());

        $limit = new \Datetime();
        $limit->add(new \DateInterval('PT60M'));
        $appointment
            ->setLockedBy($user)
            ->setDueBy($limit);

        return $this->persist($appointment);
    }

    /**
     * Unlock Call
     *
     * @param  Call $call
     * @return Call
     */
    public function unlockCall(Appointment $appointment)
    {
        $appointment
            ->setLockedBy(null)
            ->setDueBy(null);

        return $this->persist($appointment);
    }

    /**
     * Get an Object
     * @param  string  $entityName
     * @param  integer $value
     * @return object/null
     */
    public function getObject($entityName, $value)
    {
        if (null === $value) {
          return null;
        }

        $class = "LevCRMBundle:{$entityName}";

        if ($value instanceof $class) {
          return $value;
        }

        if (is_array($value) && array_key_exists('id', $value)) {
            $value = $value['id'];
        }

        if (is_object($value)) {
            $value = $value->getId();
        }

        return $this->doctrine
            ->getManager()
            ->getRepository($class)
            ->createQueryBuilder('e')
            ->andWhere('e.id = :id')
            ->setParameter('id', $value)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Run Cron Tasks
     * @return array Total affected rows by status
     */
    public function runCronTasks()
    {
        $data = array(
            'no-pitch'   => $this->updateNoPitch(),
            'pitch-miss' => $this->updatePitchMiss(),
        );
        return $data;
    }

    /**
     * Update Pitch-Miss (REHASHING)
     * rehash - DONT_SHOW_QUERY AND appointment went to _confirmed_, but did not sell (a few hours beyond the date/time)
     * @return integer Total affected rows
     */
    protected function updatePitchMiss()
    {
        $limit = new \Datetime();
        $limit->sub(new \DateInterval('PT1H'));
        $qb = $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Appointment')
            ->createQueryBuilder('a');
        $qb->innerJoin('a.customer', 'customer')
            ->andWhere('customer.tcpa = 0')
            ->andWhere('customer.wrongNumber = 0')
            ->andWhere($qb->expr()->notIn('a.cancelReason', array('tcpa', 'wrong-number')))
            ->andWhere($qb->expr()->eq('a.status', ':status'))
            ->andWhere($qb->expr()->isNotNull('a.datetime'))
            ->andWhere($qb->expr()->lte('a.datetime', ':limit'))
            ->setParameter('status', 'confirmed')
            ->setParameter('limit', $limit->format('Y-m-d H:i:s'));
        $appointments = $qb->getQuery()->execute();
        $historyService = $this->container->get('lev_crm.service.history');
        foreach ($appointments as $appointment) {
            $historyService->addHistory($appointment, 7, 'Appointment Rehashed', false);
        }
        $this->doctrine->getManager()->flush();
        $conn = $this->doctrine->getManager()->getConnection();

        $subselect = "SELECT id FROM customer c WHERE c.tcpa = 0 AND c.wrong_number = 0";
        $sql = "UPDATE appointment SET status = 'pitch-miss' WHERE status = 'confirmed' "
             . "AND datetime IS NOT NULL AND datetime <= NOW() + INTERVAL 1 HOUR AND customer_id  IN ($subselect) "
             . "AND a.cancelReason NOT IN ('tcpa', 'wrong-number')";

        return $conn->exec($sql);
    }

    /**
     * Update No-Pitch (RESETTING)
     * resetting - DONT_SHOW_QUERY AND appointment was canceled
     *
     * If a day has passed and no sales rep is set, the call should go to reset.
     * No matter what, only TODAY's calls should be in scheduling and confirming. Everything else will either be in rehash, reset, or unresulted.
     *
     * @return integer Total affected rows
     */
    protected function updateNoPitch()
    {
        $historyService = $this->container->get('lev_crm.service.history');

        // _DONT_SHOW_QUERY AND appointment was canceled_
        $qb = $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Appointment')
            ->createQueryBuilder('a');
        $qb->innerJoin('a.customer', 'customer')
            ->andWhere('customer.tcpa = 0')
            ->andWhere('customer.wrongNumber = 0')
            ->andWhere($qb->expr()->notIn('a.cancelReason', array('tcpa', 'wrong-number')))
            ->andWhere($qb->expr()->eq('a.status', ':status'))
            ->setParameter('status', 'canceled');
          $appointments = $qb->getQuery()->execute();

          foreach ($appointments as $appointment) {
              $historyService->addHistory($appointment, 7, 'Appointment reset by the system', false);
          }
          $this->doctrine->getManager()->flush();
          $conn = $this->doctrine->getManager()->getConnection();
          $subselect = "SELECT id FROM customer c WHERE c.tcpa = 0 AND c.wrong_number = 0";
          $sql = "UPDATE appointment SET status = 'no-pitch' WHERE status = 'canceled' "
               . "AND customer_id  IN ($subselect) "
               . "AND a.cancelReason NOT IN ('tcpa', 'wrong-number')";
          $totalAffected = $conn->exec($sql);

          // _DONT_SHOW_QUERY AND If a day has passed and no sales rep is set,
          //    the call should go to reset._
          //    (TODO if salesRep is mandatory when datetime is set, this rule is USELESS)
          $limit = new \Datetime();
          $limit->setTime(0, 0);
          $limitSQL = $limit->format('Y-m-d H:i:s');
          $qb = $this->doctrine->getManager()
              ->getRepository('LevCRMBundle:Appointment')
              ->createQueryBuilder('a');
          $qb->innerJoin('a.customer', 'customer')
              ->andWhere('customer.tcpa = 0')
              ->andWhere('customer.wrongNumber = 0')
              ->andWhere($qb->expr()->notIn('a.cancelReason', array('tcpa', 'wrong-number')))
              ->andWhere($qb->expr()->eq('a.status', ':status'))
              ->andWhere($qb->expr()->isNull('a.salesRep'))
              ->andWhere($qb->expr()->isNotNull('a.datetime'))
              ->andWhere($qb->expr()->lt('a.datetime', ':limit'))
              ->setParameter('status', 'pending')
              ->setParameter('limit', $limit->format('Y-m-d H:i:s'));
          $appointments = $qb->getQuery()->execute();
          foreach ($appointments as $appointment) {
              $historyService->addHistory($appointment, 7, 'Appointment reset by the system', false);
          }
          $this->doctrine->getManager()->flush();
          $conn = $this->doctrine->getManager()->getConnection();
          $subselect = "SELECT id FROM customer c WHERE c.tcpa = 0 AND c.wrong_number = 0";
          $sql = "UPDATE appointment SET status = 'no-pitch' WHERE status = 'pending' AND sales_rep_id IS NULL "
               . "AND `datetime` IS NOT NULL AND `datetime` < '$limitSQL' "
               . "AND customer_id  IN ($subselect) "
               . "AND a.cancelReason NOT IN ('tcpa', 'wrong-number')";
          $totalAffected += $conn->exec($sql);

          // _DONT_SHOW_QUERY AND No matter what, only TODAY's calls should be in
          // scheduling and confirming. Everything else will either be in rehash,
          // reset, or unresulted._
          $limit = new \Datetime();
          $limit->setTime(0, 0);
          $limitSQL = $limit->format('Y-m-d H:i:s');
          $qb = $this->doctrine->getManager()
              ->getRepository('LevCRMBundle:Appointment')
              ->createQueryBuilder('a');
          $qb->innerJoin('a.customer', 'customer')
              ->andWhere('customer.tcpa = 0')
              ->andWhere('customer.wrongNumber = 0')
              ->andWhere($qb->expr()->notIn('a.cancelReason', array('tcpa', 'wrong-number')))
              ->andWhere($qb->expr()->in('a.status', array('pending', 'scheduled', 'confirmed')))
              ->andWhere($qb->expr()->isNotNull('a.datetime'))
              ->andWhere($qb->expr()->lt('a.callback', ':limit'))
              ->setParameter('limit', $limit->format('Y-m-d H:i:s'));
          $appointments = $qb->getQuery()->execute();
          foreach ($appointments as $appointment) {
              $historyService->addHistory($appointment, 7, 'Appointment reset by the system', false);
          }
          $this->doctrine->getManager()->flush();
          $conn = $this->doctrine->getManager()->getConnection();
          $subselect = "SELECT id FROM customer c WHERE c.tcpa = 0 AND c.wrong_number = 0";
          $sql = "UPDATE appointment SET status = 'no-pitch' WHERE status IN('pending', 'scheduled', 'confirmed') "
               . "AND `datetime` IS NOT NULL AND `callback` < '$limitSQL' "
               . "AND customer_id  IN ($subselect) "
               . "AND a.cancelReason NOT IN ('tcpa', 'wrong-number')";
          $totalAffected += $conn->exec($sql);

          return $totalAffected;
    }

}
