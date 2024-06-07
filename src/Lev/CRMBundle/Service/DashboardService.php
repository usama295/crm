<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;

use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Lev\CRMBundle\Entity\Staff;
use App\Lev\CRMBundle\Entity\Office;
use Symfony\Component\HttpFoundation\Request;
/**
 * Dashboard Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class DashboardService
{

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var array
     */
    protected $dateRanges;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    protected function getDateRanges()
    {
        if (null === $this->dateRanges) {
            $today     = new \DateTime();
            $today->setTime(0, 0, 0);
            $todayEnd  = new \DateTime();
            $todayEnd->setTime(23, 59, 59);

            $startOfMonth = new \DateTime('first day of this month');
            $startOfMonth->setTime(0, 0, 0);
            $endOfMonth   = new \DateTime('last day of this month');
            $endOfMonth->setTime(23, 59, 59);

            $startOfWeek = new \DateTime();
            $startOfWeek = $startOfWeek->modify(('Sunday' == $today->format('l')) ? 'Monday last week' : 'Monday this week');
            $startOfWeek->setTime(0, 0, 0);

            $endOfWeek = new \DateTime();
            $endOfWeek = $startOfWeek->modify('Sunday this week');
            $endOfWeek->setTime(23, 59, 59);

            $yesterday = new \DateTime();
            $yesterday->sub(new \DateInterval('P1D'));
            $yesterday->setTime(0, 0, 0);
            $yesterdayEnd = new \DateTime();
            $yesterdayEnd->sub(new \DateInterval('P1D'));
            $yesterdayEnd->setTime(23, 59, 59);

            $tomorrow = new \DateTime();
            $tomorrow->add(new \DateInterval('P1D'));
            $tomorrow->setTime(0, 0, 0);
            $tomorrowEnd = new \DateTime();
            $tomorrowEnd->add(new \DateInterval('P1D'));
            $tomorrowEnd->setTime(23, 59, 59);

            $twoDaysOut = new \DateTime();
            $twoDaysOut->add(new \DateInterval('P2D'));
            $twoDaysOut->setTime(0, 0, 0);
            $twoDaysOutEnd = new \DateTime();
            $twoDaysOutEnd->add(new \DateInterval('P2D'));
            $twoDaysOutEnd->setTime(23, 59, 59);

            $last7days = new \DateTime();
            $last7days->sub(new \DateInterval('P7D'));
            $last7days->setTime(0, 0, 0);

            $last30days = new \DateTime();
            $last30days->sub(new \DateInterval('P30D'));
            $last30days->setTime(0, 0, 0);

            $next7days = new \DateTime();
            $next7days->add(new \DateInterval('P7D'));
            $next7days->setTime(23, 59, 59);

            $obj = array(
                'today'        => $today,
                'todayEnd'     => $todayEnd,
                'yesterday'    => $yesterday,
                'yesterdayEnd' => $yesterdayEnd,
                'tomorrow'     => $tomorrow,
                'tomorrowEnd'  => $tomorrowEnd,
                'last7days'    => $last7days,
                'last30days'   => $last30days,
                'twoDaysOut'   => $twoDaysOut,
                'next7days'    => $next7days,
                'startOfMonth' => $startOfMonth,
                'endOfMonth'   => $endOfMonth,
                'startOfWeek'  => $startOfWeek,
                'endOfWeek'    => $endOfWeek,
            );
            $fmt = array(
                'today'        => $today->format('Y-m-d H:i:s'),
                'todayEnd'     => $todayEnd->format('Y-m-d H:i:s'),
                'yesterday'    => $yesterday->format('Y-m-d H:i:s'),
                'yesterdayEnd' => $yesterdayEnd->format('Y-m-d H:i:s'),
                'tomorrow'     => $tomorrow->format('Y-m-d H:i:s'),
                'tomorrowEnd'  => $tomorrowEnd->format('Y-m-d H:i:s'),
                'last7days'    => $last7days->format('Y-m-d H:i:s'),
                'last30days'   => $last30days->format('Y-m-d H:i:s'),
                'twoDaysOut'   => $twoDaysOut->format('Y-m-d H:i:s'),
                'next7days'    => $next7days->format('Y-m-d H:i:s'),
                'startOfMonth' => $startOfMonth->format('Y-m-d H:i:s'),
                'endOfMonth'   => $endOfMonth->format('Y-m-d H:i:s'),
                'startOfWeek'  => $startOfWeek->format('Y-m-d H:i:s'),
                'endOfWeek'    => $endOfWeek->format('Y-m-d H:i:s'),
            );

            $this->dateRanges = array(
                'fmt' => $fmt,
                'obj' => $obj,
            );
        }

        return $this->dateRanges;
    }

    protected function getQueryBuilderAppointment()
    {
        return $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Appointment')
            ->createQueryBuilder('a')
            ->innerJoin('a.office', 'o');
    }

    protected function getQueryBuilderSale()
    {
        return $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Sale')
            ->createQueryBuilder('s')
            ->innerJoin('s.office', 'o')
            ->innerJoin('s.appointment', 'a');
    }

    protected function getQueryBuilderProject()
    {
        return $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Project')
            ->createQueryBuilder('p')
            ->innerJoin('p.office', 'o');
    }

    protected function getQueryBuilderProjectActivity()
    {
        return $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:ProjectActivity')
            ->createQueryBuilder('ac')
            ->leftJoin('ac.assignee', 'asg')
            ->innerJoin('ac.project', 'p')
            ->innerJoin('p.office', 'o')
            ->innerJoin('p.sale', 's')
            ->innerJoin('s.appointment', 'a')
            ->innerJoin('p.customer', 'c');
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcOpperationsMan(Request $request, Office $office = null, Staff $user = null)
    {
        $opperationsMan = $this->getDummyData('opperationsMan');
        $dateRanges = $this->getDateRanges();
        $conn = $this->doctrine->getManager()->getConnection();

        // SELECT a.product_interest AS name, count(*) AS qty, SUM(s.job_ceiling) AS vlr
        // FROM sale s INNER JOIN appointment a ON s.appointment_id = a.id
        // WHERE s.sold_on_date >= '2014-12-12 00:00:00'
        // AND   s.sold_on_date <= '2016-01-02 00:00:00'
        // GROUP BY a.product_interest
        $sql = "SELECT a.product_interest AS name, count(*) AS qty, SUM(s.job_ceiling) AS vlr"
             . " FROM sale s INNER JOIN appointment a ON s.appointment_id = a.id"
             . " WHERE s.office_id = {$office->getId()} "
             . " AND   s.sold_on_date >= '{$dateRanges['fmt']['today']}'"
             . " AND   s.sold_on_date <= '{$dateRanges['fmt']['todayEnd']}'"
             . " GROUP BY a.product_interest";
        $results1 = $conn->query($sql)->fetchAll();

        $todayProductVolume = $opperationsMan['todayProductVolume'];
        foreach ($results1 as $row) {
            $todayProductVolume[$row['name']] = array(
                'qty' => $row['qty'],
                'vlr' => $row['vlr']
            );
        }
        $opperationsMan['todayProductVolume'] = $todayProductVolume;

        $qb2 = $this->getQueryBuilderProject();
        $qb2
            ->where('o.id = :office')
            ->andWhere(
                $qb2->expr()->orX(
                    $qb2->expr()->andX(
                        $qb2->expr()->gte('p.startDate', ":starttofmonth"),
                        $qb2->expr()->lte('p.startDate', ":endtofmonth")
                    ),
                    $qb2->expr()->andX(
                        $qb2->expr()->gte('p.endDate', ":starttofmonth"),
                        $qb2->expr()->lte('p.endDate', ":endtofmonth")
                    ),
                    $qb2->expr()->andX(
                        $qb2->expr()->lt('p.startDate', ":starttofmonth"),
                        $qb2->expr()->gt('p.endDate', ":endtofmonth")
                    )
                )
            )
            ->setParameter("starttofmonth", $dateRanges['fmt']['startOfMonth'])
            ->setParameter("endtofmonth", $dateRanges['fmt']['endOfMonth'])
            ->setParameter('office', $office->getId());
        $results2 = $qb2->getQuery()->execute();

        $installationCalendar = array();
        foreach ($results2 as $project) {
            $installationCalendar[] = array(
              'start' => $project->getStartDate() ? $project->getStartDate()->format('Y-m-d') : null,
              'end'   => $project->getEndDate() ? $project->getEndDate()->format('Y-m-d') : null,
              'title' => 'Project #' . $project->getId() . ' '
                      . $project->getCustomer()->getPrimaryFullName() . ' - '
                      . $project->getSale()->getAppointment()->getAddressState(),
              'data'  => array(
                  'projectId' => $project->getId()
              )
            );
        }

        $opperationsMan['installationCalendar'] = $installationCalendar;

        $installationCrewsAtWorkToday = array();
        $qb3 = $this->getQueryBuilderProjectActivity();
        $qb3->where('o.id = :office')
            ->andWhere("ac.name = 'Install'")
            ->andWhere($qb3->expr()->gte('ac.startDate', ":today"))
            ->andWhere($qb3->expr()->lte('ac.endDate', ":todayend"))
            ->setParameter('office', $office->getId())
            ->setParameter("today", $dateRanges['fmt']['today'])
            ->setParameter("todayend", $dateRanges['fmt']['todayEnd']);
        $results3 = $qb3->getQuery()->execute();

        foreach ($results3 as $projectActivity) {
            $installationCrewsAtWorkToday[] = array(
              'projectId'   => $projectActivity->getProject()->getId(),
              'saleId'      => $projectActivity->getProject()->getSale()->getId(),
              'name'        => $projectActivity->getAssignee() ? $projectActivity->getAssignee()->getFullName() : 'n/a',
              'start'       => $projectActivity->getStartDate()->format('Y-m-d'),
              'end'         => $projectActivity->getEndDate()->format('Y-m-d'),
              'paymentType' => $projectActivity->getProject()->getSale()->getPaymentType(),
              'state'       => $projectActivity->getProject()->getSale()->getAppointment()->getAddressState(),

            );
        }
        $opperationsMan['installationCrewsAtWorkToday'] = $installationCrewsAtWorkToday;

        // SELECT
        //   SUM(IF(s.net_on_date IS NOT NULL, s.job_ceiling - IF(s.discount IS NOT NULL, s.discount, 0), 0))
        //   / SUM(IF(s.net_on_date IS NOT NULL, s.job_ceiling, 0))
        //   * 100 AS ceilingPercent
        // FROM sale s;
        $sql = "SELECT SUM(IF(s.net_on_date IS NOT NULL, s.job_ceiling - IF(s.discount IS NOT NULL, s.discount, 0), 0))"
             . " / SUM(IF(s.net_on_date IS NOT NULL, s.job_ceiling, 0)) * 100 AS val"
             . " FROM sale s"
             . " WHERE s.office_id = {$office->getId()} "
             . " AND   s.sold_on_date >= '{$dateRanges['fmt']['today']}'"
             . " AND   s.sold_on_date <= '{$dateRanges['fmt']['todayEnd']}'";
        $results['ceilingPercent'] = $conn->query($sql)->fetchAll();

        // SELECT count(*) AS installs
        // FROM project_calendar pc
        // WHERE pc.name LIKE '%Install%';
        // -- OR --
        // SELECT count(*) AS installs
        // FROM project p
        // WHERE pc.install_date IS NOT NULL;
        // AND   pc.install_date >= now()
        // AND   pc.install_date <= now()
        $sql = "SELECT count(*) AS val"
             . " FROM project_calendar pc INNER JOIN project p ON pc.project_id = p.id"
             . " WHERE p.office_id = {$office->getId()} "
             . " AND   pc.start_date >= '{$dateRanges['fmt']['today']}'"
             . " AND   pc.start_date <= '{$dateRanges['fmt']['todayEnd']}'"
             . " AND   pc.name LIKE '%Install%'";
        $results['installs'] = $conn->query($sql)->fetchAll();

        // SELECT
        //   SUM(IF(s.net_on_date IS NOT NULL, s.job_ceiling - IF(s.discount IS NOT NULL, s.discount, 0), 0)) AS volume
        // FROM sale s
        $sql = "SELECT SUM(IF(s.net_on_date IS NOT NULL, s.job_ceiling - IF(s.discount IS NOT NULL, s.discount, 0), 0)) AS val"
             . " FROM sale s"
             . " WHERE s.office_id = {$office->getId()} "
             . " AND   s.sold_on_date >= '{$dateRanges['fmt']['today']}'"
             . " AND   s.sold_on_date <= '{$dateRanges['fmt']['todayEnd']}'";
        $results['volume'] = $conn->query($sql)->fetchAll();

        // SELECT
        //   count(*) AS netSalesWithoutInstallDate
        // FROM sale s
        // WHERE s.id NOT IN (
        //   SELECT DISTINCT p.sale_id
        //   FROM project_calendar pc
        //   INNER JOIN project p ON pc.project_id = p.id
        //   WHERE pc.name LIKE '%Install%'
        // );
        $sql = $sql1 = "SELECT count(*) AS val"
             . " FROM project p INNER JOIN sale s ON p.sale_id = s.id"
             . " WHERE s.office_id = {$office->getId()}"
             . " AND   p.install_date IS NULL"
             . " AND   s.sold_on_date >= '{$dateRanges['fmt']['today']}'"
             . " AND   s.sold_on_date <= '{$dateRanges['fmt']['todayEnd']}'";
        $results['netSalesWithoutInstallDate'] = $conn->query($sql)->fetchAll();

        // SELECT
        //   SUM(IF (DATEDIFF(NOW(), pc.end_date) >= 1 AND pc.completed_at IS NULL, 1, 0)) AS installedNotCompletedAter24hours
        // FROM project_calendar pc
        // INNER JOIN project p ON pc.project_id = p.id
        // WHERE pc.name LIKE '%Install%';
        $sql = "SELECT SUM(IF (DATEDIFF(NOW(), pc.end_date) >= 1 AND pc.completed_at IS NULL, 1, 0)) AS val"
             . " FROM project_calendar pc"
             . " INNER JOIN project p ON pc.project_id = p.id"
             . " WHERE p.office_id = {$office->getId()} "
             . " AND   pc.name LIKE '%Install%'";
        $results['installedNotCompletedAter24hours'] = $conn->query($sql)->fetchAll();

        // SELECT
        //   AVG(DATEDIFF(pc.start_date, s.sold_on_date)) AS averageSaleToInstallDate
        // FROM project_calendar pc
        // INNER JOIN project p ON pc.project_id = p.id
        // INNER JOIN sale s ON p.sale_id = s.id
        // WHERE pc.name LIKE '%Install%';
        $sql = "SELECT AVG(DATEDIFF(p.start_date, s.sold_on_date)) AS val"
             . " FROM project p"
             . " INNER JOIN sale s ON p.sale_id = s.id"
             . " WHERE p.office_id = {$office->getId()}"
             . " AND   s.sold_on_date >= '{$dateRanges['fmt']['today']}'"
             . " AND   s.sold_on_date <= '{$dateRanges['fmt']['todayEnd']}'";             ;
        $results['averageSaleToInstallDate'] = $conn->query($sql)->fetchAll();

        foreach ($results as $item => $data) {
            $opperationsMan['todayProductVolumeTotals'][$item] = !empty($results[$item][0]['val'])
                ? $results[$item][0]['val']
                : 0;
        }

        return $opperationsMan;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcMarketingMan(Request $request, Office $office = null, Staff $user = null)
    {
        $marketingMan = $this->getDummyData('marketingMan');
        $conn = $this->doctrine->getManager()->getConnection();
        $dateRanges = $this->getDateRanges();

        // SELECT DATE(`a`.`datetime`) AS theday, count(`a`.`datetime`) AS qty
        // FROM appointment a
        // WHERE `a`.`datetime` >= '2015-12-12 00:00:00'
        // AND   `a`.`datetime` <= '2016-01-02 00:00:00'
        // GROUP BY DATE(`a`.`datetime`)
        $sql = "SELECT DATE(`a`.`datetime`) AS theday, count(`a`.`datetime`) AS qty"
             . " FROM appointment a"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['yesterday']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['tomorrowEnd']}'"
             . " GROUP BY DATE(`a`.`datetime`)";
        $results = $conn->query($sql)->fetchAll();

        $scheduledAppointments = array(
            'yesterday' => 0,
            'today'     => 0,
            'tomorrow'  => 0
        );
        foreach ($results as $row) {
            if ($row['theday'] === $dateRanges['obj']['yesterday']->format('Y-m-d')) {
                $scheduledAppointments['yesterday'] = $row['qty'];
            }
            if ($row['theday'] === $dateRanges['obj']['today']->format('Y-m-d')) {
                $scheduledAppointments['today'] = $row['qty'];
            }
            if ($row['theday'] === $dateRanges['obj']['tomorrow']->format('Y-m-d')) {
                $scheduledAppointments['tomorrow'] = $row['qty'];
            }
        }
        $marketingMan['scheduledAppointments'] = $scheduledAppointments;

        // SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep, count(*) as qty
        // FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id
        // WHERE `a`.`datetime` >= '2015-11-01 00:00:00'
        // AND   `a`.`datetime` <= '2015-12-17 23:59:59'
        // GROUP BY CONCAT(u.first_name, ' ', u.last_name)
        // ORDER BY CONCAT(u.first_name, ' ', u.last_name)
        $sql2 = "SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep, count(*) as qty"
             . " FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['today']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'"
             . " GROUP BY CONCAT(u.first_name, ' ', u.last_name)"
             . " ORDER BY CONCAT(u.first_name, ' ', u.last_name)";
        $results2 = $conn->query($sql2)->fetchAll();
        $todayMarketingStatsBySalesRep = array();
        foreach ($results2 as $row) {
            $todayMarketingStatsBySalesRep[] = array(
                'name'         => $row['salesrep'],
                'appointments' => $row['qty']
            );
        }
        $marketingMan['todayMarketingStatsBySalesRep'] = $todayMarketingStatsBySalesRep;

        return $marketingMan;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcMarketingRep(Request $request, Office $office = null, Staff $user = null)
    {
        $marketingRep = $this->getDummyData('marketingRep');
        $conn = $this->doctrine->getManager()->getConnection();
        $dateRanges = $this->getDateRanges();

        // SELECT count(*) as qty FROM appointment a
        // WHERE `a`.`datetime` >= '2015-12-25 00:00:00'
        // AND `a`.`datetime` <= '2016-01-01 00:00:00'
        // AND a.marketing_rep_id = 3;
        $sql = "SELECT count(*) as qty"
             . " FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND  `a`.`datetime` >= '{$dateRanges['fmt']['last7days']}'"
             . " AND  `a`.`datetime` <= '{$dateRanges['fmt']['today']}'"
             . " AND   a.marketing_rep_id = {$user->getId()}";
        $results = $conn->query($sql)->fetchAll();
        $marketingRep['appointments7Days'] = null !==$results[0]['qty'] ? $results[0]['qty'] :0;

        // SELECT DATE(`a`.`datetime`) AS theday, count(`a`.`datetime`) AS qty
        // FROM appointment a
        // WHERE `a`.`datetime` >= '2015-12-25 00:00:00'
        // AND `a`.`datetime` <= '2016-01-01 00:00:00'
        // AND a.marketing_rep_id = 3;
        // GROUP BY DATE(`a`.`datetime`)
        $sql = "SELECT DATE(`a`.`datetime`) AS theday, count(`a`.`datetime`) AS qty"
             . " FROM appointment a"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['yesterday']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['twoDaysOut']}'"
             . " AND   a.marketing_rep_id = {$user->getId()}"
             . " GROUP BY DATE(`a`.`datetime`)";
        $results = $conn->query($sql)->fetchAll();

        $scheduledAppointments = array(
            'today'      => 0,
            'tomorrow'   => 0,
            'twodaysout' => 0,
        );
        foreach ($results as $row) {
            if ($row['theday'] === $dateRanges['obj']['today']->format('Y-m-d')) {
                $scheduledAppointments['today'] = $row['qty'];
            }
            if ($row['theday'] === $dateRanges['obj']['tomorrow']->format('Y-m-d')) {
                $scheduledAppointments['tomorrow'] = $row['qty'];
            }
            if ($row['theday'] === $dateRanges['obj']['twoDaysOut']->format('Y-m-d')) {
                $scheduledAppointments['twodaysout'] = $row['qty'];
            }
        }
        $marketingRep['scheduledAppointments'] = $scheduledAppointments;

        // SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep, count(*) as qty
        // FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id
        // WHERE `a`.`datetime` >= '2015-11-01 00:00:00'
        // AND   `a`.`datetime` <= '2015-12-17 23:59:59'
        // GROUP BY count(*)
        $sql3 = "SELECT CONCAT(u.first_name, ' ', u.last_name) as marketingrep, count(*) as qty"
             . " FROM appointment a INNER JOIN staff u ON a.marketing_rep_id = u.id"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`created_at` >= '{$dateRanges['fmt']['today']}'"
             . " AND   `a`.`created_at` <= '{$dateRanges['fmt']['todayEnd']}'"
             . " GROUP BY CONCAT(u.first_name, ' ', u.last_name)"
             . " ORDER BY count(*) DESC";
        $results3 = $conn->query($sql3)->fetchAll();
        $todayRanking = array();
        $count = 0;
        foreach ($results3 as $row) {
            $count++;
            $todayRanking[] = array(
                'order'        => $count,
                'name'         => $row['marketingrep'],
                'appointments' => $row['qty']
            );
        }
        $marketingRep['todayRanking'] = $todayRanking;

        return $marketingRep;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcContractor(Request $request, Office $office = null, Staff $user = null)
    {
        $contractor = array();
        $dateRanges = $this->getDateRanges();

        $qb = $this->getQueryBuilderProjectActivity();
        $qb
            ->where('o.id = :office')
            ->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->andX(
                        $qb->expr()->gte('ac.startDate', ":starttofmonth"),
                        $qb->expr()->lte('ac.startDate', ":endtofmonth")
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->gte('ac.endDate', ":starttofmonth"),
                        $qb->expr()->lte('ac.endDate', ":endtofmonth")
                    ),
                    $qb->expr()->andX(
                        $qb->expr()->lt('ac.startDate', ":starttofmonth"),
                        $qb->expr()->gt('ac.endDate', ":endtofmonth")
                    )
                )
            )
            ->andWhere($qb->expr()->isNotNull('asg.id'))
            ->setParameter("starttofmonth", $dateRanges['fmt']['startOfMonth'])
            ->setParameter("endtofmonth", $dateRanges['fmt']['endOfMonth'])
            ->setParameter('office', $office->getId());
        $results = $qb->getQuery()->execute();

        foreach ($results as $activity) {
            $contractor[] = array(
              'start' => $activity->getStartDate() ? $activity->getStartDate()->format('Y-m-d') : null,
              'end'   => $activity->getEndDate() ? $activity->getEndDate()->format('Y-m-d') : null,
              'title' => 'Project #' . $activity->getProject()->getId() . ' '
                      . $activity->getAssignee()->getFullName() . ' - '
                      . $activity->getName(),
              'data'  => array(
                  'projectId' => $activity->getProject()->getId()
              )
            );
        }
        // return $qb->getQuery()->getSQL();
        return $contractor;

    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcSalesMan(Request $request, Office $office = null, Staff $user = null)
    {
        $salesMan = $this->getDummyData('salesMan');
        $conn = $this->doctrine->getManager()->getConnection();
        $dateRanges = $this->getDateRanges();

        $sqlEmployees = "SELECT count(*) as qty FROM staff s WHERE s.office_id = {$office->getId()} AND s.enabled = 1;";
        $resultsEmployees = $conn->query($sqlEmployees)->fetchAll();
        $employees = null !==$resultsEmployees[0]['qty'] ? $resultsEmployees[0]['qty'] :0;

        $sqlSalesReps = "SELECT count(*) as qty"
                      . " FROM staff s INNER JOIN staff_role_members srs ON srs.user_id = s.id"
                      . " WHERE s.office_id = {$office->getId()} AND s.enabled = 1 AND srs.group_id = 4;";
        $resultsSalesReps = $conn->query($sqlSalesReps)->fetchAll();
        $salesReps = null !==$resultsSalesReps[0]['qty'] ? $resultsSalesReps[0]['qty'] :0;

        // SELECT count(*) as qty, SUM(IF(a.status = 'sold', 1, 0)) as sold
        // FROM appointment a
        // WHERE `a`.`datetime` >= '2015-11-01 00:00:00'
        // AND `a`.`datetime` <= '2016-01-01 00:00:00'
        $sql = "SELECT count(*) as qty, SUM(IF(a.status = 'sold', 1, 0)) as sold"
             . " FROM appointment a"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['today']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'";
        $results = $conn->query($sql)->fetchAll();
        $salesMan['appointmentsScheduled'] = null !==$results[0]['qty'] ? $results[0]['qty'] :0;
        $salesMan['sold']                  = null !==$results[0]['sold'] ? $results[0]['sold'] :0;

        // SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep, count(*) as qty
        // FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id
        // WHERE `a`.`datetime` >= '2015-11-01 00:00:00'
        // AND   `a`.`datetime` <= '2015-12-17 23:59:59'
        // GROUP BY CONCAT(u.first_name, ' ', u.last_name)
        // ORDER BY CONCAT(u.first_name, ' ', u.last_name)
        $sql2 = "SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep, count(*) as qty"
             . " FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['today']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'"
             . " GROUP BY CONCAT(u.first_name, ' ', u.last_name)"
             . " ORDER BY CONCAT(u.first_name, ' ', u.last_name)";
        $results2 = $conn->query($sql2)->fetchAll();
        $repsAssignedToAppointments = array();
        foreach ($results2 as $row) {
            $repsAssignedToAppointments[] = array(
                'name'         => $row['salesrep'],
                'appointments' => $row['qty']
            );
        }
        $salesMan['repsAssignedToAppointments'] = $repsAssignedToAppointments;

        $stats = array(
            'stats7Days' =>  array('from' => $dateRanges['fmt']['last7days'], 'to' => $dateRanges['fmt']['todayEnd']),
            'stats30Days' =>  array('from' => $dateRanges['fmt']['last30days'], 'to' => $dateRanges['fmt']['todayEnd']),
        );

        foreach ($stats as $stat => $dateRange) {
            // SELECT
            // count(*) as qty,
            // SUM(IF(a.status = 'sold', 1, 0)) AS sold,
            // SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) AS demoed,
            // SUM(s.job_ceiling) / employees * salesRep AS volume,
            // SUM(IF(a.status = 'canceled', 1, 0)) AS piches,
            // SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi,
            // SUM(IF(a.status = 'sold', 1, 0)) / count(*) * 100 AS closingPercent,
            // SUM(s.job_ceiling) / SUM(IF(a.status = 'sold', 1, 0)) AS averageSales
            // FROM appointment a LEFT JOIN sale s ON s.appointment_id = a.id
            $sql3 = "SELECT count(*) as qty,"
                 . " SUM(IF(a.status = 'sold', 1, 0)) AS sold,"
                 . " SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) AS demoed,"
                 . " SUM(s.job_ceiling) / $employees * $salesReps AS volume,"
                 . " SUM(IF(a.status = 'canceled', 1, 0)) AS piches,"
                 . " SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi,"
                 . " SUM(IF(a.status = 'sold', 1, 0)) / count(*) * 100 AS closingPercent,"
                 . " SUM(s.job_ceiling) / SUM(IF(a.status = 'sold', 1, 0)) AS averageSales"
                 . " FROM appointment a LEFT JOIN sale s ON s.appointment_id = a.id"
                 . " WHERE a.office_id = {$office->getId()} "
                 . " AND   `a`.`datetime` >= '{$dateRange['from']}'"
                 . " AND   `a`.`datetime` <= '{$dateRange['to']}'";
            $results3 = $conn->query($sql3)->fetchAll();
            $salesMan[$stat] = array(
              'sold'           => null !==$results3[0]['sold'] ? $results3[0]['sold'] :0,
              'demoed'         => null !==$results3[0]['demoed'] ? $results3[0]['demoed'] :0,
              'volume'         => null !==$results3[0]['volume'] ? $results3[0]['volume'] :0,
              'closingPercent' => null !==$results3[0]['closingPercent'] ? $results3[0]['closingPercent'] :0,
              'averageSales'   => null !==$results3[0]['averageSales'] ? $results3[0]['averageSales'] :0,
              'piches'         => null !==$results3[0]['piches'] ? $results3[0]['piches'] :0,
              'dpi'            => null !==$results3[0]['dpi'] ? $results3[0]['dpi'] :0,
            );

            // SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep,
            // SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi
            // FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id
            // INNER JOIN sale s ON s.appointment_id = a.id
            // GROUP BY CONCAT(u.first_name, ' ', u.last_name)
            // ORDER SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) ASC
            // LIMIT 3
            $sql4 = "SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep,"
                 . " SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi"
                 . " FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id"
                 . " INNER JOIN sale s ON s.appointment_id = a.id"
                 . " WHERE a.office_id = {$office->getId()} "
                 . " AND   `a`.`datetime` >= '{$dateRange['from']}'"
                 . " AND   `a`.`datetime` <= '{$dateRange['to']}'"
                 . " GROUP BY CONCAT(u.first_name, ' ', u.last_name)"
                 . " ORDER BY SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) ASC"
                 . " LIMIT 3";
            $results4 = $conn->query($sql4)->fetchAll();
            $worst3PerformingSalesReps = array();
            foreach ($results4 as $row) {
                $worst3PerformingSalesReps[] = array(
                    'name'  => $row['salesrep'],
                    'total' => $row['dpi']
                );
            }
            $salesMan[$stat]['worst3PerformingSalesReps'] = $worst3PerformingSalesReps;
        }

        // SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep,
        // SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi
        // FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id
        // INNER JOIN sale s ON s.appointment_id = a.id
        // GROUP BY CONCAT(u.first_name, ' ', u.last_name)
        // ORDER BY SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) DESC
        $sql5 = "SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep,"
             . " SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi"
             . " FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id"
             . " INNER JOIN sale s ON s.appointment_id = a.id"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['last30days']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'"
             . " GROUP BY CONCAT(u.first_name, ' ', u.last_name)"
             . " ORDER BY (SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0))) DESC";
        $results5 = $conn->query($sql5)->fetchAll();
        $days30DPIRankingsScoreboard = array();
        foreach ($results5 as $row) {
            $days30DPIRankingsScoreboard[] = array(
                'name'  => $row['salesrep'],
                'total' => $row['dpi']
            );
        }
        $salesMan['days30DPIRankingsScoreboard'] = $days30DPIRankingsScoreboard;

        return $salesMan;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcSalesRep(Request $request, Office $office = null, Staff $user = null)
    {
        $salesRep = $this->getDummyData('salesRep');
        $conn = $this->doctrine->getManager()->getConnection();
        $dateRanges = $this->getDateRanges();

        $stats = array(
            'stats7Days' =>  array('from' => $dateRanges['fmt']['last7days'], 'to' => $dateRanges['fmt']['todayEnd']),
            'stats30Days' =>  array('from' => $dateRanges['fmt']['last30days'], 'to' => $dateRanges['fmt']['todayEnd']),
        );

        foreach ($stats as $stat => $dateRange) {
            $sql = "SELECT SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '') OR (a.no_pitch_reason IS NOT NULL AND a.no_pitch_reason <> ''), 1, 0)) as issueds,"
                 . " SUM(IF(a.status = 'sold', 1, 0)) / count(*) * 100 AS closing,"
                 . " SUM(s.job_ceiling) AS volume"
                 . " FROM appointment a LEFT JOIN sale s ON s.appointment_id = a.id"
                 . " WHERE a.office_id = {$office->getId()}"
                 . " AND   a.sales_rep_id = {$user->getId()}"
                 . " AND   `a`.`datetime` >= '{$dateRange['from']}'"
                 . " AND   `a`.`datetime` <= '{$dateRange['to']}'";
            $results = $conn->query($sql)->fetchAll();
            $salesRep[$stat] = array(
                'issuedAppointments' => null !==$results[0]['issueds'] ? $results[0]['issueds'] :0,
                'closing'            => null !==$results[0]['closing'] ? $results[0]['closing'] :0,
                'volume'             => null !==$results[0]['volume'] ? $results[0]['volume'] :0,
            );
        }

        // SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep,
        // SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi
        // FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id
        // INNER JOIN sale s ON s.appointment_id = a.id
        // GROUP BY CONCAT(u.first_name, ' ', u.last_name)
        // ORDER BY SUM(IF(s.net_on_date IS NOT NULL, s.job_ceiling - IF(s.discount IS NOT NULL, s.discount, 0) - IF(s.sales_tax IS NOT NULL, s.job_ceiling * s.sales_tax / 100, 0), 0)) / count(*) DESc
        $sql2 = "SELECT CONCAT(u.first_name, ' ', u.last_name) as salesrep,"
             . " SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi"
             . " FROM appointment a INNER JOIN staff u ON a.sales_rep_id = u.id"
             . " INNER JOIN sale s ON s.appointment_id = a.id"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['last30days']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'"
             . " GROUP BY CONCAT(u.first_name, ' ', u.last_name)"
             . " ORDER BY (SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0))) DESC";
        $results2 = $conn->query($sql2)->fetchAll();
        $days30DPIRankingsScoreboard = array();
        foreach ($results2 as $row) {
            $days30DPIRankingsScoreboard[] = array(
                'name'  => $row['salesrep'],
                'total' => $row['dpi']
            );
        }
        $salesRep['days30DPIRankingsScoreboard'] = $days30DPIRankingsScoreboard;

        $sql3 = "SELECT a.id, a.status, a.`datetime`, CONCAT(c.primary_first_name, ' ', c.primary_last_name) AS customername, a.product_interest"
              . " FROM appointment a INNER JOIN customer c ON c.id = a.customer_id"
              . " WHERE a.sales_rep_id = " . $user->getId()
              . " ORDER BY a.`datetime` LIMIT 5";
        $results3 = $conn->query($sql3)->fetchAll();
        $last5appointments = array();
        foreach ($results3 as $row) {
            $last5appointments[] = array(
                'id'              => $row['id'],
                'status'          => $row['status'],
                'datetime'        => $row['datetime'],
                'customername'    => $row['customername'],
                'productinterest' => unserialize($row['product_interest']),
            );
        }
        $salesRep['last5appointments'] = $last5appointments;

        return $salesRep;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcCallcenterRep(Request $request, Office $office = null, Staff $user = null)
    {
        $callcenterRep = $this->getDummyData('callcenterRep');
        $conn = $this->doctrine->getManager()->getConnection();
        $dateRanges = $this->getDateRanges();

        $sql = "SELECT SUM(IF(a.callcenter_rep = {$user->getId()} AND (a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '') OR (a.no_pitch_reason IS NOT NULL AND a.no_pitch_reason <> '')), 1, 0)) / count(*) * 100 AS issuedPercent,"
             . " SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '') AND a.callcenter_rep = {$user->getId()}, 1, 0)) / count(*) * 100 AS demoedPercent,"
             . " SUM(IF(a.status = 'canceled' AND a.callcenter_rep = {$user->getId()}, 1, 0)) / count(*) * 100 AS noPicthPercent,"
             . " SUM(IF(a.reset_by IS NOT NULL AND a.callcenter_rep = {$user->getId()}, 1, 0)) / count(*) * 100 AS resetPercent"
             . " FROM appointment a"
             . " WHERE a.office_id = {$office->getId()}"
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['today']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'";
        $results = $conn->query($sql)->fetchAll();
        $callcenterRep['last7days'] = array(
            'issuedPercent'  => null !==$results[0]['issuedPercent'] ? $results[0]['issuedPercent'] :0,
            'demoedPercent'  => null !==$results[0]['demoedPercent'] ? $results[0]['demoedPercent'] :0,
            'noPicthPercent' => null !==$results[0]['noPicthPercent'] ? $results[0]['noPicthPercent'] :0,
            'resetPercent'   => null !==$results[0]['resetPercent'] ? $results[0]['resetPercent'] :0,
        );

        return $callcenterRep;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcCallcenterMan(Request $request, Office $office = null, Staff $user = null)
    {
        // 1. Appointment Percentage…..There doesnt need to be % for this, just the # of appointments
        // 2. Demo Percentage:The # of appointments Demoed (divided by) The # of appointments issued(dispatched)
        // 3. Reset Percentage:There doesnt need to be % for this, just the # of appointments Reset
        // 4. No Pitch Percentage: The # of appointments that No Pitched (divided by) The # of appointments issued(dispatched)
        $callcenterMan = $this->getDummyData('callcenterMan');
        $conn = $this->doctrine->getManager()->getConnection();
        $dateRanges = $this->getDateRanges();

        $sql = "SELECT SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '') OR (a.no_pitch_reason IS NOT NULL AND a.no_pitch_reason <> ''), 1, 0)) / count(*) * 100 AS issuedPercent,"
             . " SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) / count(*) * 100 AS demoedPercent,"
             . " SUM(IF(a.status = 'canceled', 1, 0)) / count(*) * 100 AS noPicthPercent,"
             . " SUM(IF(a.reset_by IS NOT NULL, 1, 0)) / count(*) * 100 AS resetPercent"
             . " FROM appointment a"
             . " WHERE a.office_id = {$office->getId()}"
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['today']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'";
        $results = $conn->query($sql)->fetchAll();
        $callcenterMan['todayScheduledAppointments'] = array(
            'issuedPercent'  => null !== $results[0]['issuedPercent'] ? $results[0]['issuedPercent'] :0,
            'demoedPercent'  => null !== $results[0]['demoedPercent'] ? $results[0]['demoedPercent'] :0,
            'noPicthPercent' => null !== $results[0]['noPicthPercent'] ? $results[0]['noPicthPercent'] :0,
            'resetPercent'   => null !== $results[0]['resetPercent'] ? $results[0]['resetPercent'] :0,
        );

        // TODO nopitch now is just now nopitch instead of canceled???
        $sql = "SELECT"
             . " count(*) AS appointments,"
             . " SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '') OR (a.no_pitch_reason IS NOT NULL AND a.no_pitch_reason <> ''), 1, 0)) AS issued,"
             . " SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '') OR (a.no_pitch_reason IS NOT NULL AND a.no_pitch_reason <> ''), 1, 0)) / count(*) * 100 AS issuedPercent,"
             . " SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) AS demoed,"
             . " SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) / count(*) * 100 AS demoedPercent,"
             . " SUM(IF(a.status = 'sold', 1, 0)) AS sold,"
             . " SUM(IF(a.status = 'sold', 1, 0)) / count(*) * 100 AS soldPercent,"
             . " SUM(IF(a.status = 'canceled', 1, 0)) AS noPicth,"
             . " SUM(IF(a.status = 'canceled', 1, 0)) / count(*) * 100 AS noPicthPercent,"
             . " SUM(IF(a.confirmed_sales_rep = 0, 1, 0)) AS notConfirmed,"
             . " SUM(IF(a.confirmed_sales_rep = 0, 1, 0)) / count(*) * 100 AS notConfirmedPercent"
             . " FROM appointment a"
             . " WHERE a.office_id = {$office->getId()}"
             . " AND   `a`.`datetime` >= '{$dateRanges['fmt']['last7days']}'"
             . " AND   `a`.`datetime` <= '{$dateRanges['fmt']['todayEnd']}'";
        $results = $conn->query($sql)->fetchAll();
        $callcenterMan['last7days'] = array(
            'appointments'        => null !==$results[0]['appointments'] ? $results[0]['appointments'] :0,
            'issued'              => null !==$results[0]['issued'] ? $results[0]['issued'] :0,
            'issuedPercent'       => null !==$results[0]['issuedPercent'] ? $results[0]['issuedPercent'] :0,
            'demoed'              => null !==$results[0]['demoed'] ? $results[0]['demoed'] :0,
            'demoedPercent'       => null !==$results[0]['demoedPercent'] ? $results[0]['demoedPercent'] :0,
            'noPicth'             => null !==$results[0]['noPicth'] ? $results[0]['noPicth'] :0,
            'noPicthPercent'      => null !==$results[0]['noPicthPercent'] ? $results[0]['noPicthPercent'] :0,
            'notConfirmed'        => null !==$results[0]['notConfirmed'] ? $results[0]['notConfirmed'] :0,
            'notConfirmedPercent' => null !==$results[0]['notConfirmedPercent'] ? $results[0]['notConfirmedPercent'] :0,
            // ''  => null !==$results[0][''] ? $results[0][''] :0,
        );

        return $callcenterMan;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcProjectMan(Request $request, Office $office = null, Staff $user = null)
    {
        $projectMan =  $this->getDummyData('projectMan');
        $dateRanges = $this->getDateRanges();
        $conn = $this->doctrine->getManager()->getConnection();

        // 1. you have that correct. projects that have not completed.
        $sql = "SELECT count(*) AS qty"
             . " FROM project p"
             . " WHERE p.office_id = {$office->getId()}"
             . " AND   p.status NOT IN ('completed', 'canceled')";
        $results = $conn->query($sql)->fetchAll();
        $projectMan['inDevelopment'] = null !==$results[0]['qty'] ? $results[0]['qty'] :0;

        // 2. projects without an “Install” date.
        $subselect = "SELECT DISTINCT pc.project_id FROM project_calendar pc INNER JOIN project p ON pc.project_id = p.id"
                   . " WHERE p.office_id = {$office->getId()}"
                   . " AND   p.status NOT IN ('completed', 'canceled')"
                   . " AND   pc.name LIKE '%Install%'";
        $sql = "SELECT p.id as id,"
             . " CONCAT(jb.first_name, ' ', jb.last_name) jobManager,"
             . " CONCAT(c.primary_first_name, ' ', c.primary_last_name) name,"
             . " a.address_state as state"
             . " FROM project p"
             . " INNER JOIN staff jb ON p.job_manager_id = jb.id"
             . " INNER JOIN sale s ON p.sale_id = s.id"
             . " INNER JOIN appointment a ON s.appointment_id = a.id"
             . " INNER JOIN customer c ON p.customer_id = c.id"
             . " WHERE p.office_id = {$office->getId()}"
             . " AND   p.status NOT IN ('completed', 'canceled')"
             . " AND   p.id NOT IN ($subselect)";
        $results = $conn->query($sql)->fetchAll();
        $needingAttention = array();
        foreach ($results as $row) {
            $needingAttention[] = $row;
        }
        $projectMan['needingAttention'] = $needingAttention;

        // 3. This is based on the “Install” date.
        $subselect = "SELECT DISTINCT pc.project_id FROM project_calendar pc INNER JOIN project p ON pc.project_id = p.id"
                   . " WHERE p.office_id = {$office->getId()}"
                   . " AND   p.status NOT IN ('completed', 'canceled')"
                   . " AND   pc.name LIKE '%Install%'"
                   . " AND   pc.start_date > '{$dateRanges['fmt']['todayEnd']}'"
                   . " AND   pc.start_date <= '{$dateRanges['fmt']['next7days']}'"
                   ;
        $sql = "SELECT p.id as id,"
             . " CONCAT(jb.first_name, ' ', jb.last_name) jobManager,"
             . " CONCAT(c.primary_first_name, ' ', c.primary_last_name) name,"
             . " a.address_state as state"
             . " FROM project p"
             . " INNER JOIN staff jb ON p.job_manager_id = jb.id"
             . " INNER JOIN sale s ON p.sale_id = s.id"
             . " INNER JOIN appointment a ON s.appointment_id = a.id"
             . " INNER JOIN customer c ON p.customer_id = c.id"
             . " WHERE p.office_id = {$office->getId()}"
             . " AND   p.status NOT IN ('completed', 'canceled')"
             . " AND   p.id IN ($subselect)";
        $results = $conn->query($sql)->fetchAll();
        $scheduledForNext7days = array();
        foreach ($results as $row) {
            $scheduledForNext7days[] = $row;
        }
        $projectMan['scheduledForNext7days'] = $scheduledForNext7days;

        return $projectMan;
    }

    /**
     * Calc Opperations Man Report
     * @param  Request $request
     * @param  Staff  $user
     * @return array
     */
    public function calcFinanceMan(Request $request, Office $office = null, Staff $user = null)
    {
        $financeMan =  $this->getDummyData('financeMan');
        $dateRanges = $this->getDateRanges();
        $conn = $this->doctrine->getManager()->getConnection();

        // SELECT s.payment_type, COUNT(s.payment_type) AS qty, SUM(s.job_ceiling) as vlr
        // FROM sale s WHERE s.id IN (
        //   SELECT pj.sale_id FROM project_calendar pa INNER JOIN project pj ON pa.project_id = pj.id
        //   WHERE pj.office_id = 1 AND pa.start_date = NOW() AND pa.name = 'Install'
        // );
        $subselect = "SELECT DISTINCT pj.sale_id FROM project_calendar pa INNER JOIN project pj ON pa.project_id = pj.id "
                   . " WHERE pj.office_id = {$office->getId()} AND pa.start_date = '{$dateRanges['fmt']['today']}' AND pa.name = 'Install'";
        $sql1 = "SELECT s.payment_type as paytype, COUNT(s.payment_type) AS qty, SUM(s.job_ceiling) as vlr "
              . " FROM sale s where s.id IN ($subselect) group by paytype ";

        $results1 = $conn->query($sql1)->fetchAll();

        $todayInstalls = array();
        foreach ($results1 as $row) {
            if (null !== $row['paytype']) {
                $todayInstalls[$row['paytype']] = array(
                    'qty' => $row['qty'],
                    'vlr' => $row['vlr']
                );
            }
        }
        $todayInstalls['financed']       = array_key_exists('financing', $todayInstalls) ? $todayInstalls['financing'] : array('qty' => 0, 'vlr' => 0);
        $todayInstalls['cashCreditCard'] = array('qty' => 0, 'vlr' => 0);
        if (array_key_exists('cash', $todayInstalls)) {
            $todayInstalls['cashCreditCard']['qty'] += $todayInstalls['cash']['qty'];
            $todayInstalls['cashCreditCard']['vlr'] += $todayInstalls['cash']['vlr'];
        }
        if (array_key_exists('credit-card', $todayInstalls)) {
            $todayInstalls['cashCreditCard']['qty'] += $todayInstalls['credit-card']['qty'];
            $todayInstalls['cashCreditCard']['vlr'] += $todayInstalls['credit-card']['vlr'];
        }
        $todayInstalls['total']['qty'] = $todayInstalls['financed']['qty'] + $todayInstalls['cashCreditCard']['qty'];
        $todayInstalls['total']['vlr'] = $todayInstalls['financed']['vlr'] + $todayInstalls['cashCreditCard']['vlr'];

        if (array_key_exists('financing', $todayInstalls)) {
            unset($todayInstalls['financing']);
        }
        if (array_key_exists('cash', $todayInstalls)) {
            unset($todayInstalls['cash']);
        }
        if (array_key_exists('credit-card', $todayInstalls)) {
            unset($todayInstalls['credit-card']);
        }
        $financeMan['todayInstalls'] = $todayInstalls;

        // SELECT s.payment_type, COUNT(s.payment_type) AS qty, SUM(s.job_ceiling) as vlr
        // FROM sale s WHERE s.paid_date IS NULL AND s.id IN (
        //   SELECT pj.sale_id FROM project_calendar pc INNER JOIN project pj ON pa.project_id = pj.id
        //   WHERE pj.office_id = 1 AND pc.end_date > NOW()
        // );
        $conn = $this->doctrine->getManager()->getConnection();
        $subselect = "SELECT DISTINCT pj.sale_id FROM project_calendar pc INNER JOIN project pj ON pc.project_id = pj.id "
                   . " WHERE pj.office_id = {$office->getId()} AND pc.end_date > '{$dateRanges['fmt']['today']}'";
        $sql2 = "SELECT s.payment_type as paytype, COUNT(s.payment_type) AS qty, SUM(s.job_ceiling) as vlr "
              . " FROM sale s WHERE s.paid_date IS NULL AND s.id IN ($subselect) group by paytype;";
        $results2 = $conn->query($sql2)->fetchAll();

        $projCompPayIncomp = array();
        foreach ($results2 as $row) {
           if (null !== $row['paytype']) {
               $projCompPayIncomp[$row['paytype']] = array(
                   'qty' => $row['qty'],
                   'vlr' => $row['vlr']
               );
           }
        }
        $projCompPayIncomp['financed']       = array_key_exists('financing', $projCompPayIncomp) ? $projCompPayIncomp['financing'] : array('qty' => 0, 'vlr' => 0);
        $projCompPayIncomp['cashCreditCard'] = array('qty' => 0, 'vlr' => 0);
        if (array_key_exists('cash', $projCompPayIncomp)) {
            $projCompPayIncomp['cashCreditCard']['qty'] += $projCompPayIncomp['cash']['qty'];
            $projCompPayIncomp['cashCreditCard']['vlr'] += $projCompPayIncomp['cash']['vlr'];
        }
        if (array_key_exists('credit-card', $projCompPayIncomp)) {
            $projCompPayIncomp['cashCreditCard']['qty'] += $projCompPayIncomp['credit-card']['qty'];
            $projCompPayIncomp['cashCreditCard']['vlr'] += $projCompPayIncomp['credit-card']['vlr'];
        }
        $projCompPayIncomp['total']['qty'] = $projCompPayIncomp['financed']['qty'] + $projCompPayIncomp['cashCreditCard']['qty'];
        $projCompPayIncomp['total']['vlr'] = $projCompPayIncomp['financed']['vlr'] + $projCompPayIncomp['cashCreditCard']['vlr'];

        if (array_key_exists('financing', $projCompPayIncomp)) {
            unset($projCompPayIncomp['financing']);
        }
        if (array_key_exists('cash', $projCompPayIncomp)) {
            unset($projCompPayIncomp['cash']);
        }
        if (array_key_exists('credit-card', $projCompPayIncomp)) {
            unset($projCompPayIncomp['credit-card']);
        }
        $financeMan['projectCompletePaymentIncomplete'] = $projCompPayIncomp;

        return $financeMan;
    }

    protected function getDummyData($type)
    {
        $dummyData = array(
            'opperationsMan' => array(
                'todayProductVolume' => array(
                    'doors' => array(
                        'qty' => 0,
                        'vlr' => 0
                    ),
                    'gutters' => array(
                        'qty' => 0,
                        'vlr' => 0
                    ),
                    'roofing' => array(
                        'qty' => 0,
                        'vlr' => 0
                    ),
                    'siding' => array(
                        'qty' => 0,
                        'vlr' => 0
                    ),
                    'windows' => array(
                        'qty' => 0,
                        'vlr' => 0
                    )
                ),
                'todayProductVolumeTotals' => array(
                    'ceilingPercent' => 0,
                    'installs' => 0,
                    'volume' => 0,
                    'netSalesWithoutInstallDate' => 0,
                    'installedNotCompletedAter24hours' => 0,
                    'averageSaleToInstallDate' => 0
                ),
                'installationCrewsAtWorkToday' => array(
                    // array( 'name' => 'Ethan Brown', 'paymentType' => 'Cash', 'state' => 'MD' ),
                ),
                'installationCalendar' => array()
            ),
            'contractor' => array(),
            'marketingMan' => array(
                'scheduledAppointments' => array(
                    'yesterday' => 0,
                    'today'     => 0,
                    'tomorrow'  => 0
                ),
                'todayMarketingStatsBySalesRep' => array()
            ),
            'marketingRep' => array(
                'appointments7Days' => 0,
                'scheduledAppointments' => array(
                    'today'      => 0,
                    'tomorrow'   => 0,
                    'twodaysout' => 0
                ),
                'todayRanking' => array(
                    // array( 'order' => 1, 'name' => 'Ethan Brown' ),
                )
            ),
            'salesMan' => array(
                'appointmentsScheduled' => 0,
                'sold' => 0,
                'repsAssignedToAppointments' => array(
                    // array( 'name' => 'Ethan Brown', 'appointments' => 50 ),
                ),
                'stats7Days' => array(
                    'sold' => 0,
                    'demoed' => 0,
                    'volume' => 0,
                    'piches' => 0,
                    'dpi' => 0,
                    'closingPercent' => 0,
                    'averageSales' => 0,
                    'worst3PerformingSalesReps' => array(
                        // array( 'name' => 'Ethan Brown', 'total' => 800 ),
                    )
                ),
                'stats30Days' => array(
                    'sold' => 0,
                    'demoed' => 0,
                    'volume' => 0,
                    'piches' => 0,
                    'dpi' => 0,
                    'closingPercent' => 0,
                    'averageSales' => 0,
                    'worst3PerformingSalesReps' => array(
                        // array( 'name' => 'Ethan Brown', 'total' => 800 ),
                    )
                ),
                'days30DPIRankingsScoreboard' => array(
                    // array( 'name' => 'Ethan Brown', 'dpi' => 3900 ),
                )
            ),
            'salesRep' => array(
                'last5appointments' => array(),
                'stats7Days' => array(
                    'issuedAppointments' => 0,
                    'closing' => 0,
                    'volume' => 0
                ),
                'stats30Days' => array(
                    'issuedAppointments' => 0,
                    'closing' => 0,
                    'volume' => 0
                ),
                'days30DPIRankingsScoreboard' => array(
                    // array( 'name' => 'Ethan Brown', 'dpi' => 3900 ),
                )
            ),
            'callcenterRep' => array(
                'last7days' => array(
                    'issuedPercent' => 0,
                    'demoedPercent' => 0,
                    'noPicthPercent' => 0,
                    'resetPercent' => 0
                )
            ),
            'callcenterMan' => array(
                'todayScheduledAppointments' => array(
                    'issuedPercent' => 0,
                    'demoedPercent' => 0,
                    'noPicthPercent' => 0,
                    'resetPercent' => 0
                ),
                'last7days' => array(
                    'appointments' => 0,
                    'issued' => 0,
                    'issuedPercent' => 0,
                    'demoed' => 0,
                    'demoedPercent' => 0,
                    'sold' => 0,
                    'soldPercent' => 0,
                    'noPicth' => 0,
                    'noPicthPercent' => 0,
                    'notConfirmed' => 0,
                    'notConfirmedPercent' => 0
                )
            ),
            'projectMan' => array(
                'inDevelopment' => 0,
                'needingAttention' => array(
                    // array( 'id' => 221, 'name' => 'Ethan Brown', 'state' => 'Maryland' ),
                ),
                'scheduledForNext7days' => array(
                    // array( 'id' => 221, 'name' => 'Ethan Brown', 'state' => 'Maryland' ),
                )
            ),
            'financeMan' => array(
                'todayInstalls' => array(
                    'financed'       => array('qty' => 0, 'vlr' => 0),
                    'cashCreditCard' => array('qty' => 0, 'vlr' => 0),
                    'total'          => array('qty' => 0, 'vlr' => 0),
                ),
                'projectCompletePaymentIncomplete' => array(
                    'financed'       => array('qty' => 0, 'vlr' => 0),
                    'cashCreditCard' => array('qty' => 0, 'vlr' => 0),
                    'total'          => array('qty' => 0, 'vlr' => 0),
                ),
                'currentMonth'=> array(
                    'currentOffice' => array(
                        'financedVolume'    => array('qty' => 0, 'vlr' => 0),
                        'approvedNetVolume' => array('qty' => 0, 'vlr' => 0),
                        'approvedNetSale'   => 0,
                        'approvedNetVolume' => 0,
                        'bankBuyDown'       => array('qty' => 0, 'vlr' => 0),
                    ),
                    'mass' => array(
                        'financedVolume'    => array('qty' => 0, 'vlr' => 0),
                        'approvedNetVolume' => array('qty' => 0, 'vlr' => 0),
                        'approvedNetSale'   => 0,
                        'approvedNetVolume' => 0,
                        'bankBuyDown'       => array('qty' => 0, 'vlr' => 0),
                    ),
                ),
            ),
        );

        return array_key_exists($type, $dummyData) ? $dummyData[$type] : array();
    }
}
