/**
 * File search.js.
 * When the search icon is clicked, toggle search
 */
(function () {
  const searchToggle = document.querySelectorAll('.ft-c-header__search-icon');
  const searchArea = document.querySelector('.ft-c-search');
  const header = document.querySelector('.ft-c-header');
  const hamburger = document.querySelector('.ft-c-hamburger');

  const handleToggle = () => {
    searchArea.classList.toggle('ft-j-active');
    if (searchArea.classList.contains('ft-j-active')) {
      header.classList.remove('ft-j-active');
      hamburger.classList.remove('ft-j-active');
    }
  }

  // a little bit extra to make sure it works in IE11
  Array.prototype.slice.call(searchToggle).forEach(el => {
    el.onclick = () => handleToggle();
    el.addEventListener('keyup', (event) => {
      if (event.keyCode === 13 || event.keyCode === 32) {
        handleToggle();
      }
    });
  })


}());