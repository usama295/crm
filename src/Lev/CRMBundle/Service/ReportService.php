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
use App\Lev\APIBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


/**
 * Report Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ReportService
{

    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * @var array
     */
    protected $salesCommissions;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine, array $salesCommissions)
    {
        $this->doctrine         = $doctrine;
        $this->salesCommissions = $salesCommissions;
    }

    /**
     * Add Id to an array
     * @param array $data The data
     */
    protected function addId(array $data)
    {
        $id = 0;
        foreach ($data as $key => $row) {
            if (!array_key_exists('id', $row)) {
                $data[$key]['id'] = $id;
                $id++;
            }
        }
        return $data;
    }

    /**
     * Format Request Date
     *
     * @param  Request $request [description]
     * @return array
     */
    public function formatRequestDate(Request $request, $dateByDefault = 'createdAt')
    {
      $filter = $request->query->get('filter', array());

      if (is_string($filter) && strlen($filter) > 0) {
          $filter = json_decode($filter, true);
          if (null === $filter) {
              throw new \Exception(
                  'Malformed filter - invalid JSON'
                  , AbstractController::ERR_FILTER
              );
          }
      }

      try {
        $dateMin = (array_key_exists('date', $filter) && array_key_exists('min', $filter['date']))
             ? new \DateTime($filter['date']['min'])
             : new \DateTime();
        $dateMin->setTime(0, 0, 0);
        $dateMin = $dateMin->format('Y-m-d H:i:s');
      } catch (\Exception $e) {
          throw new \Exception("Not a valid min date: {$filter['date']['min']} " . $e->getMessage(), 402);
      }

      try {
        $dateMax = (array_key_exists('date', $filter) && array_key_exists('max', $filter['date']))
             ? new \DateTime($filter['date']['max'])
             : new \DateTime();
        $dateMax->setTime(23, 59, 59);
        $dateMax = $dateMax->format('Y-m-d H:i:s');
      } catch (\Exception $e) {
          throw new Exception("Not a valid max date: {$filter['date']['max']}", 402);
      }

      $dateFields = array(
          'demoedDate' => 'datetime',
          'datetime'   => 'datetime',
          'createdAt'  => 'created_at',
          'soldOnDate' => 'sold_on_date',
          'netOnDate'  => 'net_on_date',
      );
      $dateBy = array_key_exists('by', $filter) && array_key_exists($filter['by'], $dateFields)
          ? $dateFields[$filter['by']]
          : $dateFields[$dateByDefault];
      return array($dateMin, $dateMax, $dateBy);
    }

    /**
     * Report Marketers
     *
     *  // Source Type	Source Name	 Customer	 Call Center Rep.	 Status	   Result Detail
     *  // Online	      Jim Smith	   John Doe	 Nadia Smitd	     Canceled	 Bad Timing
     *
     * @param  Request $request [description]
     * @param  Office  $office  [description]
     * @param  User    $user    [description]
     * @return array
     */
    public function marketerReport(Request $request, Office $office = null, Staff $user = null)
    {

        list ($dateMin, $dateMax, $dateBy) = $this->formatRequestDate($request);

        $sql = "SELECT a.id AS id,"
             . "     marketer_source AS sourcetype,"
             . "     CONCAT(m.first_name, ' ', m.last_name) AS sourcename,"
             . "     CONCAT(sr.first_name, ' ', sr.last_name) AS salesrepname,"
             . "     CONCAT(c.primary_first_name, ' ', c.primary_last_name) As customer,"
             . "     CONCAT(cc.first_name, ' ', cc.last_name) AS callcenterrep,"
             . "     a.status, a.cancel_reason, a.pitch_miss_reason,"
             . "     a.no_pitch_reason, a.`datetime`, a.created_at, a.product_interest"
             . " FROM appointment a"
             . "     LEFT JOIN staff m ON a.marketing_rep_id = m.id"
             . "     LEFT JOIN staff sr ON a.sales_rep_id = sr.id"
             . "     LEFT JOIN customer c ON a.customer_id = c.id"
             . "     LEFT JOIN staff cc ON a.callcenter_rep = cc.id"
             . " WHERE a.office_id = {$office->getId()} "
             . " AND   `a`.`{$dateBy}` >= '{$dateMin}'"
             . " AND   `a`.`{$dateBy}` <= '{$dateMax}'"
             . " AND a.`datetime` IS NOT NULL"
             . (null !== $user ? " AND a.marketing_rep_id = {$user->getId()}" : '')
             . " ORDER BY `a`.`datetime` ASC";
        $conn = $this->doctrine->getManager()->getConnection();
        $results = $conn->query($sql)->fetchAll();
        $data = array();
        foreach ($results as $row) {
            switch ($row['status']) {
                case 'canceled': $resultDetail   = $row['cancel_reason']; break;
                case 'no-pitch': $resultDetail   = $row['no_pitch_reason']; break;
                case 'pitch-miss': $resultDetail = $row['pitch_miss_reason']; break;
                default: $resultDetail = ''; break;
            }
            $record = array(
                'id'              => $row['id'],
                'sourceType'      => null !== $row['sourcetype'] ? $row['sourcetype'] : '',
                'sourceName'      => null !== $row['sourcename'] ? $row['sourcename'] : '',
                'salesRepName'    => null !== $row['salesrepname'] ? $row['salesrepname'] : '',
                'customer'        => $row['customer'],
                'callcenterRep'   => null !== $row['callcenterrep'] ? $row['callcenterrep'] : '',
                'status'          => $row['status'],
                'resultDetail'    => $resultDetail,
                'datetime'        => null !== $row['datetime'] ? $row['datetime'] : '',
                'createdAt'       => $row['created_at'],
                'productInterest' => implode(', ', unserialize($row['product_interest'])),
            );

            $today = new \Datetime();
            $today = $today->format('Y-m-d');
            if (
                  null !== $user
                  && $user->hasStaffRole('MKTREP')
                  && !$user->hasStaffRole('ADMIN')
                  && $today === substr($record['datetime'], 0, 10)
            ) {
                $record['status']       = '-';
                $record['resultDetail'] = '-';
            }

            $data[] = $record;
        }

        return $data;
    }

    /**
     * Sales Commissions Report
     *
     * Date of sales ( most recent to oldest) / Customers last name / Sold Price $ /
     * Appt Ceiling Price$ / Sales Ceiling Price $ / % of ceiling (sold $/ Sales ceiling) /
     * Commission $ / Status / Install date
     *
     * @param  Request $request [description]
     * @param  Office  $office  [description]
     * @param  User    $user    [description]
     * @return array
     */
    public function salesCommissionsReport(Request $request, Office $office = null, Staff $user = null)
    {
      list ($dateMin, $dateMax, $dateBy) = $this->formatRequestDate($request);

      $sql = "SELECT s.id,"
           . "     s.sold_on_date AS saleDate,"
           . "     c.primary_first_name AS customerFirstName,"
           . "     c.primary_last_name AS customerLastName,"
           . "     CONCAT(c.primary_first_name, ' ', c.primary_last_name) AS customer,"
           . "     IF(a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '', 1, 0) AS rehashed,"
           . "     s.sold_price AS soldPrice,"
           . "     a.job_ceiling AS apptJobCeiling,"
           . "     s.job_ceiling AS saleJobCeiling,"
           . "     s.sold_price / s.job_ceiling * 100 AS ceilingPct,"
           . "     s.status,"
           . "     s.payment_type,"
           . "     p.install_date AS installDate"
           . " FROM sale s"
           . "     INNER JOIN customer c ON s.customer_id = c.id"
           . "     INNER JOIN appointment a ON s.appointment_id = a.id"
           . "     LEFT JOIN staff sr ON a.sales_rep_id = sr.id"
           . "     LEFT JOIN project p ON p.sale_id = s.id"
           . " WHERE a.office_id = {$office->getId()} "
           . "     AND s.sold_on_date >= '{$dateMin}'"
           . "     AND s.sold_on_date <= '{$dateMax}'"
           .       (null !== $user ? " AND a.sales_rep_id = {$user->getId()}" : '')
           . " ORDER BY s.sold_on_date DESC, CONCAT(c.primary_first_name, ' ', c.primary_last_name) ASC"
           ;
      $conn = $this->doctrine->getManager()->getConnection();
      $results = $conn->query($sql)->fetchAll();
      $data = array();
      foreach ($results as $row) {

          $soldPrice       = null !== $row['soldPrice'] ? $row['soldPrice'] : 0;
          if ($row['payment_type'] === 'financing') {
              $soldPrice = $soldPrice / 1.025;
          }
          $ceilingPct      = null !== $row['ceilingPct'] ? $row['ceilingPct'] : 0;
          $floorCeilingPct = (int) floor($ceilingPct);
          if ($floorCeilingPct > 100) {
              $floorCeilingPct = 100;
          }
          $commission      = 0;
          $commissionData  = array_key_exists($floorCeilingPct, $this->salesCommissions)
                           ? $this->salesCommissions[$floorCeilingPct]
                           : array(0,0);
          if ($row['status'] !== 'canceled') {
              $commission      = $soldPrice * ($row['rehashed'] === 1
                                               ? $commissionData[1]
                                               : $commissionData[0]) / 100;
          }
          $record = array(
            'id'                => $row['id'],
            'saleDate'          => $row['saleDate'],
            'customerFirstName' => $row['customerFirstName'],
            'customerLastName'  => $row['customerLastName'],
            'customer'          => $row['customer'],
            'soldPrice'         => $soldPrice,
            'apptJobCeiling'    => null !== $row['apptJobCeiling'] ? $row['apptJobCeiling'] : 0,
            'saleJobCeiling'    => null !== $row['saleJobCeiling'] ? $row['saleJobCeiling'] : 0,
            'ceilingPct'        => $ceilingPct,
            'commission'        => $commission,
            'status'            => null !== $row['status'] ? $row['status'] : '-',
            'installDate'       => null !== $row['installDate'] ? $row['installDate'] : '',
          );

          $today = new \Datetime();
          $today = $today->format('Y-m-d');
          if (
                null !== $user
                && $user->hasStaffRole('SALESMAN')
                && !$user->hasStaffRole('ADMIN')
                && $today === substr($record['saleDate'], 0, 10)
          ) {
              $record['status']     = '-';
              $record['commission'] = 0;
          }
          $data[] = $record;
      }

      return $data;
    }

    /**
     * Sales Report
     *
     * Number of appts / Issued # / Demoed # / Sold # / Sold Volume / Net # / Net Volume / DPI Underneath
     * Issued % / Demoed % / Sold %/ Net %
     *
     * @param  Request $request [description]
     * @param  Office  $office  [description]
     * @param  User    $user    [description]
     * @return array
     */
    public function salesReport(Request $request, Office $office = null, Staff $user = null)
    {
        list ($dateMin, $dateMax, $dateBy) = $this->formatRequestDate($request);

        $sql = "SELECT sr.id AS id,"
             . "     CONCAT(sr.first_name, ' ', sr.last_name) AS salesRep,"
             . "     count(*) AS appointmentQty,"
             . "     SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> '') OR (a.no_pitch_reason IS NOT NULL AND a.no_pitch_reason <> ''), 1, 0)) as issued,"
             . "     SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) / count(*) * 100 AS issuedPercent,"
             . "     SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) AS demoed,"
             . "     SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) / count(*) * 100 AS demoedPercent,"
             . "     SUM(IF(a.status = 'sold', 1, 0)) AS sold,"
             . "     SUM(IF(a.status = 'sold', 1, 0)) / count(*) * 100 AS soldPercent,"
             . "     SUM(IF(s.id IS NOT NULL, s.sold_price, 0)) AS soldVolume,"
             . "     SUM(IF(s.net_on_date IS NOT NULL, 1, 0)) AS net,"
             . "     SUM(IF(s.net_on_date IS NOT NULL, 1, 0)) / count(*) * 100 AS netPercent,"
             . "     SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) AS netVolume,"
             . "     SUM(IF(s.net_on_date IS NOT NULL, s.sold_price, 0)) / SUM(IF(a.status IN('sold', 'pitch-miss'), 1, 0)) AS dpi"
             . " FROM appointment a"
             . "     LEFT JOIN staff sr ON a.sales_rep_id = sr.id"
             . "     LEFT JOIN sale s ON a.id = s.appointment_id"
             . " WHERE a.office_id = {$office->getId()} "
             . "     AND `a`.`datetime` >= '{$dateMin}'"
             . "     AND `a`.`datetime` <= '{$dateMax}'"
             .       (null !== $user ? " AND a.sales_rep_id = {$user->getId()}" : '')
             . " GROUP BY sr.id"
             . " ORDER BY CONCAT(sr.first_name, ' ', sr.last_name) DESC"
             ;
        $conn = $this->doctrine->getManager()->getConnection();
        $results = $conn->query($sql)->fetchAll();
        $data = array();
        foreach ($results as $row) {
            if (null === $row['salesRep']) {
                $row['salesRep'] = 'Unassigned';
            }
            $row['soldVolume'] = null !== $row['soldVolume'] ? $row['soldVolume'] : 0;
            $data[] = $row;
        }

        return $data;
    }

    /**
     * Marketer Payroll Report
     *
     * @param  Request $request [description]
     * @param  Office  $office  [description]
     * @param  User    $user    [description]
     * @return array
     */
    public function marketerPayrollReport(Request $request, Office $office = null, Staff $user = null)
    {
      list ($dateMin, $dateMax, $dateBy) = $this->formatRequestDate($request);

      $sql = "SELECT"
           . "     a.marketing_rep_id AS mkt_id,"
           . "     CONCAT(m.first_name, ' ', m.last_name) AS sourcename,"
           . "     CONCAT(m.address_city, ' ', m.address_state) AS state,"
           . "     SUM(IF(a.status = 'sold' OR (a.pitch_miss_reason IS NOT NULL AND a.pitch_miss_reason <> ''), 1, 0)) AS demoed"
           . " FROM appointment a"
           . "     INNER JOIN staff m ON a.marketing_rep_id = m.id"
           . " WHERE a.office_id = {$office->getId()} "
           . "     AND `a`.`datetime` IS NOT NULL"
           . "     AND `a`.`datetime` >= '{$dateMin}'"
           . "     AND `a`.`datetime` <= '{$dateMax}'"
           . (null !== $user ? "     AND a.marketing_rep_id = {$user->getId()}" : '')
           . "     AND m.enabled = 1"
           . " GROUP BY a.marketing_rep_id"
           . " ORDER BY CONCAT(m.first_name, ' ', m.last_name);";
      $conn = $this->doctrine->getManager()->getConnection();
      $results = $conn->query($sql)->fetchAll();
      $data = array();
      foreach ($results as $row) {
          $data[$row['sourcename']]        = $row;
          $data[$row['sourcename']]['net'] = 0;
      }

      $sql2 = "SELECT"
           . "     a.marketing_rep_id AS mkt_id,"
           . "     CONCAT(m.first_name, ' ', m.last_name) AS sourcename,"
           . "     COUNT(*) AS net"
           . " FROM sale s"
           . "     INNER JOIN appointment a ON s.appointment_id = a.id"
           . "     INNER JOIN staff m ON a.marketing_rep_id = m.id"
           . " WHERE a.office_id = {$office->getId()} "
           . "     AND s.net_on_date IS NOT NULL"
           . "     AND s.net_on_date >= '{$dateMin}'"
           . "     AND s.net_on_date <= '{$dateMax}'"
           . (null !== $user ? " AND a.marketing_rep_id = {$user->getId()}" : '')
           . " GROUP BY a.marketing_rep_id"
           . " ORDER BY CONCAT(m.first_name, ' ', m.last_name);";
      $conn = $this->doctrine->getManager()->getConnection();
      $results = $conn->query($sql2)->fetchAll();
      foreach ($results as $row) {
          $data[$row['sourcename']]['net'] = $row['net'];
      }

      foreach($data as $key => $row) {
          if (!array_key_exists('demoed', $row)) {
              $data[$key]['demoed'] = 0;
          }
          $data[$key]['demoedCost'] = $data[$key]['demoed'] * 200;
          $data[$key]['netCost']    = $data[$key]['net'] * 100;
          $data[$key]['weekBonus']  = $data[$key]['demoed'] >= 4
                                    ? $data[$key]['demoed'] * 50
                                    : 0;
          $data[$key]['total']      = $data[$key]['demoedCost']
                                    + $data[$key]['netCost']
                                    + $data[$key]['weekBonus'];
      }

      ksort($data);
      $data = $this->addId(array_values($data));

      return $data;
    }

}
