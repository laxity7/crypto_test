<?php

declare(strict_types=1);

namespace App\Scheduler;

use Symfony\Component\Console\Messenger\RunCommandMessage;
use Symfony\Component\Scheduler\Attribute\AsSchedule;
use Symfony\Component\Scheduler\RecurringMessage;
use Symfony\Component\Scheduler\Schedule;
use Symfony\Component\Scheduler\ScheduleProviderInterface;
use Symfony\Contracts\Cache\CacheInterface;

#[AsSchedule]
class RateUpdateScheduler implements ScheduleProviderInterface
{
    /**
     * @param array<int, array{provider: string, frequency: string}> $schedulesConfig
     */
    public function __construct(
        private readonly array $schedulesConfig,
        private readonly CacheInterface $cache
    ) {
    }

    public function getSchedule(): Schedule
    {
        $schedule = new Schedule()->stateful($this->cache)->processOnlyLastMissedRun(true);

        foreach ($this->schedulesConfig as $config) {
            $commandString = sprintf('app:fetch-rates --provider=%s', $config['provider']);
            $message = new RunCommandMessage($commandString);

            $schedule->add(RecurringMessage::every($config['frequency'], $message));
        }

        return $schedule;
    }
}
