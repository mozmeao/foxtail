/**
 * File navigation.js.
 * When the hamburger is clicked, toggle classes
 */

(function () {
	const toggle = document.querySelector('.ft-c-header__toggle');
	const hamburger = document.querySelector('.ft-c-hamburger');
	const menu = document.querySelector('.ft-c-header');
	const handleToggle = () => {
		hamburger.classList.toggle('ft-j-active');
		menu.classList.toggle('ft-j-active');
	}

	toggle.onclick = () => handleToggle();
	toggle.addEventListener('keyup', (event) => {
		if (event.keyCode === 13 || event.keyCode === 32) {
			handleToggle();
		}
	});
}());