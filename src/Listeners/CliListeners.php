<?php
declare(strict_types=1);

namespace VitesseCms\Datagroup\Listeners;

use VitesseCms\Cli\ConsoleApplication;
use VitesseCms\Cli\Interfaces\CliListenersInterface;
use VitesseCms\Datagroup\Enums\DatagroupEnum;
use VitesseCms\Datagroup\Repositories\DatagroupRepository;

class CliListeners implements CliListenersInterface
{
    public static function setListeners(ConsoleApplication $di): void
    {
        $di->eventsManager->attach(DatagroupEnum::LISTENER->value, new DatagroupListner(new DatagroupRepository()));
    }
}
