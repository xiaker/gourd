<?php

declare(strict_types=1);

namespace Xiaker\Gourd\Exception;

use Exception;
use Psr\Container\NotFoundExceptionInterface;

class NotFoundException extends Exception implements NotFoundExceptionInterface {}
