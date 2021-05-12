// Get page elements needed for JS code
const gifsDiv = document.getElementById("gifs");
const chatWindow = document.getElementById("chat-window");
const textInput = document.getElementById("input");
const chatForm = document.getElementById("chat-form");

// When a user presses enter while typing in the text area,
// send the text that was typed
textInput.addEventListener("keydown", function(event) {
  if(event.key === "Enter") {
    sendTextMessage();
  }
});

// When a user presses the send button, 
// send the text that was typed
chatForm.addEventListener("submit", function(event) {
  event.preventDefault();
  sendTextMessage();
});

// Global variable to keep track of the last id of the user's chat window
let lastId = -1;

// When a user first loads the page, prompt them for their user name
//const curUser = prompt("Who would you like to chat as today?");

// Have the user "join the chat"
joinChat();

// Set an interval to check for new chats every second
let checkingNew = setInterval(checkForNew, 1000);

/**
 * TODO:
 * Call getChats with the endpoint to get all chats
 */
function joinChat() {
	getChats(-1);
  // TODO
}

/**
 * TODO: 
 * Call getChats with the endpoint to get chats since the last 
 * chat the user has in their chat window
 */
function checkForNew() {
  // TODO
  // Hint: Use the lastId global variable
	getChats(lastId);
}

/**
 * TODO:
 * Contact your chat.php API to get chats using the given endpoint
 * Update the lastId global variable based on the results
 * Load any new chats in the chat window by calling loadNewChats()
 * @param {String} endpoint endpoint to hit for getting chats
 */
function getChats(endpoint) {
  // TODO
  // Hint: Make use of loadNewChats() method, implemented for you below
	let append = "";
	if (endpoint > 0) {
		append = "?last_id=" + endpoint + "&";
	} else {
		append = "?";
	}
	let append2 = "leagueid=" + leagueid;
	let ajax = new XMLHttpRequest();
	ajax.addEventListener("load", textloaded);
	ajax.open("GET", "chat.php" + append + append2);
	ajax.send();	
}

function textloaded(){
	let obj = JSON.parse(this.response);
	if (obj.status == "ok") {
		loadNewChats(obj.chats);
		if (obj.chats.length > 0) {
			lastId = obj.chats[obj.chats.length - 1]['id'];
			//grabs the id of the last chat received
		}
	}
}

/**
 * Adds the given chats to the chat window
 * 
 * @param {Array} chats chat array where each element 
 * is a chat object with id, user and message keys
 */
function loadNewChats(chats) {
  for(let chat of chats) {
    // Create user span
    let userPart = document.createElement("span");
    userPart.innerHTML = `${chat.user}:`;
    userPart.className = "user";

    // Create message span
    let msgPart = document.createElement("span");
    msgPart.innerHTML = chat.message;
    msgPart.className = "message";

    // Add the spans to the chat window
    chatWindow.appendChild(userPart);
    chatWindow.appendChild(msgPart);

    // Automatically scroll down for the user
    chatWindow.scrollTop = chatWindow.scrollHeight;
  }
}

/**
 * When sending a gif, send the HTML code to render it
 * (so "<img src=...") given by the img elements outerHTML
 * This function should be bound to gif img clicks
 */
function sendGif() {
  sendMessage(this.outerHTML);
}

/**
 * When sending text, send what's in the text box
 */
function sendTextMessage() {
  sendMessage(textInput.value);
	textInput.value = "";
}

/**
 * TODO:
 * POST the given message to the chat API 
 * Hint: Use JSON data, form data may be too restrictive...
 * If successful, clear out the text input: textInput.value = "";
 * @param {String} msg Message to send to the chat API
 */
function sendMessage(msg) {
  // TODO
	let xhr = new XMLHttpRequest();
	xhr.addEventListener("load", responseReceivedHandler);
	xhr.open("POST", "chat.php");
	let user = curUser;
	let message = msg;
	obj = { "user": user , "message" : message , "leagueid" : leagueid};
	xhr.setRequestHeader("Content-Type", "application/json");
	xhr.send(JSON.stringify(obj));
}

function responseReceivedHandler() {
	//let obj = JSON.parse(this.response);
	textInput.value = "";
}


/**
 * TODO:
 * When a user asks for gifs by clicking Get Gifs button, 
 * search for gifs using GIPHY API with the text in the 
 * textInput textbox as the search query for gifs
 * 
 * When gifs are returned,
 * clear out the current gifs,
 * create img elements for each gif result (details in lab writeup)
 * add a listener to each img to be able to send it in the chat
 * and add the img to the gif div
 */
/*document.getElementById("gif-btn").addEventListener("click", function() {
  let searchQuery = textInput.value;
  // TODO
});*/