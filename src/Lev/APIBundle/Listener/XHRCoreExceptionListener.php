<?php

namespace App\Lev\APIBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use JMS\Serializer\SerializerInterface;

/**
 * Class XHRCoreExceptionListener
 * @see https://gist.github.com/xanf/1015146
 * @package Lev\APIBundle\Listener
 */
class XHRCoreExceptionListener
{

    private $serializer;

    /**
     * Constructor
     *
     * @author Joe Sexton <joe@webtipblog.com>
     * @param  SerializerInterface $serializer
     */
    public function __construct( SerializerInterface $serializer )
    {
        $this->serializer = $serializer;
    }

    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request   = $event->getRequest();
        if (! $request->isXmlHttpRequest()) {
//            return;
        }
        $statusCode = $exception->getCode();

        if (!array_key_exists($statusCode, Response::$statusTexts)) {
            $statusCode = 500;
        }

        $content = $this->serializer->serialize(
            array(
                'error'             => 'general',
                'error_description' => $exception->getMessage()
            ),
            'json'
        );

        $response = new Response(
            $content,
            $statusCode,
            array('Content-Type' => 'application/json')
        );

        $event->setResponse($response);
    }
}