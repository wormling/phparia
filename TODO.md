Buy Skype number +1 818 456 0270

Link events to channels/channel
===
For example: onDtmlRecieved() where channels catches all and channel 
catches for the instance.  See [node-ari-client client.js](https://github.com/asterisk/node-ari-client/blob/master/lib/client.js) and 
[bridge-dial example](https://github.com/asterisk/ari-examples/blob/master/bridge-dial/example.py)

*note* Since many events send the channel back with the same id, the destructor will remove the listener even if it's an event such as VarSet.
    Even passing a variable will probably not work since it can still remove the listener.  So only remove the listener if it was created in this instance. (done)

Merge correct functionality in to both channels and channel
===
For example: channels->originate(), channel->originate() (createChannel)

See [channel.originate after registering events example](https://www.npmjs.org/package/ari-client)

To do this, all first class objects must have access to the client instance.  See [node-ari-client resources.js](https://github.com/asterisk/node-ari-client/blob/master/lib/resources.js)
@TODO  ChannelEnteredBridge, ChannelLeftBridge would be useful in the bridge class but uses channel id... figure it out
  'Application',
    Events: **ApplicationReplaced**

  'Channel',
    Events: **StasisEnd**, **StasisStart**, **ChannelCallerId**, **ChannelCreated**, **ChannelDestroyed**, **ChannelDtmfReceived**, **ChannelEnteredBridge**, 
**ChannelHangupRequest**, **ChannelLeftBridge**, **ChannelStateChange**, **ChannelTalkingFinished**, **ChannelTalkingStarted**, 
**ChannelUserevent**, **ChannelVarset**

  'Bridge',
    Events: BridgeAttendedTransfer, BridgeBlindTransfer, **BridgeCreated**, **BridgeDestroyed**, **BridgeMerged**

  'DeviceState',
    Events: **DeviceStateChange**

  'Endpoint',
    Events: **EndpointStateChange**

  'LiveRecording',
    Events: **RecordingFailed**, **RecordingFinished**, **RecordingStarted**

  'Playback',
    Events: **PlaybackFinished**, **PlaybackStarted**

For attaching events, the object must either already be created and have an ID, OR one must be 
generated from the constructor.  This ID will be passed to ARI calls to allow the events to be emitted to
the subscribing instance.

Sound chain (done)
===
Create an optionally interruptible sound chain that acts as one sound.  It should also optionally accept 
the interrupt digit as input.   This is very useful for things like sayDatetime().  It should also support 
Promise/A.

Rename APIs (done)
===
For example: ChannelsApi becomes Channels

The client access will also change from $client->getChannelsApi()->method() to $client->channels()->method()

Move/copy API calls to resources
===
For example: playback resource should have a stopPlayback method with the playback id already in the call.
**bridge**, **channel**, endpoint, mailbox, playback, storedRecording, liveRecording