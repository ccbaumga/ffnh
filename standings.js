function route(event) {
	let linktext = this.children[0].getAttribute("href");
	window.location.href = linktext;
}
myteams = document.querySelectorAll("li.fteam");
for (let i = 0; i < myteams.length; i++) {
    myteams[i].addEventListener('click', route);
}