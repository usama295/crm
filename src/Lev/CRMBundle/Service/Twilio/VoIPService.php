<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service\Twilio;
// require __DIR__ . '/vendor/autoload.php';
// require '../vendor/autoload.php';

// use Services_Twilio;
use Services_Twilio_Twiml;
// use Services_Twilio_Capability;
use Twilio\Exceptions\RestException;
use Twilio\Rest\Client;
use Twilio\Jwt\ClientToken;


use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Lev\CRMBundle\Entity\Staff;
use App\Lev\CRMBundle\Entity\Customer;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\CallRecord;
use App\Lev\CRMBundle\Service\HistoryService;
use Symfony\Bridge\Monolog\Logger;

use Twilio\Twiml;

/**
 * VoIP Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class VoIPService
{
    /**
     * @var string
     */
    protected $accountSid;

    /**
     * @var string
     */
    protected $authToken;

    /**
     * @var string
     */
    protected $from;

    /**
     * @var string
     */
    protected $applicationSid;

    /**
     * @var Logger
     */
    protected $logger;

    /**
     * @var HistoryService
     */
    protected $historyService;

    /**
     * @var string
     */
    protected $apiUrl;

    /**
     * @var string
     */
    protected $twilio_number;

    /**
     * @var string
     */
    protected $outboundUrl;

    /**
     * @var string
     */
    protected $statusCallbackUrl;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     * @param Logger $logger
     * @param HistoryService $historyService
     * @param TwigEngine $templating
     * @param string $accountSid
     * @param string $authToken
     * @param string $applicationSid
     * @param string $delivery
     * @param string $apiUrl
     * @param string $twilio_number
     */
    public function __construct(Registry $doctrine, Logger $logger, HistoryService $historyService, $accountSid, $authToken, $applicationSid, $delivery, $apiUrl,$twilio_number)
    {
        $this->doctrine            = $doctrine;
        $this->logger              = $logger;
        $this->historyService      = $historyService;
        $this->accountSid          = $accountSid;
        $this->authToken           = $authToken;
        $this->applicationSid      = $applicationSid;
        $this->delivery            = $delivery;
        $this->apiUrl              = $apiUrl;
        $this->outboundUrl         = $apiUrl . '/twilio/outbound/';
        $this->statusCallbackUrl   = $apiUrl . '/twilio/statuscallback/';
        $this->twilio_number       = $twilio_number;
    }

    /**
     * Do a call
     * @param  string $from
     * @param  string $to
     * @param  integer $appointmentId
     * @return [type]
     */
    public function call($from, $to, $appointmentId)
    {
        // $client = new Services_Twilio($this->accountSid, $this->authToken);
        $client = new Client($this->accountSid, $this->authToken);
        
        $this->logger->debug('TWILIO VoIP Call start');

        $from = $this->delivery && is_array($this->delivery)
        && array_key_exists('from', $this->delivery)
        && !empty($this->delivery['from'])
            ? $this->delivery['from'] : $from;
        
        if($from == "")
        {
            $from = $this->twilio_number;
        }
        
        if (strlen($from) < 10) {
            $msg = "TWILIO Call Error: $from \"from\" number is invalid";
            $this->logger->debug($msg);
            throw new \Exception($msg, 404);
        }
        $from = ($from[0] !== '+' ? '+1' : '+') . preg_replace('/\D/', '', $from);

        $to = $this->delivery && is_array($this->delivery)
        && array_key_exists('to', $this->delivery)
        && !empty($this->delivery['to'])
            ? $this->delivery['to'] : $to;
        
        if (strlen($to) < 10) {
            $msg = "TWILIO Call Error: $to \"to\" number is invalid";
            $this->logger->debug($msg);
            throw new \Exception($msg, 404);
        }
        $to = ($to[0] !== '+' ? '+1' : '+') . preg_replace('/\D/', '', $to);
        $this->logger->debug("TWILIO VoIP Call from $from to  $to");
        
        /* Old Code
        $call = $client->account->calls->create(
            $from,
            $to,
            $this->outboundUrl . $appointmentId,
            array(
                'Record' => 'true',
                'StatusCallback' => $this->statusCallbackUrl . $appointmentId,
                'StatusCallbackMethod' => 'POST',
                'StatusCallbackEvent' => array('completed'),
            )
        );
        */
       
        /* SMS Code
        $message = $client->messages->create(
            $to_number,
            array(
                "from" => $from, 
                "body" => "McAvoy or Stewart? These timelines can get so confusing."
            )
        );
        print($message->sid);exit;
        */
        
        $call = $client->account->calls->create(
            $to,
            $this->twilio_number,
            array(
                "record" => True,
                // "statusCallback" => $this->statusCallbackUrl . $appointmentId,
                // 'StatusCallbackEvent' => array('completed'),
                // "statusCallbackMethod" => "POST",
                "url" => "http://demo.twilio.com/docs/voice.xml"
            )
        );
        // print($call->sid);
        $this->logger->debug("TWILIO VoIP Call ID {$call->sid}");

        return $call;
    }

    /**
     * Get Outbound
     *
     * @param  Appointment $appointmentId
     * @return \Services_Twilio_Twiml(
     */
    public function outbound(Appointment $appointment, $from, $to)
    {
        $message = 'Please hold while we connect you with the customer.';

        if (!empty($this->delivery['from'])) {
            $from = $this->delivery['from'];
        }

        $from = ($from[0] !== '+' ? '+1' : '+') . preg_replace('/\D/', '', $from);
        $to = ($to[0] !== '+' ? '+1' : '+') . preg_replace('/\D/', '', $to);

        $twiml = new Services_Twilio_Twiml();
        $twiml->say($message, array('voice' => 'alice'));
        // $twiml->record(array('action' => $this->statusCallbackUrl . $appointment->getId()));
        $twiml->dial($to, array(
          'callerId' => $from,
          'record'   => 'record-from-answer',
          'action'   => $this->statusCallbackUrl . $appointment->getId()
        ));

        return $twiml;
    }

    /**
     * Get Capability Token
     *
     * @see https://twilio-php.readthedocs.org/en/latest/usage/token-generation.html
     * @param  string $username
     * @return string
     */
    public function token($username)
    {
        // $capability = new Services_Twilio_Capability($this->accountSid, $this->authToken);
        /* token generation without twiml apps
            $capability = new ClientToken($this->accountSid, $this->authToken);
            $this->logger->debug('TWILIO VoIP Token start');
            
            $params = array("username" => $username);
            $capability->allowClientOutgoing($this->applicationSid, $params);
            $token = $capability->generateToken();
        */
        
        /* token generation with twiml apps */
        
        //$this->logger->debug("accountSid $this->accountSid == 'AC2c46557377a3178a1dc2ade6c17dea15'");
        //$this->logger->debug("authToken $this->authToken == '2fd771b190a2be1a02b45b1376f41961' ");
        //$this->logger->debug("applicationSid $this->applicationSid == 'APf591775702a74eebbccbc4f5bd4debc4'");


        $capability = new ClientToken($this->accountSid , $this->authToken );
        $capability->allowClientOutgoing($this->applicationSid);
        $capability->allowClientIncoming($username = "Anamayous");
        $token = $capability->generateToken();


        $this->logger->debug("TWILIO VoIP Token for user {$username}: $token");

        return $token;

    }

    /**
     * Create Call Record
     *
     * @see https://www.twilio.com/docs/api/twiml/twilio_request#synchronous
     * @see https://www.twilio.com/docs/api/rest/making-calls#status-callback-parameter
     * @param  Appointment $appointemnt
     * @param  array $data
     * @return CallRecord
     */
    public function createCallRecord(Appointment $appointemnt, array $data)
    {

        switch($data['Direction']) {

            /**
             * Just record info
             * RecordingUrl is useful, but we will get from extra request
             * DialCallSid has the reference to dial call'
             * {
             *   "ApiVersion":"2010-04-01",
             *   "Called":"---",
             *   "CallStatus":"completed",
             *   "RecordingSid":"RE57ebb0983bab9662f859e0dfbd547ced",
             *   "RecordingUrl":"https:\/\/api.twilio.com\/2010-04-01\/Accounts\/ACd7403aa426c1bdf14e6933d29f6618c2\/Recordings\/RE57ebb0983bab9662f859e0dfbd547ced",
             *   "From":"client:Anonymous",
             *   "DialCallStatus":"completed",
             *   "Direction":"inbound",
             *   "AccountSid":"ACd7403aa426c1bdf14e6933d29f6618c2",
             *   "DialCallDuration":"4",
             *   "ApplicationSid":"APf591775702a74eebbccbc4f5bd4debc4",
             *   "Caller":"client:Anonymous",
             *   "DialCallSid":"CAdb532a51fda319c4577822bc0c0c3162",
             *   "CallSid":"CA81d9de95706c7778a2baae25337075a6",
             *   "To":"---",
             *   "RecordingDuration":"14"
             * }
             */
            case 'inbound':
                $client = new Services_Twilio($this->accountSid, $this->authToken);
                $call = $client->account->calls->get($data['DialCallSid']);
                $data['To'] = $call->to;
                $data['From'] = $call->from;

                $callRecord = new CallRecord;
                $callRecord
                    ->setCallSid($data['CallSid'])
                    ->setFromNumber(urldecode($data['From']))
                    ->setToNumber(urldecode($data['To']))
                    ->setCallStatus($data['CallStatus'])
                    ->setRecordingUrl(array_key_exists('RecordingUrl', $data) ? $data['RecordingUrl'] : null)
                    ->setRecordingSid(array_key_exists('RecordingSid', $data) ? $data['RecordingSid'] : null)
                    ->setRecordingDuration(array_key_exists('RecordingDuration', $data) ? $data['RecordingDuration'] : null)
                    ->setTimestamp(array_key_exists('Timestamp', $data) ? $data['Timestamp'] : null)
                    ->setCallbackSource(array_key_exists('CallbackSource', $data) ? $data['CallbackSource'] : null)
                    ->setSequenceNumber(array_key_exists('SequenceNumber', $data) ? $data['SequenceNumber'] : null)
                    ->setOffice($appointemnt->getOffice())
                    ->setCustomer($appointemnt->getCustomer())
                    ->setAppointment($appointemnt);

                $this->doctrine->getManager()->persist($callRecord);
                $this->doctrine->getManager()->flush();

                $this->historyService->addHistory($appointment, 5, "Callback Status: {$data['CallStatus']}", true);
                break;

            // Everything in one call entry, no need to get extra info
            case 'outbound-api':
            case 'outbound-dial':
            default:
                // ignore
                $callRecord = null;
                break;

        }
        return $callRecord;
    }

    /**
     * Get Url
     * @return string
     */
    public function getOutboundUrl()
    {
        return $this->outboundUrl;
    }

    /**
     * Get Status Callback
     * @return string
     */
    public function getStatusCallbackUrl()
    {
        return $this->statusCallbackUrl;
    }

     public function voice($number = 0) {

        $response = new Twiml;
        $number = htmlspecialchars($number);
        // get the phone number from the page request parameters, if given
        if ($number > 0) {
            
            $dial = $response->dial(array('callerId' => $this->twilio_number));
            
            // wrap the phone number or client name in the appropriate TwiML verb
            // by checking if the number given has only digits and format symbols
            if (preg_match("/^[\d\+\-\(\) ]+$/", $number)) {
                $dial->number($number);
            } else {
                $dial->client($number);
            }
        } else {
            $response->say("Thanks for calling!");
        }

        
        return  $response;
    }
}
