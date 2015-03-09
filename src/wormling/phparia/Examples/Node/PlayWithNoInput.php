<?php

// Make sure composer dependencies have been installed
require __DIR__ . '/../../../../../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('xdebug.var_display_max_depth', 4);
$configFile = __DIR__ . '/../config.yml';
$yaml = new Symfony\Component\Yaml\Parser();
$value = $yaml->parse(file_get_contents($configFile));

$userName = $value['client']['userName'];
$password = $value['client']['password'];
$applicationName = $value['client']['applicationName'];
$host = $value['client']['host'];
$port = $value['client']['port'];

$channel = null;
$client = new \phparia\Client\Client($userName, $password, $applicationName, $host, $port);
$client->getStasisClient()->on(\phparia\Events\Event::STASIS_START, function($event) use ($client, &$channel) {
    if (count($event->getArgs()) > 0 && $event->getArgs()[0] === 'dialed') {
        return; // Not an incoming call
    }

    $logger = $client->getLogger();

    // Toss the call in a bridge
    $bridge = $client->bridges()->createBridge('occ_bridge_' . uniqid(), 'mixing, dtmf_events, proxy_media', 'bridgy');
    $client->bridges()->addChannel($bridge->getId(), $event->getChannel()->getId(), null);

    $nodeController = new \phparia\Node\NodeController($client, $event->getChannel(), $bridge);

    $nodeController->register('mainMenu')
            ->saySound('beep')
            ->saySound('beep')
    ;

    $nodeController->registerResult('mainMenu')
            ->onComplete()
            ->jumpAfterEval(function(\phparia\Node\Node $node) use ($nodeController) {
                return getSecondMenu($nodeController);
            })
    ;



    $nodeController->jumpTo('mainMenu');
});

function getSecondMenu(\phparia\Node\NodeController $nodeController)
{
    $nodeController->register('secondMenu')
            ->sayNumber(27)
    ;

    return 'secondMenu';
}

$client->run();

