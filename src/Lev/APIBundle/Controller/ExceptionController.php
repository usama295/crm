<?php
/**
 * This file is part off Lev\APIBundle Boilerplate
 *
 * PHP version 5.3
 *
 * @category Controller
 * @package  Lev\APIBundle
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */

namespace App\Lev\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Log\DebugLoggerInterface;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Class AbstractController
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class ExceptionController extends Controller
{

    /**
     * Render Json Response
     *
     * @param array   $data Data to return
     * @param integer $code HTTP Code
     *
     * @return JsonResponse
     */
    public function showAction(Request $request, $exception, DebugLoggerInterface $logger = null)
    {
        $statusCode = $exception->getCode();

        if (!array_key_exists($statusCode, Response::$statusTexts)) {
            $statusCode = 500;
        }

        $content = $this->serializer->serialize(
            array(
                'error'             => 'controller',
                'error_description' => $exception->getMessage()
            ),
            'json'
        );

        return new JsonResponse($content, $statusCode);
    }

}