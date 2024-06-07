<?php
/**
 * This file is part off Lev\APIBundle
 *
 * PHP version 5.4
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */

namespace App\Lev\APIBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Pagerfanta\Pagerfanta;
use Doctrine\Common\Inflector\Inflector;
use App\Lev\APIBundle\Config\APIConfig;
use Symfony\Component\Debug\Exception\FlattenException;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * Class AbstractController
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
abstract class AbstractController extends Controller
{

    const ERR_VALIDATE         = 20;
    const ERR_DATABASE         = 30;
    const ERR_FILTER           = 40;
    const ERR_RECORD_NOT_FOUND = 50;
    const ERR_SERVER           = 500;

    static $badRequestCodes = array(
        self::ERR_VALIDATE,
        self::ERR_FILTER,
    );

    static $errorCodes = array(
        self::ERR_VALIDATE,
        self::ERR_FILTER,
        self::ERR_DATABASE,
        self::ERR_RECORD_NOT_FOUND,
        self::ERR_SERVER,
    );

    /**
     * @inheritdoc
     */
    protected function renderError(\Exception $e)
    {
        $e = FlattenException::create($e);

        $data = array(
            'error'             => $e->getCode(),
            'error_description' => $e->getMessage(),
        );

        switch ($e->getCode()) {

            case self::ERR_RECORD_NOT_FOUND:
                $statusCode = Response::HTTP_NOT_FOUND;
                break;

            case self::ERR_VALIDATE:
            case self::ERR_FILTER:
                $statusCode = Response::HTTP_BAD_REQUEST;
                break;

            default:
                $statusCode = in_array(
                                (string) $e->getCode(), Response::$statusTexts
                            )
                            ? $e->getCode()
                            : Response::HTTP_INTERNAL_SERVER_ERROR;
                break;
        }

        if ($this->get('kernel')->getEnvironment() === 'dev') {
            $data['stacktrace'] = $e->getTrace();
        }

        if ($this->get('kernel')->getEnvironment() === 'test') {
            foreach($e->getTrace() as $t){
                $data['stacktrace'][] = "[{$t['class']}] {$t['file']}:{$t['line']}";
            }
        }

        return $this->renderJsonResponse($data, $statusCode);
    }

    /**
     * @inheritdoc
     */
    public function renderValidationErrors(ConstraintViolationListInterface $errors)
    {
        $data = array(
            'error'             => self::ERR_VALIDATE,
            'error_description' => 'Record invalid',
            'validation_errors' => array(),
        );

        $_errors = array();

        foreach ($errors as $error) {
            $_errors[$error->getPropertyPath()][] = $error->getMessage();
        }

        $data['validation_errors'] = $_errors;

        return $this->renderJsonResponse($data, Response::HTTP_BAD_REQUEST);
    }


    /**
     * @inheritdoc
     */
    public function renderJsonResponse($data, $statusCode)
    {
        $serializer = $this->get('serializer');

        $response = new Response(
            $serializer->serialize($data, 'json'),
            $statusCode,
            array('Content-Type' => 'application/json')
        );

        return $response;
    }

    /**
     * @inheritdoc
     * @return \Doctrine\ORM\EntityManager
     */
    public function getManager()
    {
        return $this->getDoctrine()->getManager();
    }

    /**
     * Throws an exception unless one of the staffroles are granted against the current authentication token
     *
     * @param mixed  $staffroles The staffrole
     * @param string $message    The message passed to the exception
     *
     * @throws AccessDeniedException
     */
    protected function denyAccessUnlessHasStaffrole($staffroles, $message = 'Access Denied.')
    {
        if (!is_array($staffroles)) {
            $staffroles = array($staffroles);
        }
        $denied = true;
        foreach ($staffroles as $staffrole) {
          if ($this->getUser()->hasStaffRole($staffrole) || $this->getUser()->hasStaffRole('ADMIN')) {
              $denied = false;
          }
        }
        if ($denied) {
            throw $this->createAccessDeniedException($message);
        }
    }

}
