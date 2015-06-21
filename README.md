phparia
===

Framework for creating Asterisk 12/13 ARI applications.  This is just a wrapper around Asterisk REST Interface.  (https://wiki.asterisk.org/wiki/display/AST/Getting+Started+with+ARI)

Breaking Change
---

1.X to 2.X

Client class has been replaced with Phparia class with renamed methods


Available via Composer
---
Just add the package "wormling/phparia":

    {
        "require": {
            "wormling/phparia": "dev-master"
        }
    }

Creating a stasis application
---
    $ariAddress = 'ws://localhost:8088/ari/events?api_key=username:password&app=stasis_app_name';

    $logger = new \Zend\Log\Logger();
    $logWriter = new \Zend\Log\Writer\Stream("php://output");
    $logger->addWriter($logWriter);
    //$filter = new \Zend\Log\Filter\SuppressFilter(true);
    $filter = new \Zend\Log\Filter\Priority(\Zend\Log\Logger::NOTICE);
    $logWriter->addFilter($filter);
        
    $this->client = new \phparia\Client\Phparia($logger);
    $this->client->connect($ariAddress);
    $this->client->onStasisStart(function($event) {
        $channel = $event->getChannel();
        $bridge = $this->client->bridges()->createBridge(uniqid(), 'dtmf_events, mixing', 'bridgename');
        $this->client->bridges()->addChannel($bridge->getId(), $channel->getId(), null);

        ...
    });

    $this->client->run();

Creating a stasis application and listening to AMI events
---
    $ariAddress = 'ws://localhost:8088/ari/events?api_key=username:password&app=stasis_app_name';
    $amiAddress = 'username:password@localhost:5038';

    $logger = new \Zend\Log\Logger();
    $logWriter = new \Zend\Log\Writer\Stream("php://output");
    $logger->addWriter($logWriter);
    //$filter = new \Zend\Log\Filter\SuppressFilter(true);
    $filter = new \Zend\Log\Filter\Priority(\Zend\Log\Logger::NOTICE);
    $logWriter->addFilter($filter);
        
    $this->client = new \phparia\Client\Phparia($logger);
    $this->client->connect($ariAddress, $amiAddress);
    $this->client->onStasisStart(function($event) {
        $channel = $event->getChannel();
        $bridge = $this->client->bridges()->createBridge(uniqid(), 'dtmf_events, mixing', 'bridgename');
        $this->client->bridges()->addChannel($bridge->getId(), $channel->getId(), null);

        $this->client->getWsClient()->on('SomeAMIEventName', function($event) {
            ...
        });

        ...
    });

    $this->client->run();

Documentation
---
You will find wrappers for (https://wiki.asterisk.org/wiki/display/AST/Asterisk+13+ARI) in the Client folder.

You will find wrappers for (https://wiki.asterisk.org/wiki/display/AST/Asterisk+13+REST+Data+Models) in the Resources and Events folders.

Examples
---
(https://github.com/wormling/phparia/tree/master/src/wormling/phparia/Examples)

License
---
Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)