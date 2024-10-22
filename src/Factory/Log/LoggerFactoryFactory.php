<?php

declare(strict_types=1);

namespace App\Factory\Log;

use WebServCo\Log\Contract\LoggerFactoryInterface;
use WebServCo\Log\Factory\ContextFileLoggerFactory;

use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

final class LoggerFactoryFactory
{
    public function createLoggerFactory(string $projectPath): LoggerFactoryInterface
    {
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        return new ContextFileLoggerFactory(sprintf('%svar/log', $projectPath));
    }
}
