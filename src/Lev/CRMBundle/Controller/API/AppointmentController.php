<?php

namespace App\Lev\CRMBundle\Controller\API;

use FOS\RestBundle\Controller\Annotations\RouteResource;
use App\Lev\APIBundle\Config\APIConfig;
use App\Lev\CRMBundle\Controller\AbstractAPICRMController;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Get;
use App\Lev\CRMBundle\Entity\Staff;
use App\Lev\CRMBundle\Entity\Sale;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\Attachment;
use App\Lev\CRMBundle\Entity\Office;
use Symfony\Component\HttpFoundation\Response;
use App\Lev\CRMBundle\Entity\FileUpload;
use Swagger\Annotations as SWG;
use App\Lev\APIBundle\Component\HttpFoundation\File\UploadedFile;
/**
 * @RouteResource("Appointment")
 */
class AppointmentController extends AbstractAPICRMController
{

    protected $warnSalesRep = false;
    protected $isSoldNow = false;


    /**
     * @SWG\Tag(name="Appointment")
     * @SWG\Response(
     *     response=200,
     *     description="get sales representative appointments")
     * @Get("/appointments/{office}/salesreps/schedule/{date}", name="appointment_salesrep_schedule")
     */
    public function getSalesRepsSchedules(Request $request, $office, $date)
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
            , null
            , 'You don\'t have access to this page'
        );

        $qb = $this->getQueryBuilder($request);
        $qb->addOrderBy('salesRep.firstName', 'ASC')
          ->addOrderBy('salesRep.lastName', 'ASC')
          ->addOrderBy('e.datetime', 'ASC')
          ->andWhere("office.id = :office")
          ->setParameter("office", $office);

        $fieldname         = 'datetime';
        $doctrineFieldname = $this->getDoctrineFieldName($fieldname);

        $qb->andWhere("{$doctrineFieldname} >= :{$fieldname}min");
        $valueMin = new \DateTime($date);
        $valueMin->setTime(0, 0, 0);
        $qb->setParameter("{$fieldname}min", $valueMin);

        $qb->andWhere("{$doctrineFieldname} <= :{$fieldname}max");
        $valueMax = new \DateTime($date);
        $valueMax->setTime(23, 59, 59);
        $qb->setParameter("{$fieldname}max", $valueMax);

        $results = $qb->getQuery()->execute();
        $data = array();
        /** @var \Bundle\Entity\Appointment $appointment */
        foreach ($results as $appointment) {
            if ($appointment->getSalesRep()) {
                if (!array_key_exists($appointment->getSalesRep()->getId(), $data)) {
                    $data[$appointment->getSalesRep()->getId()] = array(
                      'id'        => $appointment->getSalesRep()->getId(),
                      'fistName'  => $appointment->getSalesRep()->getFirstName(),
                      'lastName'  => $appointment->getSalesRep()->getLastName(),
                      'schedules' => array(),
                    );
                }
                $data[$appointment->getSalesRep()->getId()]['schedules'][] = array(
                  'appointmentId' => $appointment->getId(),
                  'datetime'      => $appointment->getDatetime(),
                );

                $checks = array(
                    //'6:30 pm', '7:30 pm' on weekdays
                    'week' => array(
                        'next' => array('06:30 PM'),
                        'prev' => array('07:30 PM'),
                    ),
                    //'9:00 am', '10:00 am' / '1:00 pm', '2:00 pm' on saturdays?
                    'saturday' => array(
                        'next' => array('09:00 AM', '01:00 PM'),
                        'prev' => array('10:00 AM', '02:00 PM'),
                    )
                );
                $nexts = array(
                    '06:30 PM' => array(19, 30),
                    '09:00 AM' => array(10, 0),
                    '01:00 PM' => array(14, 0),
                );
                $prevs = array(
                  '07:30 PM' => array(18, 30),
                  '10:00 AM' => array(9, 0),
                  '02:00 PM' => array(13, 0),
                );
                $slot = $appointment->getDatetime()->format('h:i A');
                $check = $appointment->getDatetime()->format('w') === '6'
                    ? $checks['saturday']
                    : $checks['week'];
                if (in_array($slot, $check['next'])) {
                    $next = clone $appointment->getDatetime();
                    $time = $nexts[$slot];
                    $next->setTime($time[0], $time[1]);
                    $data[$appointment->getSalesRep()->getId()]['schedules'][] = array(
                      'appointmentId' => 'hold-next',
                      'datetime'      => $next,
                    );
                }
                if (in_array($slot, $check['prev'])) {
                    $prev = clone $appointment->getDatetime();
                    $time = $prevs[$slot];
                    $prev->setTime($time[0], $time[1]);
                    $data[$appointment->getSalesRep()->getId()]['schedules'][] = array(
                      'appointmentId' => 'hold-prev',
                      'datetime'      => $prev,
                    );
                }
            }
        }

        $data = array_values($data);

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }


    /**
     * @inheritdoc
     */
    public function getModelClass()
    {
        return '\App\Lev\CRMBundle\Entity\Appointment';
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
            array('name' => 'customer', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid_in', 'object' => '\App\Lev\CRMBundle\Entity\Customer'),
            array('name' => 'name', 'exposed' => true, 'saved' => false, 'filter' => 'customer_fullname'),
            array('name' => 'addressStreet', 'exposed' => true, 'saved' => true),
            array('name' => 'addressCity', 'exposed' => true, 'saved' => true),
            array('name' => 'addressState', 'exposed' => true, 'saved' => true),
            array('name' => 'addressZip', 'exposed' => true, 'saved' => true),
            array('name' => 'addressLat', 'exposed' => true, 'saved' => true),
            array('name' => 'addressLng', 'exposed' => true, 'saved' => true),
            array('name' => 'productInterest', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'roofAge', 'exposed' => true, 'saved' => true, 'filter' => 'integerRange'),
            array('name' => 'windowsQty', 'exposed' => true, 'saved' => true, 'filter' => 'integerRange'),
            array('name' => 'windowsLastReplaced', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'sidesQty', 'exposed' => true, 'saved' => true, 'filter' => 'integerRange'),
            array('name' => 'sidingAge', 'exposed' => true, 'saved' => true, 'filter' => 'integerRange'),
            array('name' => 'type', 'exposed' => true, 'saved' => true, 'filter' => 'string_in'),
            array('name' => 'status', 'exposed' => true, 'saved' => false, 'filter' => 'string_in'),
            array('name' => 'realStatus', 'exposed' => true, 'saved' => false),
            array('name' => 'notes', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'datetime', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'marketingRep', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid_in', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'salesRep', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid_in', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'sale', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Sale'),
            array('name' => 'products', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\SaleProduct'),
            array('name' => 'nearestSalesRep', 'exposed' => false, 'saved' => false),
            array('name' => 'resetBy', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'callcenterRep', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'histories', 'exposed' => false, 'saved' => false),
            array('name' => 'historyNote', 'exposed' => true, 'saved' => false),
            array('name' => 'demoed', 'exposed' => true, 'saved' => false, 'filter' => 'demoed'),
            array('name' => 'createdAt', 'exposed' => true, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'createdBy', 'exposed' => true, 'saved' => false,
                'filter' => 'objectid', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'attachments', 'exposed' => true, 'saved' => true),
            array('name' => 'deletedAttachments', 'exposed' => true, 'saved' => false),
            array('name' => 'insertedAttachments', 'exposed' => true, 'saved' => false),
            // Callcenter fields
            array('name' => 'lockedBy', 'exposed' => true, 'saved' => true,
                'filter' => 'objectid_in', 'object' => '\App\Lev\CRMBundle\Entity\Staff'),
            array('name' => 'callback', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'dueBy', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'doneAt', 'exposed' => false, 'saved' => false, 'filter' => 'daterange', 'date' => true),
            array('name' => 'outcome', 'exposed' => false, 'saved' => false, 'filter' => 'string'),
            array('name' => 'confirmedSalesRep', 'exposed' => true, 'saved' => true, 'filter' => 'boolean', 'boolean' => true),
            array('name' => 'date', 'exposed' => false, 'saved' => false),
            array('name' => 'marketerSource', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'cancelReason', 'exposed' => true, 'saved' => true),
            array('name' => 'pitchMissReason', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'noPitchReason', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'contractSignDate', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'contractCancellationDueDate', 'exposed' => true, 'saved' => true, 'filter' => 'daterange', 'date' => true),
            array('name' => 'contractDeliveryAddress', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'contractCustomerSignature1', 'exposed' => true, 'saved' => true),
            array('name' => 'contractCustomerSignature2', 'exposed' => true, 'saved' => true),
            array('name' => 'contractRecipientEmailAddress', 'exposed' => true, 'saved' => true),
            array('name' => 'contractNotifyFlag', 'exposed' => false, 'saved' => false),
            array('name' => 'paymentType', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'discountMethod', 'exposed' => true, 'saved' => true, 'filter' => 'string'),
            array('name' => 'discountPercentage', 'exposed' => true, 'saved' => true, 'filter' => 'floatrange'),
            array('name' => 'financing', 'exposed' => true, 'saved' => false, 'filter' => 'boolean', 'boolean' => true),
            array('name' => 'downPayment', 'exposed' => true, 'saved' => true, 'filter' => 'floatRange'),
            array('name' => 'salesTax', 'exposed' => true, 'saved' => true, 'filter' => 'floatrange'),
            array('name' => 'totalLiquid', 'exposed' => false, 'saved' => false),
            array('name' => 'creditQualityValue', 'exposed' => true, 'saved' => true, 'filter' => 'integerrange',),
             array('name' => 'isdeleted', 'exposed' => true, 'saved' => true),

        );

        $config->setQueryMaxPerPage(20)
            ->setFieldsFromArray($fields)
            ->setDefaultRoles('appointment')
            ->setQuerySort(array(
                'createdAt' => 'DESC'
            ));
    }

    /**
     * @inheritdoc
     */
    public function getQueryBuilder(Request $request)
    {
        $qb = $this->getRepository()->createQueryBuilder('e') ->where('e.isdeleted = 0');

        if (! $request->get('ignoreOffice', false)) {
            if ($this->getUser()->isGroupAdmin() && $request->get('officeid', false)) {
                $qb->innerJoin('e.office', 'office')
                    ->where('office.id = :office_id')
                    ->andWhere('e.isdeleted = 0')
                    ->setParameter('office_id', $request->get('officeid', false));

            } else {
                $qb->innerJoin('e.office', 'office')
                    ->where('office.id = :office_id')
                    ->andWhere('e.isdeleted = 0')
                    ->setParameter('office_id', $this->getOffice()->getId());


            }
        }
        $qb->leftJoin('e.products', 'products')
            ->leftJoin('e.customer', 'customer')
            ->leftJoin('e.salesRep', 'salesRep')
            ->leftJoin('e.marketingRep', 'marketingRep')
            ->leftJoin('e.lockedBy', 'lockedBy')

            ;

        return $qb;
    }

    /**
     * @inheritdoc
     * @SWG\Tag(name="Appointment")
     * @SWG\Response(
     *     response=200,
     *     description="update appointment schedule")
     */
    protected function updateRecord($record, Request $request)
    {
        
        
        $old = array(
            'status'        => $record->getStatus(),
            'datetime'      => $record->getDateTime() instanceof \DateTime ? clone $record->getDateTime(): $record->getDateTime(),
            'callback'      => $record->getCallback() instanceof \DateTime ? clone $record->getCallback() : $record->getCallback(),
            'salesRep'      => $record->getSalesRep(),
            'callcenterRep' => $record->getCallcenterRep(),
            'resetBy'       => $record->getResetBy(),
        );
        

        $record = parent::updateRecord($record, $request);
        
        $data = $request->request->all();
        
       
        
       $now = new \DateTime();
      
        foreach( $request->files->all() as $file)
        {
           
            $internalFile = new UploadedFile($file[0]->getRealPath(), md5( $now->format('YMDHis') . $file[0]->getFilename()));
            // dd($internalFile);
            $internalFile->setFilename($now->format('YMDHis') . $file[0]->getFilename());
           
            
        $this->get('lev_crm.service.fileupload')->save($record,$internalFile);
        }

        if (array_key_exists('status', $data)) {
            $record->setStatus($data['status']);
        }

        $customer       = $record->getCustomer();
        $historyMessage = array();
        $messageCode    = 3;

        $callService = $this->get('lev_crm.service.call');

        if (array_key_exists('outcome', $data) && null !== $data['outcome'] && $data['outcome'] !== '') {
            $record->setCallcenterRep($this->getUser());
            $callService->addCall($record, $data['outcome'], false);
            $messageCode = 5;
            $callback = new \Datetime();

            switch($data['outcome']) {

                case 'tcpa':
                    $customer->setTcpa(true);
                    $this->getManager()->persist($customer);
                    $historyMessage[] = 'TCPA set for customer - appointment canceled';
                    $record->setStatus('canceled')
                        ->setCallback(null)
                        ->setCancelReason('tcpa');
                    break;

                case 'wrong-number':
                    $customer->setWrongNumber(true);
                    $this->getManager()->persist($customer);
                    $historyMessage[] = 'Wrong Number set for customer - appointment canceled';
                    $record->setStatus('canceled')
                        ->setCallback(null)
                        ->setCancelReason('wrong-number');
                    break;

                case 'confirmed':
                    $record->setStatus('confirmed')
                        ->setCallback(null);
                    $historyMessage[] = 'Scheduled confirmed';
                    break;

                case 'no-answer':
                    $noAnswerCount = $record->getCallsCountByOutcome('no-answer');
                    $callNo = $noAnswerCount + 1;
                    switch($noAnswerCount) {
                        case 0:
                            $callback->add(new \DateInterval('PT25M'));
                            break;
                        default:
                        case 1:
                            $callback->add(new \DateInterval('PT45M'));
                            break;
                    }
                    $historyMessage[] = "No answer #{$callNo}, callback in "
                                      . $callback->format('M d H:i');
                    $record->setCallback($callback);
                    break;

                case 'left-message':
                    $noAnswerCount = $record->getCallsCountByOutcome('left-message');
                    $callNo = $noAnswerCount + 1;
                    $callback->add(new \DateInterval('PT25M'));
                    $historyMessage[] = "Left message #{$callNo}, callback in "
                                      . $callback->format('M d H:i');
                    $record->setCallback($callback);
                    break;

                case 'busy-signal':
                    $noAnswerCount = $record->getCallsCountByOutcome('busy-signal');
                    $callNo = $noAnswerCount + 1;
                    $callback->add(new \DateInterval('PT25M'));
                    $historyMessage[] = "Busy signal #{$callNo}, callback in "
                                      . $callback->format('M d H:i');
                    $record->setCallback($callback);
                    break;

                case 'canceled':
                    $historyMessage[] = 'Appoinment Canceled';
                    if (array_key_exists('cancelReason', $data)) {
                        $record->setCancelReason($data['cancelReason']);
                    }
                    $record->setStatus('canceled')
                        ->setCallback($callback);
                    break;

                case 'no-pitch':
                    $historyMessage[] = 'No Pitch';
                    if (array_key_exists('noPitchReason', $data)) {
                        $record->setNoPitchReason($data['noPitchReason']);
                    }
                    $record->setStatus('no-pitch')
                        ->setCallback($callback);
                    break;

                case 'pitch-miss':
                    $historyMessage[] = 'Pitch Miss';
                    if (array_key_exists('pitchMissReason', $data)) {
                        $record->setPitchMissReason($data['pitchMissReason']);
                    }
                    $record->setStatus('pitch-miss')
                        ->setCallback($callback);
                    break;

                case 'sold':
                    $historyMessage[] = 'Sold';
                    $record->setStatus('sold')
                        ->setCallback($callback);
                    $this->isSoldNow = true;
                    break;

                default:
                case 'schedule-call-back-updated':
                case 'shedule-call-back-updated':
                    $oldDatetime = $old['datetime'] instanceof \DateTime ? $old['datetime']->format('M d Y, H:i') : 'n/a';
                    $newDatetime = $record->getDatetime() instanceof \DateTime ? $record->getDatetime()->format('M d Y, H:i') : 'n/a';
                    $oldCallback = $old['callback'] instanceof \DateTime ? $old['callback']->format('M d Y, H:i') : 'n/a';
                    $newCallback = $record->getCallback() instanceof \DateTime ? $record->getCallback()->format('M d Y, H:i') : 'n/a';

                    if ($oldDatetime !== $newDatetime && null !== $record->getDatetime()) {
                        // $this->get('logger')->info("CALLCENTER (outcome) New datetime set: $oldDatetime => $newDatetime");
                        $callback = clone $record->getDatetime();
                        $callback->sub(new \DateInterval('PT3H'));
                        $record->setStatus('scheduled')
                             ->setConfirmedSalesRep(false)
                             ->setCallback($callback);
                        $callService->addCall($record, 'scheduled', false);
                        $historyMessage[] = "Scheduled changed from $oldDatetime to $newDatetime";
                        // Warns salesRep
                        $now = new \DateTime();
                        if (null !== $record->getSalesRep() && $record->getDatetime() > $now) {
                          $this->warnSalesRep = true;
                        }
                    } else if ($oldCallback !== $newDatetime && $record->getCallback() >= new \DateTime()) {
                        $callService->addCall($record, 'call-later', false);
                    }
                    break;
            }

        } else {
            $oldDatetime = $old['datetime'] instanceof \DateTime ? $old['datetime']->format('M d Y, H:i') : 'n/a';
            $newDatetime = $record->getDatetime() instanceof \DateTime ? $record->getDatetime()->format('M d Y, H:i') : 'n/a';
            $oldCallback = $old['callback'] instanceof \DateTime ? $old['callback']->format('M d Y, H:i') : 'n/a';
            $newCallback = $record->getCallback() instanceof \DateTime ? $record->getCallback()->format('M d Y, H:i') : 'n/a';

            if ($oldDatetime !== $newDatetime && null !== $record->getDatetime()) {
                // $this->get('logger')->info("CALLCENTER (NO outcome) New datetime set: $oldDatetime => $newDatetime");
                $callback = clone $record->getDatetime();
                $callback->sub(new \DateInterval('PT3H'));
                $record->setStatus('scheduled')
                     ->setConfirmedSalesRep(false)
                     ->setCallback($callback);
                $callService->addCall($record, 'scheduled', false);
                $historyMessage[] = "Scheduled changed from $oldDatetime to $newDatetime";
                // Warns salesRep
                $now = new \DateTime();
                if (null !== $record->getSalesRep() && $record->getDatetime() > $now) {
                  $this->warnSalesRep = true;
                }
            } else if ($oldCallback !== $newDatetime && $record->getCallback() >= new \DateTime()) {
                $callService->addCall($record, 'call-later', false);
            }
        }

        // Status history and if it's SOLD, mark as sold on controller to
        // create sold automaticly
        if ($old['status'] !== $record->getStatus()) {
            $historyMessage[] = "Status changed from '{$old['status']}' to '" . $record->getStatus() . "'";
            if ($record->getStatus() === 'sold') {
                $this->isSoldNow = true;
            }
        }

        // SalesRep changes - need to warn salesRep
        if ($old['salesRep'] !== $record->getSalesRep()) {
            $oldSalesRep = $old['salesRep'] instanceof Staff ? $old['salesRep']->getFullname() : 'n/a';
            $newSalesRep = $record->getSalesRep() instanceof Staff ? $record->getSalesRep()->getFullname() :  'n/a';
            $historyMessage[] = "Scheduled changed from $oldSalesRep to $newSalesRep";
            // Warns salesRep
            $now = new \DateTime();
            if (null !== $record->getSalesRep() && $record->getDatetime() > $now) {
              $this->warnSalesRep = true;
            }
        }

        // CallcenterRep changes - or just set the acctual user
        if ($old['callcenterRep'] !== $record->getCallcenterRep()) {
          $historyMessage[] = (null === $old['callcenterRep'])
          ? "Callcenter changed to {$record->getCallcenterRep()->getFullName()}"
          : "Callcenter changed from {$old['callcenterRep']->getFullName()} to {$record->getCallcenterRep()->getFullName()}";
        }

        // Set who reset the appointment
        if ($old['status'] === "no-pitch") {
            $record->setResetBy($this->getUser());
            $historyMessage[] = "Callcenter reset by {$record->getCallcenterRep()->getFullName()}";
        }

        // Callback changes
        $oldCallback = $old['callback'] instanceof \DateTime ? $old['callback']->format('M d Y, H:i') : 'n/a';
        $newCallback = $record->getCallback() instanceof \DateTime ? $record->getCallback()->format('M d Y, H:i') : 'n/a';
        if ($oldCallback !== $newCallback) {
            $historyMessage[] = "Callback changed from $oldCallback to $newCallback";
        }

        if ($request->getMethod() === 'PUT') {
          // Avoid default history
          // @see \Lev\CRMBundle\ControllerAbstractAPICRMController:>updateRecord
          $this->customHistory = true;

          $historyMessage = implode("\n", $historyMessage);
          if (count($historyMessage)) {
            $historyService = $this->get('lev_crm.service.history');
            $historyService->addHistory($record, $messageCode, $historyMessage, false);
          }

        }

        // Product calculater update
        $productCalculatorService = $this->get('lev_crm.service.productcalculator');
        $record = $productCalculatorService->updateProducts($record, $data);

        // TODO
        // $this->warnSalesRep = false;


        

        if (null === $record->getId()) {
            $record->setCallcenterRep($this->getUser());
        }


        return $record;
    }

    /**
     * @SWG\Tag(name="Calls")
     * @SWG\Response(
     *     response=200,
     *     description="get callcenters on hold")
     * @Get("/calls/callcenter/hold/{id}", name="calls_callcenter_hold")
     */
    public function hold(Request $request, $id)
    {
        return $this->holdRelease($request, 'hold', $id);
    }

    /**
     * @SWG\Tag(name="Calls")
     * @SWG\Response(
     *     response=200,
     *     description="get callcenters center release by id")
     * @Get("/calls/callcenter/release/{id}", name="calls_callcenter_release")
     */
    public function release(Request $request, $id)
    {
        return $this->holdRelease($request, 'release', $id);
    }

    /**
     * @inheritdoc
     */
    protected function holdRelease(Request $request, $action, $id)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('UPDATE')
            , null
            , 'You don\'t have access to this page'
        );

        try {
            $record = $this->getAppointmentById($id);

            if (!$record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }

            if (!$request->isMethod('GET')) {
                throw new \Exception(
                    'Method not allowed (expected GET)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $data = array();
            $historyService = $this->get('lev_crm.service.history');
            $callService = $this->get('lev_crm.service.call');
            switch ($action) {
                case 'hold':
                    if ($record->getLockedBy() !== null) {
                        $message = $this->getUser()->getFullName()
                                 . ' tried to lock a call, but already locked by '
                                 . $record->getLockedBy()->getFullName();
                        $data = array(
                            'hold'    => false,
                            'message' => $message,
                            'record'  => $this->prepareRecord($record),
                        );
                        $historyService->addHistory($record, 6, $message);
                        return $this->renderJsonResponse($data, Response::HTTP_OK);
                    }
                    $limit = new \Datetime();
                    $limit->add(new \DateInterval('PT5M'));
                    $request->request->set('dueBy', $limit->format('Y-M-d H:i:s'));
                    $request->request->set('lockedBy', $this->getUser()->getId());
                    $message = 'Call locked by ' . $this->getUser()->getFullName();
                    $data = array(
                        'hold'    => true,
                        'message' => $message
                    );
                    $callService->lockCall($record, $this->getUser());
                    break;

                case 'release':
                    $request->request->set('dueBy', null);
                    $request->request->set('lockedBy', null);
                    $message = 'Call unlocked by ' . $this->getUser()->getFullName();
                    $data = array(
                        'release' => true,
                        'message' => $message
                    );
                    $callService->unlockCall($record);
                    break;

                default:
                    throw new Exception("Invalid action - must be 'hold' or 'release'", Response::HTTP_BAD_REQUEST);
            }

            $this->getManager()->persist($record);
            $this->getManager()->flush();
            $data['record'] = $this->prepareRecord($record);

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @SWG\Tag(name="Calls")
     * @SWG\Response(
     *     response=200,
     *     description="get call center calls")
     * @Get("/calls/callcenter/{method}/{type}", name="calls_callcenter")
     */
    public function getCalcenterCalls(Request $request, $method, $type)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('VIEW')
            , null
            , 'You don\'t have access to this page'
        );

        try {

            if (!$request->isMethod('GET')) {
                throw new \Exception(
                    'Method not allowed (expected GET)',
                    Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $callService = $this->get('lev_crm.service.call');
            $userId = $this->getUser()->getId();
            switch ($method) {
                default:
                case 'outbound':
                    $data = $callService->getOutboundCalls($type, $userId);
                    break;
            }


         } catch (\Exception $e) {
             return $this->renderError($e);
         }

         return $this->renderJsonResponse($data, Response::HTTP_OK);
     }

     /**
      * @SWG\Tag(name="Appointment")
      * @SWG\Response(
      *     response=200,
      *     description="get an appointment")  
      * Get record by request data
      *
      * @param Request $request The Request
      * @param integer $id      The record primary key
      *
      * @return mixed
      */
    public function getOneByRequest(Request $request, $id, $hydrate = false)
    {
        $this->getConfig()->addFieldFromArray(array(
            'name' => 'histories', 'exposed' => true, 'saved' => false
        ));
        $this->classArrayType = 'toArrayWithHistory';

        $qb = $this->getRepository()->createQueryBuilder('e');
        $qb->leftJoin('e.products', 'products')
            ->leftJoin('e.customer', 'customer')
            ->leftJoin('e.salesRep', 'salesRep')
            ->leftJoin('e.marketingRep', 'marketingRep')
            ->leftJoin('e.lockedBy', 'lockedBy')
            ->leftJoin('e.histories', 'histories');

        return $qb->andWhere('e.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @inheritdoc
     * @SWG\Tag(name="Appointment")
     * @SWG\Response(
     *     response=200,
     *     description="update appointment")
     */
    public function putAction(Request $request, $id)
    {
        $this->denyAccessUnlessGranted(
            $this->getRoleName('UPDATE')
            , null
            , 'You don\'t have access to this page'
        );

        try {
            $record = $this->getOneByRequest($request, $id, true);

            if (!$record) {
                throw new \Exception(
                    'Record not found'
                    , self::ERR_RECORD_NOT_FOUND
                );
            }

            if (!$request->isMethod('PUT')) {
                throw new \Exception(
                    'Method not allowed (expected PUT)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $record = $this->updateRecord($record, $request);
            $errors = $this->getValidator()->validate($record);

            if (count($errors) > 0) {
                return $this->renderValidationErrors($errors);
            }

            $this->getManager()->persist($record);
            $this->getManager()->flush();
            $data = $this->prepareRecord($record);

            // Need to warn salesRep
            if ($this->warnSalesRep) {
                $this->get('lev_crm.service.mail')->warnSalesRep($record);
                // $sms = $this->get('lev_crm.service.sms')->warnSalesRep($record);
            }
            // Sending contract to email
            $dataRequest = $request->request->all();
            if (array_key_exists('contractNotifyFlag', $dataRequest)
              && ($dataRequest['contractNotifyFlag'] === true || $dataRequest['contractNotifyFlag'] === 1)
            ) {
                $this->get('lev_crm.service.mail')->sendContract($record);
            }
            // It's sold, let's create a sale
            if ($this->isSoldNow) {
                $this->get('lev_crm.service.appointment')->createSaleByAppointment($record);
            }

        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @inheritdoc
     * @SWG\Tag(name="Appointment")
     * @SWG\Response(
     *     response=200,
     *     description="create appointment")
     */
    public function postAction(Request $request)
    {

       
        $this->denyAccessUnlessGranted(
            $this->getRoleName('CREATE')
            , null
            , 'You don\'t have access to this page'
        );

        try {

            if (!$request->isMethod('POST')) {
                throw new \Exception(
                    'Method not allowed (expected POST)'
                    , Response::HTTP_METHOD_NOT_ALLOWED
                );
            }

            $class = $this->getModelClass();
            $record = new $class;



            $record = $this->updateRecord($record, $request);

          
            
            $errors = $this->getValidator()->validate($record);

            if (count($errors) > 0) {
                return $this->renderValidationErrors($errors);
            }

            $this->getManager()->persist($record);
            $this->getManager()->flush();
            $data = $this->prepareRecord($record);
            
            // Need to warn salesRep
            if ($this->warnSalesRep) {
                $this->get('lev_crm.service.mail')->warnSalesRep($record);
              //  $sms = $this->get('lev_crm.service.sms')->warnSalesRep($record);
            }
            // Sending contract to email

            
               
               
           
            $dataRequest = $request->request->all();
           
          
           

            if (array_key_exists('contractNotifyFlag', $data)
              && ($dataRequest['contractNotifyFlag'] === true || $dataRequest['contractNotifyFlag'] === 1)
            ) {
                $this->get('lev_crm.service.mail')->sendContract($record);
               
            }
            // It's sold, let's create a sale
            if ($this->isSoldNow) {
                $this->get('lev_crm.service.appointment')->createSaleByAppointment($record);
            }

            // if (array_key_exists('insertedAttachments', $dataRequest) && !empty($dataRequest['insertedAttachments'])) {
            //      $this->get('lev_crm.service.fileupload')->save($dataRequest['insertedAttachments'], true);
            //      }

            // if($dataRequest['insertedAttachments']!=null)
            // {
               
            //     
            // }


        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_CREATED);
    }


     public function  deleteAction(Request $request, $id)
    {

    $em = $this->getDoctrine()->getManager();     
$query = $em->getRepository('\App\Lev\CRMBundle\Entity\Appointment')->createQueryBuilder('')
            ->update('\App\Lev\CRMBundle\Entity\Appointment', 'u')

            ->set('u.isdeleted', ':isdeleted')
            ->setParameter('isdeleted', 1)

            ->where('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery();

$result = $query->execute();

 return $this->renderJsonResponse($result, Response::HTTP_OK);
}

}
