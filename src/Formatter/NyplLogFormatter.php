<?php
namespace NYPL\Starter\Formatter;

use Monolog\Formatter\JsonFormatter;

class NyplLogFormatter extends JsonFormatter
{
    protected function translateLevel($level = 0)
    {
        switch ($level) {
            case 100:
                return 'DEBUG';
            case 200:
                return 'INFO';
            case 250:
                return 'NOTICE';
            case 300:
                return 'WARNING';
            case 400:
                return 'ERROR';
            case 500:
                return 'CRITICAL';
            case 550:
                return 'ALERT';
            case 600:
                return 'EMERGENCY';
        }
    }

    public function format(array $record)
    {
        $record['level'] = $this->translateLevel($record['level']);
        
        $record['datetime'] = date('c');

        unset($record['level_name']);
        unset($record['channel']);

        if (!$record['extra']) unset($record['extra']);
        if (!$record['context']) unset($record['context']);

        $record = parent::format($record);

        return $record;
    }
}
