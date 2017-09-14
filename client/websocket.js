(function(){
    const JOINUS_MESSAGE = 'Novo usuário na sala, bem vindo ';
    const LEFTUS_MESSAGE = ' deixou a sala';
    
    var output = document.querySelector('textarea[name="output"]');
    var input = document.querySelector('input[name="input"]');
    var clients = document.querySelector('select[name="clients"]');
    
    var btnStart = document.querySelector('button[name="start_ws"]');
    var conn = null;
        
    var btnSend = document.querySelector('button[name="send"]');
    btnSend.addEventListener('click', function() {
        if (conn) {
            var rid = 0;
            if (clients.selectedIndex !== -1) {
                rid = clients.selectedOptions[0].value;
            }
            conn.send(senderEvent.message(input.value, rid));
            input.value = '';
        }
    }, false);
    
    var senderEvent = {
        message: function(message, rid) {
            return JSON.stringify({
                'message': message
                , 'rid': rid
            });
        }
        
    };
    
    function handleReceiverEvent(event) {
        var eventName = event.event;
        
        switch(eventName) {
            case "MESSAGE":
                output.value += "\n" + new Date().toLocaleString() + ": " + event.data;
                break;
                
            case "USER_JOIN_US":
                output.value += "\n" + new Date().toLocaleString() + ": " + JOINUS_MESSAGE + event.data.nickname;
                addClientToList(event.data);
                break;
                
            case "USER_LEFT_US":
                output.value += "\n" + new Date().toLocaleString() + ": " + event.data.nickname + LEFTUS_MESSAGE;
                removeClientFromListByRid(event.data.rid);
                break;
                
            case "AVAILABLE_USERS":
                event.data.forEach(function(item) {
                    addClientToList(item);
                });
                break;
        }
    }
    
    function addClientToList(client) {
        var newClientOpt = document.createElement('option');
        newClientOpt.value = client.rid;
        newClientOpt.textContent = client.nickname;

        clients.appendChild(newClientOpt);
    }
    
    function removeClientFromListByRid(rid) {
        var client = document.querySelector('select[name="clients"] option[value="' + rid + '"]');
        
        clients.removeChild(client);
    }
    
    function onmessage(e) {
        var data = JSON.parse(e.data);
console.log(data.event);        
        handleReceiverEvent(data);
    }
    
    function onopen(e) {
        console.log(e);
        output.value = 'Connected, enjoy!';
    }
    
    function onclose(e) {
        output.value += "Ooops, the server is down!"
        output.parentNode.className += ' has-error';
        input.disabled = true;
    }
    
    var nickname = window.sessionStorage.getItem('nickname');
    
    conn = new WebSocket('ws://192.168.33.10:8181?nickname=' + nickname);
    conn.onmessage = onmessage;
    conn.onopen = onopen;
    conn.onclose = onclose;
    
    addClientToList({rid: null, nickname: 'Todos'});
}());