Breaking Change
---

1.X to 2.0

Client class has been replaced with Phparia class with renamed methods

```php
    getStasisApplication() => getStasisApplicationName()
    getStasisClient() => getWsClient()
    getStasisLoop() => getEventLoop()
```
