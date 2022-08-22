<?php

namespace Console\Module\S3;

use Console\AbstractModule;
use Aws\S3\S3Client;

class S3 extends AbstractModule
{
    private S3Client $s3Client;
    private S3Service $s3Service;

    function __construct() {
        $this->s3Client = S3Client::factory(array(
            //aws sts get-session-token 
            'profile' => 'mfa',
            'region'  => 'ap-southeast-1',
            'version' => 'latest',
        ));

        $this->s3Service = new S3Service;
    }

    public function getClient(): S3Client
    {
        return $this->s3Client;
    }

    public function getService(): S3Service
    {
        return $this->s3Service;
    }
}