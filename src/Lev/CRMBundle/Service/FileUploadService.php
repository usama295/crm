<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 21/09/15
 * Time: 15:12
 */

namespace App\Lev\CRMBundle\Service;
use Doctrine\Bundle\DoctrineBundle\Registry;
use App\Lev\CRMBundle\Entity\Staff;
use App\Lev\CRMBundle\Entity\Customer;
use App\Lev\CRMBundle\Entity\Appointment;
use App\Lev\CRMBundle\Entity\Sale;
use App\Lev\CRMBundle\Entity\Project;
use App\Lev\CRMBundle\Entity\Attachment;
use App\Lev\CRMBundle\Entity\Office;
use App\Lev\APIBundle\Component\HttpFoundation\File\UploadedFile;


class FileUploadService
{
    /**
     * @var Registry
     */
    protected $doctrine;

    /**
     * Constructor
     *
     * @param Registry $doctrine
     */
    public function __construct(Registry $doctrine, $rootDir)
    {
        $this->doctrine   = $doctrine;
        $this->uploadPath = realpath($rootDir . '/../web/upload');
    }

    /**
     * @param object $object
     * @param $messageCode
     * @return HistoryService
     * @throws \Exception
     */
    public function save($object, UploadedFile $file, $flush = true)
    {
      
      
        $attachment = new Attachment();
        $attachment
            ->setFilename($file->getFilename())
            ->setHash($file->getHash())
            ->setSize($file->getSize())
            ->setMimetype($file->getMimeType())
            ->setLocalpath($file->getPath() . $file->getHash())
            ->setOffice( $object->getOffice())
        ;

        if ($object instanceof Staff) {
            $attachment->setStaff($object);
        }
        if ($object instanceof Customer) {
            $attachment->setCustomer($object);
        }
        if ($object instanceof Appointment) {
            $attachment->setAppointment($object);
        }
        if ($object instanceof Sale) {
            $attachment->setSale($object);
        }
        if ($object instanceof Project) {
            $attachment->setProject($object);
        }

        $this->doctrine->getManager()->persist($attachment);
        if ($flush) {
            $this->doctrine->getManager()->flush();
        }

        return $attachment;
    }

    public function createUploadedFile($filename, $content)
    {
        return UploadedFile::create($filename, $this->uploadPath . DIRECTORY_SEPARATOR, $content);
    }

    /**
     * @param $hash
     * @return \Lev\CRMBundle\Entity\Attachment
     */
    public function getAttachmentByHash($hash)
    {
        return $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Attachment')
            ->findOneBy(array('hash' => $hash));
    }

    /**
     * @param $hash
     * @return UploadedFile
     */
    public function getUploadedFileByHash($hash)
    {
        $attachment = $this->getAttachmentByHash($hash);

        return new UploadedFile($attachment->getLocalpath(), $attachment->getHash());
    }

    public function getObject($entity, $id)
    {
        $object = null;

        switch ($entity) {
            case 'staff':
                $object = $this->doctrine->getManager()
                    ->getRepository('LevCRMBundle:Staff')
                    ->findOneBy(array('id' => $id));
                break;

            case 'customer':
                $object = $this->doctrine->getManager()
                    ->getRepository('LevCRMBundle:Customer')
                    ->findOneBy(array('id' => $id));
                break;

            case 'appointment':
                $object = $this->doctrine->getManager()
                    ->getRepository('LevCRMBundle:Appointment')
                    ->findOneBy(array('id' => $id));
                break;

            case 'sale':
                $object = $this->doctrine->getManager()
                    ->getRepository('LevCRMBundle:Sale')
                    ->findOneBy(array('id' => $id));
                break;

            case 'project':
                $object = $this->doctrine->getManager()
                    ->getRepository('LevCRMBundle:Project')
                    ->findOneBy(array('id' => $id));
                break;
        }

        return $object;
    }

    public function delete($ids, $flush = true)
    {
        /** @var \Doctrine\ORM\QueryBuilder $qb */
        $qb = $this->doctrine->getManager()
            ->getRepository('LevCRMBundle:Attachment')
            ->createQueryBuilder('a');
        $results = $qb->where($qb->expr()->in('a.id', $ids))
            ->getQuery()
            ->execute();

        /** @var \Lev\CRMBundle\Entity\Attachment $attachment */
        foreach($results as $attachment) {
            unlink($attachment->getLocalpath());
            $this->doctrine->getManager()->remove($attachment);
        }
        if ($flush) {
            $this->doctrine->getManager()->flush();
        }
    }
}

