/**
 * Created by luklew on 16.03.15.
 */

start('ws://localhost:8080');

jQuery.fn.extend({
    disable: function(state) {
        return this.each(function() {
            this.disabled = state;
        });
    }
});

var count_messages = 0;

function start(websocketServerLocation){
    ws = new WebSocket(websocketServerLocation);
    ws.onopen = function(e){
        jQuery("#shoutbox_data").html('');
    };


    ws.onmessage = function(e) {
        if (e.data == 'nonickname') {
            var userNick = prompt("Enter your nickname:");
            jQuery("#shout_data").val('Please login');
            if (userNick !== 'null' && userNick !== null && userNick !== '') {
                ws.send('/NICK' + userNick);
                $("#shout_data").disable(false);
            }
        }
        else if(e.data == 'loggedin'){
            jQuery("#shouting-status").val('Send');
            jQuery("#shout_data").val('');
        }
        else if(e.data == 'loginerr'){
            var userNick = prompt("Enter your nickname:");
            ws.send('/NICK' + userNick);
        }
        else {
            var messages = jQuery("#shoutbox_data").html();
            obj = JSON.parse(e.data);
            for(var key in emoticons) {
                obj['text'] = obj['text'].split(key).join(emoticons[key]);
            }

            var html_message = '<p style="line-height: 32px;"><b>' + obj['nick'] + '</b> - <small>' + obj['date'] + '</small> -- ' + obj['text'] + '</p>';

            if (count_messages > 20) {
                jQuery("#shoutbox_data p:last").remove();
            }
            else {
                count_messages++;
            }

            jQuery("#shoutbox_data").prepend(html_message);
        }
    };
    ws.onclose = function(){
        //try to reconnect in 5 seconds
        setTimeout(function(){start(websocketServerLocation)}, 5000);
    };
}


var ShoutBox = {
    postShout: function () {
        message = jQuery("#shout_data").val();
        ws.send(message);
        jQuery("#shout_data").val('');
    }
};