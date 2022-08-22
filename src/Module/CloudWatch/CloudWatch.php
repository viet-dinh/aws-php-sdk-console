<?php

namespace Console\Module\CloudWatch;

use Console\AbstractModule;
use Aws\CloudWatch\CloudWatchClient;

class CloudWatch extends AbstractModule
{
    private CloudWatchClient $cloutWatchClient;
    private CloudWatchService $cloudWatchService;

    function __construct() {
        $this->cloutWatchClient = CloudWatchClient::factory(array(
            //aws sts get-session-token 
            'profile' => 'mfa',
            'region'  => 'ap-southeast-1',
            'version' => 'latest',
        ));
        $this->cloudWatchService = new CloudWatchService;
    }

    public function getClient(): CloudWatchClient
    {
        return $this->cloutWatchClient;
    }

    public function getService(): CloudWatchService
    {
        return $this->cloudWatchService;
    }
}