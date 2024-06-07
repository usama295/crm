<?php

namespace App\Lev\CRMBundle\Controller;

use App\Lev\APIBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use App\Lev\CRMBundle\Entity\Appointment;
use Swagger\Annotations as SWG;
use Twilio\Jwt\ClientToken;

use Twilio\Twiml;

class TwilioController extends AbstractController
{
    /**
     * @SWG\Tag(name="Twilio")
     * @SWG\Response(
     *    response=200,
     *    description="Twilio outbound")
     * Outbound Action
     * @param  Request $request
     * @return Response
     */
    public function outboundAction(Request $request, $appointmentId)
    {
        // $data = array_merge($request->request->all(), $request->query->all());
        $data = $request->request->all();
        $this->get('logger')->info('TWILIO Outbound action: ' . json_encode($data));
        try {
          $appointment = $this->getDoctrine()->getManager()
              ->getRepository('LevCRMBundle:Appointment')
              ->findOneBy(array('id' => $appointmentId));
          if (!$appointment instanceof Appointment) {
              throw new \Exception("Appointment ID not found", 404);
          }
          $twiml = $this->get('lev_crm.service.voip')->outbound($appointment, $data['From'], $data['To']);
          $response = new Response($twiml, 200);
          $response->headers->set('Content-Type', 'text/xml');

          if (array_key_exists('CallSid', $data)) {
            $this->get('logger')->info('TWILIO Outbound saving CallRecord');
            $callRecord = $this->get('lev_crm.service.voip')->createCallRecord($appointment, $data);
            $historyService = $this->get('lev_crm.service.history');
            $historyService->addHistory($appointment, 5, "Callback Status: {$data['CallStatus']}", true);
          } else {
              throw new \Exception("Twilio data not found", 404);
          }
        } catch (\Exception $e) {
            return $this->renderError($e);
        }
        return $response;
    }

    /**
     * @SWG\Tag(name="Twilio")
     * @SWG\Response(
     *    response=200,
     *    description="Twilio outbiund short action")
     * Outbound Action
     * @param  Request $request
     * @return Response
     */
    public function outboundShortAction(Request $request)
    {
        $data = array_merge($request->request->all(), $request->query->all());
        $appointmentId = $data['appointmentId'];
        return $this->outboundAction($request, $appointmentId);
    }

    /**
     * @SWG\Tag(name="Twilio")
     * @SWG\Response(
     *    response=200,
     *    description="Twilio call")
     * Twilio call action
     *
     * @Get("/twilio/call/{to}/{appointmentId}", name="twilio_call")
     * @param  Request $request
     * @param  string  $to
     * @param  string  $from
     * @return JsonResponse
     */
    public function callAction(Request $request, $to, $appointmentId)
    {
        try {
          $from = $this->getUser()->getPhoneTwilio();
          $call = $this->get('lev_crm.service.voip')->call(
              $from,
              $to,
              $appointmentId
          );
          $data = array('message' => "Call requested from $from to $to");
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Twilio call action
     * @SWG\Tag(name="Twilio")
     * @SWG\Response(
     *    response=200,
     *    description="Twilio test call")
     * @Get("/twilio/testcall/{from}/{to}/{appointmentId}", name="twilio_testcall")
     * @param  Request $request
     * @param  string  $to
     * @param  string  $from
     * @return JsonResponse
     */
    public function testcallAction(Request $request, $from, $to, $appointmentId)
    {
        try {
          $call = $this->get('lev_crm.service.voip')->call(
              $from,
              $to,
              $appointmentId
          );
          $data = array('message' => "Call requested from $from to $to");
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * Twilio token action
     * @SWG\Tag(name="Twilio")
     * @SWG\Response(
     *    response=200,
     *    description="Twilio token")
     * @Get("/twilio/token/{appointmentId}", name="twilio_token")
     * @param  Request $request
     * @return JsonResponse
     */
    public function tokenAction(Request $request, $appointmentId)
    {
        try {
          $voipService = $this->get('lev_crm.service.voip');
          $token = $voipService->token($request->get('username', false));
          $data = array(
              'token'          => $token,
              'Url'            => $voipService->getOutboundUrl() . $appointmentId,
              'StatusCallback' => $voipService->getStatusCallbackUrl() . $appointmentId,
          );
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        return $this->renderJsonResponse($data, Response::HTTP_OK);
    }

    /**
     * @SWG\Tag(name="Twilio")
     * @SWG\Response(
     *    response=200,
     *    description="Twilio call status")
     * Status Callback Action
     * @see https://www.twilio.com/docs/api/rest/making-calls#status-callback-parameter
     * @param  Request $request
     * @return Response
     */
    public function statusCallbackAction(Request $request, $appointmentId)
    {
        $data = array_merge($request->request->all(), $request->query->all());
        $this->get('logger')->info('TWILIO Callback ending: ' . json_encode($data));
        try {
          $appointment = $this->getDoctrine()->getManager()
              ->getRepository('LevCRMBundle:Appointment')
              ->findOneBy(array('id' => $appointmentId));
          $callRecord = $this->get('lev_crm.service.voip')->createCallRecord($appointment, $data);
        } catch (\Exception $e) {
            return $this->renderError($e);
        }

        $response = new Response('', Response::HTTP_OK);
        $response->headers->set('Content-Type', 'text/xml');
        return $response;
    }


    /**
     * @SWG\Tag(name="Twilio")
     * @SWG\Response(
     *    response=200,
     *    description="Twilio voice")
     * Twilio voice action
     *
     * @Post("/twilio/voice", name="twilio_voice")
     */
    public function voiceAction(Request $request)
    {     
      $voipService = $this->get('lev_crm.service.voip');
      $response_data = $voipService->voice($request->get('To'));
      
      $response = new Response($response_data, Response::HTTP_OK);
      $response->headers->set('Content-Type', 'text/xml');
      return $response;
    }


}
