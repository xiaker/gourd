<?php

namespace Tests\Fixtures;

use Xiaker\Gourd\Container;
use Xiaker\Gourd\ServiceProviderInterface;

class DemoServiceProvider implements ServiceProviderInterface
{
    public function register(Container $gourd)
    {
        $gourd['demo'] = function () {
            return 'FromDemoServiceProvider';
        };
    }
}
