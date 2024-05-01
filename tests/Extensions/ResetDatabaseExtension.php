<?php
declare(strict_types=1);

namespace Tests\Extensions;

use PHPUnit\Runner\Extension\Extension;
use PHPUnit\Runner\Extension\Facade;
use PHPUnit\Runner\Extension\ParameterCollection;
use PHPUnit\TextUI\Configuration\Configuration;
use Tests\Subscribers\ApplicationBootstrapFinishedSubscriber;

class ResetDatabaseExtension implements Extension
{
    public function bootstrap(Configuration $configuration, Facade $facade, ParameterCollection $parameters): void
    {
        $facade->registerSubscriber(new ApplicationBootstrapFinishedSubscriber());
    }
}
