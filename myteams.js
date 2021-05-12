function route(event) {
	let linktext = this.children[1].getAttribute("href");
	console.log(linktext);
	window.location.href = linktext;
}
console.log(0);
myteams = document.querySelectorAll("li.ateam");
for (let i = 0; i < myteams.length; i++) {
	console.log(1);
    myteams[i].addEventListener('click', route);
		console.log(1);
}

