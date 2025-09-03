<?php

namespace App\Logging;

use Illuminate\Log\Logger as IlluminateLogger;
use Monolog\Formatter\JsonFormatter;
use Monolog\Logger;

class CustomizeFormatter
{
    public function __invoke($logger)
    {
        if ($logger instanceof IlluminateLogger) {
            $logger = $logger->getLogger();
        }

        if ($logger instanceof Logger) {
            foreach ($logger->getHandlers() as $handler) {
                $handler->setFormatter(new JsonFormatter());
            }
        }

        return $logger;
    }
}
