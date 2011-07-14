var GetChaturl = "getChatData.php";
var SendChaturl = "sendChatData.php";
var lastID = -1;
window.onload = initJavaScript;

function initJavaScript() {
	document.forms['chatForm'].elements['chatbarText'].setAttribute('autocomplete','off');
	checkStatus('');
	receiveChatText();
}

function receiveChatText() {
	if (httpReceiveChat.readyState == 4 || httpReceiveChat.readyState == 0) {
  	httpReceiveChat.open("GET",GetChaturl + '?lastID=' + lastID + '&rand='+Math.floor(Math.random() * 1000000), true);
    httpReceiveChat.onreadystatechange = handlehHttpReceiveChat; 
  	httpReceiveChat.send(null);
	}
}

function handlehHttpReceiveChat() {
  if (httpReceiveChat.readyState == 4) {
    results = httpReceiveChat.responseText.split('---');
    if (results.length > 2) {
	    for(i=0;i < (results.length-1);i=i+3) {
	    	insertNewContent(results[i+1],results[i+2]);
	    }
	    lastID = results[results.length-4];
    }
    setTimeout('receiveChatText();',4000);
  }
}

function insertNewContent(liName,liText) {
	insertO = document.getElementById("outputList");
	oLi = document.createElement('li');
	oSpan = document.createElement('span');
	oSpan.setAttribute('className','name');
	oSpan.setAttribute('class','name');
	oName = document.createTextNode(liName+': ');
	oText = document.createTextNode(liText);
	oSpan.appendChild(oName);
	oLi.appendChild(oSpan);
	oLi.appendChild(oText);
	insertO.insertBefore(oLi, insertO.firstChild);
}

function sendComment() {
	currentChatText = document.forms['chatForm'].elements['chatbarText'].value;
	if (currentChatText != '' & (httpSendChat.readyState == 4 || httpSendChat.readyState == 0)) {
		currentName = document.forms['chatForm'].elements['name'].value;
		param = 'n='+ currentName+'&c='+ currentChatText;	
		httpSendChat.open("POST", SendChaturl, true);
		httpSendChat.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
  	httpSendChat.onreadystatechange = handlehHttpSendChat;
  	httpSendChat.send(param);
  	document.forms['chatForm'].elements['chatbarText'].value = '';
	} else {
		setTimeout('sendComment();',1000);
	}
}

function handlehHttpSendChat() {
  if (httpSendChat.readyState == 4) {
  	receiveChatText();
  }
}
function checkStatus(focusState) {
	currentChatText = document.forms['chatForm'].elements['chatbarText'];
	oSubmit = document.forms['chatForm'].elements['submit'];
	if (currentChatText.value != '' || focusState == 'active') {
		oSubmit.disabled = false;
	} else {
		oSubmit.disabled = true;
	}
}

function getHTTPObject() {
  var xmlhttp;
  /*@cc_on
  @if (@_jscript_version >= 5)
    try {
      xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch (e) {
      try {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
      } catch (E) {
        xmlhttp = false;
      }
    }
  @else
  xmlhttp = false;
  @end @*/
  if (!xmlhttp && typeof XMLHttpRequest != 'undefined') {
    try {
      xmlhttp = new XMLHttpRequest();
    } catch (e) {
      xmlhttp = false;
    }
  }
  return xmlhttp;
}
var httpReceiveChat = getHTTPObject();
var httpSendChat = getHTTPObject();