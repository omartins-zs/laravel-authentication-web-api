<?php

namespace App\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Level;
use Monolog\LogRecord;
use App\Models\SystemLog;
use Throwable;

class DatabaseLogger extends AbstractProcessingHandler
{
    public function __construct(Level $level = Level::Debug, bool $bubble = true)
    {
        parent::__construct($level, $bubble);
    }

    protected function write(LogRecord $record): void
    {
        try {
            SystemLog::create([
                'level'   => $record->level->getName(),
                'message' => $record->message,
                'context' => json_encode($record->context, JSON_UNESCAPED_UNICODE),
            ]);
        } catch (Throwable $e) {
            error_log('Falha ao gravar log no database: ' . $e->getMessage());
        }
    }
}
