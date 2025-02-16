# LongPolling

or neverending streaming...

Javascript class, working as ajax connector to server, allowing to receive "chunks" - blocks of complete information, which could be immediatelly used in the browser application, altough the server response is still not complete and request is opened.
For example, you need to show real progress, while server is looping in some big algorhytmus. Or you want to show realtime data from server, as they arising. An alternative is to use websockets, but sometimes websockets are not the option.

It is assumed also properly prepared server side. Simple examples:
+ [PHP](https://github.com/jasterstary/neverending-streaming/blob/master/example/get.php).
+ [Perl](https://github.com/jasterstary/neverending-streaming/blob/master/example/perl.cgi).
+ [Pascal](https://github.com/jasterstary/neverending-streaming/blob/master/example/pascal.pas).

Each time chunk is received, there is called either javascript event "longpolling-chunk", or, if assigned in options, custom function "onChunk".

## Security:

As during this operation, connection on server remains opened for long time,
it's up to you, to filter users, before allow them to connect to your stream.
Too many unnecessary opened connections could put your server down.
Use advisedly!

## Usage:
``` html
    <!-- insert before the end tag of body element: -->
    <script type="text/javascript" src="neverending-streaming.js" ></script>
    <script type="text/javascript" >
    var ans = new AjaxNeverendingStreaming(
      'get_partial_responses.php',
      { /* options */ }
    );
    </script>

```

## Options:
``` javascript
    var ans = new LongPolling('get.php', {
      method: 'POST',//default is GET
      data: {},

      // These are the default values:
      maxTurns: 1, // how many times request should be repeated, after previous ended.
      // Could be false, in such case request is repeated forever.
      tag: 'chunk', // tag in server response. See the server side example.
      interval: 1000, // interval to parse server response arrived so far.
      useJSON: false, // server sends JSON chunks wrapped to xml tag.
      stopped: false, // if true, class is initially stopped. Could be started with method "start".
      // Custom callbacks:
      onChunk: function(chunk, detail){}, // Default is to trigger JS event "longpolling-chunk"
      onProgress: function(detail){}, // Default is to trigger JS event "longpolling-progress"
      onRequest: function(detail){}, // Default is to trigger JS event "longpolling-request"
      onComplete: function(detail){}, // Default is to trigger JS event "longpolling-complete"
      onError: function(detail){}, // Default is to trigger JS event "longpolling-error"
      onAbort: function(detail){}, // Default is to trigger JS event "longpolling-abort"
      onSuccess: function(detail){}, // Default is to trigger JS event "longpolling-success"
      onAllDone: function(detail){} // Default is to trigger JS event "longpolling-all-done"

    });
```

## Methods:
``` javascript
    ans.stop(); // stops downloading and processing
    ans.resume(); // starts downloading and processing, turns are continuing
    ans.start(); // starts downloading and processing, turns are reset to 0
    ans.options({ // configuring dynamically
        onChunk: function(chunk){ // for example, changing what to do with processed chunk

        }
    });
ans.destroy(); // free instance
```

## Events
or Custom event functions:
Events are described also in options section. Events are JS events, not the jQuery variant. Please note, that when custom event function is assigned, event is not called anymore, as you can do better events!

### On Chunk
> JS event "longpolling-chunk" / onChunk function:
Called each time the chunk is received. There are two variables passed to function: chunk and detail, object with some details about:
+ turn: count of requests called so far,
+ chunks: count of chunks received so far in this request,
+ time: time passed from the beginning of request
+ data: chunk itself

### On Progress
> JS event "longpolling-progress" / onProgress function:
Called in interval, specified in options parameter "interval", if chunks are received or not.
There is only one parameter passed: detail, object with details:
+ turn: count of requests called so far,
+ chunks: count of chunks received so far in this request,
+ received: count of chunks processed through this "progress",
+ time: time passed from the beginning of request

### On Request
> JS event "longpolling-request" / onRequest function:
Called before the request to server is sent.
There is only one parameter passed: detail, object with details:
+ turn: count of requests called so far,
+ url: url of this request

### On Complete
> JS event "longpolling-complete" / onComplete function:
Called when server completed response and all resting chunks was processed.
There is only one parameter passed: detail, object with details:
+ turn: count of requests called so far,
+ status
+ statusText
+ chunks: count of chunks received totally in this request,
+ time: time passed from the beginning of request

### On Error
> JS event "longpolling-error" / onError function:
Called when server completed response and result was error.
There is only one parameter passed: detail, object with details:
+ turn: count of requests called so far,
+ status
+ statusText
+ chunks: count of chunks received totally in this request,
+ time: time passed from the beginning of request

### On Abort
> JS event "longpolling-abort" / onAbort function:
Called when request was aborted by user.
There is only one parameter passed: detail, object with details:
+ turn: count of requests called so far,
+ status
+ statusText
+ chunks: count of chunks received totally in this request,
+ time: time passed from the beginning of request

### On Success
> JS event "longpolling-success" / onSuccess function:
Called when server completed response and result was success with status 200.
There is only one parameter passed: detail, object with details:
+ turn: count of requests called so far,
+ status
+ statusText
+ chunks: count of chunks received totally in this request,
+ time: time passed from the beginning of request

### On All Done
> JS event "longpolling-all-done" / onAllDone function:
Called when "maxTurns" requests was reached and completed.
There is only one parameter passed: detail, object with details:
+ turn: count of requests called so far

## History
