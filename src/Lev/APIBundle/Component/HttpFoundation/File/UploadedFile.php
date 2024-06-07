<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 21/09/15
 * Time: 15:11
 */

namespace App\Lev\APIBundle\Component\HttpFoundation\File;

use Symfony\Component\HttpFoundation\File\File;

class UploadedFile extends File
{
    /**
     * @var string
     */
    protected $filename;

    /**
     * @var string
     */
    protected $hash;

    public function __construct($path, $hash)
    {
        $this->path = $path;
        $this->hash = $hash;
        parent::__construct($path, false);
    }

    public static function create($filename, $uploadDir, $content)
    {
        $now  = new \DateTime();
        $hash = md5($now->format('YMDHis') . $filename);
        $path = $uploadDir . DIRECTORY_SEPARATOR . $hash;
        file_put_contents($path, $content);

        $uploadFile = new UploadedFile($path, $hash);
        $uploadFile
            ->setHash($hash)
            ->setFilename($filename);

        return $uploadFile;
    }

    /**
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;
        return $this;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
        return $this;
    }


}