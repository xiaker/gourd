<?php

declare(strict_types=1);

namespace Xiaker\Gourd\Exception;

use Exception;
use Psr\Container\ContainerExceptionInterface;

class ContainerException extends Exception implements ContainerExceptionInterface {}
