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

namespace App\Lev\CRMBundle\Controller\API;

use App\Lev\APIBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use App\Lev\CRMBundle\Entity\Attachment;
use Swagger\Annotations as SWG;

/**
 * Class AbstractController
 *
 * @category Controller
 * @package  Lev\APIBundle\Controller
 * @author   Rafael Goulart <rafaelgou@gmail.com>
 * @license  Private <http://none.de>
 * @link     none
 */
class FileUploadController extends AbstractController
{
    /**
     * @SWG\Tag(name="File")
     * @SWG\Response(
     *     response=200,
     *     description="Successfully updated file")
     * @Post("/files/upload/{entity}/{entityId}/{filename}", name="file_upload")
     */
    public function upload(Request $request, $entity, $entityId, $filename)
    {

       


        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
            , null
            , 'You don\'t have access to this page'
        );

        /** @var \App\Lev\CRMBundle\Service\FileUploadService $fileUploadService */
        $fileUploadService = $this->get('lev_crm.service.fileupload');
        $object            = $fileUploadService->getObject($entity, $entityId);
        $fileContent       = $request->getContent();
        $uploadedFile      = $fileUploadService->createUploadedFile($filename, $fileContent);
        /** @var \App\Lev\CRMBundle\Entity\Attachment $attachment */
        $attachment = $fileUploadService->save($object, $uploadedFile);

        return $this->renderJsonResponse($attachment->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @SWG\Tag(name="File")
     * @SWG\Response(
     *     response=200,
     *     description="Get uploaded files")
     * @Get("/files/{hash}/{filename}", name="file_get")
     */
    public function getFile(Request $request, $hash, $filename)
    {
        $this->denyAccessUnlessGranted(
            'IS_AUTHENTICATED_FULLY'
            , null
            , 'You don\'t have access to this page'
        );

        /** @var \Lev\CRMBundle\Service\FileUploadService $fileUploadService */
        $fileUploadService = $this->get('lev_crm.service.fileupload');

        /** @var \Lev\CRMBundle\Entity\Attachment $attachment */
        $attachment      = $fileUploadService->getAttachmentByHash($hash);

        $content = file_get_contents($attachment->getLocalpath());
        $response = new Response();
        $response->headers->set('Content-type', $attachment->getMimetype());
        $response->headers->set('Content-Disposition', sprintf('attachment; filename="%s"', $attachment->getFilename()));
        $response->headers->set('Content-Length', $attachment->getSize());
        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($content);
        $response->sendHeaders();
        ob_clean();
        flush();

        return $response;
    }

}
