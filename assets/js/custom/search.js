/**
 * File search.js.
 * When the search icon is clicked, toggle search
 */
(function () {
  const searchToggle = document.querySelectorAll('.ft-c-header__search-icon');
  const searchArea = document.querySelector('.ft-c-search');

  const handleToggle = () => {
    searchArea.classList.toggle('ft-j-active');
  }

  searchToggle.forEach(el => {
    el.onclick = () => handleToggle();
    el.addEventListener('keyup', (event) => {
      if (event.keyCode === 13 || event.keyCode === 32) {
        handleToggle();
      }
    });
  })


}());