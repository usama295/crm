<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 07/05/15
 * Time: 18:58
 */

namespace App\Lev\CRMBundle\Service;

use Swift_Mailer;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\Sale;
use Symfony\Bundle\TwigBundle\TwigEngine;
/**
 * Mailer Service
 *
 * @category Service
 * @package  Lev\CRMBundle\Service
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class MailService
{

    /**
     * @var Swift_Mailer
     */
    protected $mailer;

    /**
     * @var array
     */
    protected $from;

    /**
     * @var Templating
     */
    protected $templating;

    /**
     * @var string
     */
    protected $appUrl;

    /**
     * Constructor
     *
     * @param Swift_Mailer $mailer
     */
    public function __construct(Swift_Mailer $mailer, array $from, TwigEngine $templating, $appUrl)
    {
        $this->mailer     = $mailer;
        $this->from       = $from;
        $this->templating = $templating;
        $this->appUrl     = $appUrl;
    }

    /**
     * Warn Sales Rep
     *
     * Names
     * Address (google link)
     * Product interested in
     * Time of appt
     *
     * @param  Appointment $appointment
     * @return boolean
     */
    public function warnSalesRep(Appointment $appointment)
    {
        $subject = 'ODR CRM Alert for Sales Representative';
       $html = $this->templating->render(
            'LevCRMBundle:Mail:warnSalesRep.html.twig',
            array(
                'appointment' => $appointment,
                'app_url'     => $this->appUrl ? $this->appUrl : 'http://odr-crm.web.lev-interactive.com',
            )
        );

        $salesRep = $appointment->getSalesRep();

         //$message = $this->mailer->getMessage();
        $message = new \Swift_Message('Hello Email');
        $message->setFrom(array($this->from['email'] => $this->from['name']));
        $message
            ->setSubject($subject)
            ->setTo(
                array($salesRep->getEmail() => $salesRep->getFullName())
            )
            ->setBody($html, 'text/html');
        return $this->mailer->send($message);
    }

    /**
     * Warn Sales Rep
     *
     * Names
     * Address (google link)
     * Product interested in
     * Time of appt
     *
     * @param  Appointment $appointment
     * @return boolean
     */
    public function sendContract(Appointment $appointment)
    {
        $subject = 'ODR CRM Contract';
        $html = $this->templating->render(
            'LevCRMBundle:Mail:contract.html.twig',
            array(
                'appointment' => $appointment,
                'app_url' => $this->appUrl ? $this->appUrl : 'http://odr-crm.web.lev-interactive.com',
            )
        );

        $message = \Swift_Message::newInstance();
        $message->setFrom(array($this->from['email'] => $this->from['name']));
        $message
            ->setSubject($subject)
            ->setTo(
                array($appointment->getContractRecipientEmailAddress())
            )
            ->setBody($html, 'text/html');
        return $this->mailer->send($message);
    }


}
