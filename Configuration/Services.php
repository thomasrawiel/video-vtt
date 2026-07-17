<?php
declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TRAW\VideoVtt\EventListener\EnrichFileDataEventListener;
use TRAW\VideoVtt\Utility\FileUtility;

return static function (ContainerConfigurator $configurator, ContainerBuilder $builder): void {
    $services = $configurator->services();
    $services->defaults()
        ->autowire()
        ->autoconfigure()
        ->private();
    $services
        ->load('TRAW\VideoVtt\\', __DIR__ . '/../Classes/*');

    $services->set(FileUtility::class)->public();

    if (class_exists(\FriendsOfTYPO3\Headless\Event\EnrichFileDataEvent::class)) {
        $services->set(EnrichFileDataEventListener::class)
            ->tag('event.listener', [
                'identifier' => 'traw-videovtt/headless-enrichfiledata',
            ]);
    }

};
