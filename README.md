phparia
===

Framework for creating Asterisk 12/13 ARI applications.  This is just a wrapper around Asterisk REST Interface.  (https://wiki.asterisk.org/wiki/display/AST/Getting+Started+with+ARI)

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
    $this->client = new \phparia\Client\Client('username', 'password', 'stasis_app_name', '127.0.0.1', '8088');
    $this->client->onStasisStart(function($event) {
        $channel = $event->getChannel();
        $bridge = $this->client->bridges()->createBridge(uniqid(), 'dtmf_events, mixing', 'bridgename');
        $this->client->bridges()->addChannel($bridge->getId(), $channel->getId(), null);

        ...
    });

    $this->client->run();

Documentation
---
You will find wrappers for (https://wiki.asterisk.org/wiki/display/AST/Asterisk+13+ARI) in the Client folder.

You will find wrappers for (https://wiki.asterisk.org/wiki/display/AST/Asterisk+13+REST+Data+Models) in the Resources and Events folders.

You will find examples in the Examples folder.

Extras
---
See phparia IVR for easy voice menu creation (https://github.com/wormling/phpariaIvr).

License
---
Apache 2.0 (http://www.apache.org/licenses/LICENSE-2.0)