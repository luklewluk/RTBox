# RTBox

RTBox (Real-Time Box) is a ShoutBox using WebSocket Protocol. The project is quite new and it has only basic functions which can show how more powerful WebSocket is. 
RTBox is based on TinyWS - High performance, flexible, RFC-complaint & well documented WebSockets server library.

### WebSockets? There is lots of good working AJAX shoutboxes!
A lot of websites and forums use simple shoutbox which refreshs every X seconds. To avoid unnecessary connections between browser and server, people designed WebSockets for synchronous connection (which is the best for Chats, ShoutBoxes, etc.).
I wanted to try this new technology and check if it works as good as people say. It does. Try it yourself :)


### Can I implement it into [forum engine]?
Currently there is no possibility to automatic installation on any forum but it should be easy to install it manually. In "web" directory you can find very easy HTML/JS implementation of ShoutBox client. If you know HTML a little bit, you can just replace it with your old (ajax) shoutbox.
And PHP if you need to read nicknames from your forum. 

## Usage
### Requirements
  * PHP >=5.4
  * [TinyWS](https://github.com/kiler129/TinyWs) with dependencies
  * Bootstrap and jQuery (if you want to use my layout)

### How to run?
If you run it not in your local computer, change 'localhost' to your server IP in js/websocket.js file.
After installation enter into 'src' directory and execute 'php RunServer.php' (Unix). I recommend to use 'screen' to keep server working constantly.
