// Analyze All The Things
if (typeof gtag === 'function') {
  const searchOpen = document.querySelector('ft-c-header__search-icon');
  const searchField = document.querySelector('.search-field');
  const searchSubmit = document.querySelector('.search-submit');
  const prev = document.querySelector('#previous-post');
  const next = document.querySelector('#next-post');
  const mainNavLinks = document.querySelectorAll('#nav a');
  const blogname = document.querySelector('body').getAttribute('data-blogname'); 
  const related = document.querySelectorAll('#related-articles a');
  const navDownload = document.querySelector('#nav-download');

  // Track clicks on the main top nav

  mainNavLinks.forEach(item => {
    item.addEventListener('click', event => {
      gtag('event', 'nav click', {
        'event_category': blogname + ' Interactions',
        'event_label': 'Global nav: ' + item.innerHTML
      });

    })
  })


  // Click on search icon
  // (handled in search.js)

  // Newsletter Signups
  // (handled in basket-client.js)


  // Search
  searchSubmit.addEventListener('click', event => {
    gtag('event', 'search', {
      'event_category': blogname + ' Interactions',
      'event_label': 'Search: ' + searchField.value,
    })
  })


  // Previous article
  if (prev) {
    prev.addEventListener('click', event => {
      gtag('event', 'adjacent click', {
        'event_category': blogname + ' Interactions',
        'event_label': 'Previous: ' + prev.querySelector('div p').innerHTML,
      })
    })
  }


  // Next article
  if (next) {
    next.addEventListener('click', event => {
      gtag('event', 'adjacent click', {
        'event_category': blogname + ' Interactions',
        'event_label': 'Next: ' + next.querySelector('div p').innerHTML,
      })
    })
  }

  // Related Articles
  related.forEach(item => {
    item.addEventListener('click', event => {
      gtag('event', 'related click', {
        'event_category': blogname + ' Interactions',
        'event_label': 'Related: ' + item.querySelector('.ft-c-card__title').innerHTML,
      })

    })
  })

  // Count download clicks in the navbar
  navDownload.addEventListener('click', event => {
    gtag('event', 'Firefox Download', {
      'event_category': blogname + ' Interactions',
      'event_label': 'Firefox for Desktop',
      'dimension15': 'nav cta',
      'dimension1': blogname,
    });

  })
}