<?php
declare(strict_types=1);

namespace Tests\Subscribers;

use PHPUnit\Event\TestRunner\BootstrapFinished;
use PHPUnit\Event\TestRunner\BootstrapFinishedSubscriber;

class ApplicationBootstrapFinishedSubscriber implements BootstrapFinishedSubscriber
{
    public function notify(BootstrapFinished $event): void
    {
        exec('php artisan migrate:fresh --seed');
    }
}
