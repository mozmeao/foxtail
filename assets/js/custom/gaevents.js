// Analyze All The Things
if (typeof ga === 'function') {
  const searchOpen = document.querySelector('.ft-c-header__search-icon');
  const searchField = document.querySelector('.search-field');
  const searchSubmit = document.querySelector('.search-submit');
  const prev = document.querySelector('#previous-post');
  const next = document.querySelector('#next-post');
  const mainNavLinks = document.querySelectorAll('#nav a');
  const blogname = document.querySelector('body').getAttribute('data-blogname'); 
  const related = document.querySelectorAll('#related-articles a');
  const navDownload = document.querySelector('#nav-download');
  const megaCta = document.querySelector('.ft-c-mega-cta__content a')
  const pageTitle = document.title;
  const inlineCtas = document.querySelectorAll('.ft-c-inline-cta')

  // Track clicks on the main top nav

  mainNavLinks.forEach(item => {
    item.addEventListener('click', event => {
      ga('send', {
        hitType: 'event',
        eventAction: 'nav click',
        eventCategory: blogname + ' Interactions',
        eventLabel: 'Global nav: ' + item.innerHTML
      });  
    })
  })



  // Click on search icon
  // (handled in search.js)

  // Newsletter Signups
  // (handled in basket-client.js)


  // Search
  searchSubmit.addEventListener('click', event => {
    ga('send', {
      hitType: 'event',
      eventAction: 'search',
      eventCategory: blogname + ' Interactions',
      eventLabel: 'Search: ' + searchField.value,
    }); 
  })

  // Mega CTA
  if (megaCta) {
    megaCta.addEventListener('click', event => {
      ga('send', {
        hitType: 'event',
        eventAction: 'Mega CTA Click',
        eventCategory: blogname + ' Interactions',
        eventLabel: 'Page: ' + pageTitle,
      }); 
    });
  }

  // Inline CTA
  inlineCtas.forEach(item => {
    item.addEventListener('click', event => {
      ga('send', {
        hitType: 'event',
        eventAction: 'Inline CTA Click',
        eventCategory: blogname + ' Interactions',
        eventLabel: 'CTA Text: ' + item.querySelector('.ft-c-inline-cta__content h3').innerHTML,
      }); 
    })
  })

  // Previous article
  if (prev) {
    prev.addEventListener('click', event => {
      ga('send', {
        hitType: 'event',
        eventAction: 'adjacent click',
        eventCategory: blogname + ' Interactions',
        eventLabel: 'Previous: ' + prev.querySelector('div p').innerHTML,
      }); 
    })
  }


  // Next article
  if (next) {
    next.addEventListener('click', event => {
      ga('send', {
        hitType: 'event',
        eventAction: 'adjacent click',
        eventCategory: blogname + ' Interactions',
        eventLabel: 'Next: ' + next.querySelector('div p').innerHTML,
      });
    })
  }

  // Related Articles
  related.forEach(item => {
    item.addEventListener('click', event => {
      ga('send', {
        hitType: 'event',
        eventAction: 'related click',
        eventCategory: blogname + ' Interactions',
        eventLabel: 'Related: ' + item.querySelector('.ft-c-card__title').innerHTML,
      });
    })
  })

  // Count download clicks in the navbar
  navDownload.addEventListener('click', event => {
    ga('send', {
      hitType: 'event',
      eventAction: 'Firefox Download',
      eventCategory: blogname + ' Interactions',
      eventLabel: 'Firefox for Desktop',
      dimension15: 'nav cta',
      dimension1: blogname
  });

  })
}