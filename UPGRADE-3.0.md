Breaking Changes
---

2.X to 3.0  

$phparia->bridges()->removeChannel(...) will now throw phparia\Exception\InvalidParameterException instead of phparia\Exception\NotFoundException if the channel does not exist.
  
$phparia->bridges()->addChannel(...) will now throw phparia\Exception\InvalidParameterException instead of phparia\Exception\NotFoundException if the channel does not exist.
  
$phparia->bridges()->getChannels() has been renamed to $phparia->bridges()->getChannelIds()
