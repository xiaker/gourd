<?php

declare(strict_types=1);

namespace Xiaker\Gourd;

interface ServiceProviderInterface
{
    public function register(Container $gourd): void;
}
