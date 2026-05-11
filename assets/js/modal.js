
let postForm = "";
let forms = document.querySelectorAll(".suppr");
forms.forEach(form => {
	form.addEventListener('click', function (e) {
		postForm = e.target;
		e.preventDefault();
	});
});

let oui = document.getElementById('oui');

oui.addEventListener('click', function (e) {
	console.log(postForm);
	postForm.parentElement.submit();
});
