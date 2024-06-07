<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service\Twilio;

use Services_Twilio;
use App\Lev\CRMBundle\Entity\Appointment;
use Symfony\Bundle\TwigBundle\TwigEngine;
use Symfony\Bridge\Monolog\Logger;
/**
 * Mailer Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class SMSService
{

    /**
     * @var string
     */
    protected $appUrl;

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
    protected $delivery;

    protected $logger;
    /**
     * Constructor
     *
     * @param Swift_Mailer $mailer
     */
    public function __construct(Logger $logger, $appUrl, $accountSid, $authToken, $from,  $delivery = false)
    {
        $this->logger     = $logger;
        $this->appUrl     = $appUrl;
        $this->accountSid = $accountSid;
        $this->authToken  = $authToken;
        $this->from       = $from;
        $this->delivery   = $delivery;
    }

    /**
     * Warn Sales Rep
     *
     * @param  Appointment $appointment
     * @return boolean
     */
    public function warnSalesRep(Appointment $appointment)
    {
        try {
            $client = new Services_Twilio($this->accountSid, $this->authToken);
            $this->logger->debug('TWILIO SMS start');

            $from = '+1' . $this->from;
            $to = $this->delivery && !empty($this->delivery)
                  ? $this->delivery
                  : $appointment->getSalesRep()->getPhoneMobile();
            $this->logger->debug("TWILIO to TEST $to". ' - ('.strlen($to).')' . '['.$to[0].']');
            $to = ($to[0] !== '+' ? '+1' : '+') . preg_replace('/\D/', '', $to);

            $this->logger->debug("TWILIO SMS from $from to $to". ' - ('.strlen($to).')');
            if (strlen($to) > 10) {
                $sms = $client->account->messages->sendMessage(
                    $from,
                    $to,
                    "ODR CRM Alert: {$appointment->getName()}, scheduled for "
                    . $appointment->getDatetime()->format('M, d Y h:i A') . "\n"
                    . "Address: {$appointment->getAddressStreet()}, {$appointment->getAddressCity()}"
                    . " / {$appointment->getAddressState()} - {$appointment->getAddressZip()} \n"
                    . " - confirm link {$this->appUrl}/appointment/confirm/{$appointment->getId()}"
                );
                $this->logger->debug('TWILIO SMS sent: ' . $sms);
            } else {
                $sms = 'TWILIO SMS INVALID number "' . $to . '", SMS *NOT* sent';
                $this->logger->debug($sms);
            }
        } catch (\Exception $e) {
            $sms = 'TWILIO SMS ERROR: Invalid other error, SMS *NOT* sent to"' . $to . '" => ' . $e->getMessage();
            $this->logger->debug($sms);
        }

        return $sms;
    }

}
