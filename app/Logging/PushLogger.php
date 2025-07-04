<?php

namespace App\Logging;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class PushLogger
{
    protected $logger;

    public function __construct()
    {
        $this->logger = new Logger('context');
    }

    public function log(string $message, int $messageId, array $context = [], string $level = 'debug'): void
    {
        $path = storage_path('logs/message-' . $messageId . '.log');
        $handler = new StreamHandler($path);
        $this->logger->setHandlers([$handler]);

        $this->logger->log($level, $message, $context);
    }

}