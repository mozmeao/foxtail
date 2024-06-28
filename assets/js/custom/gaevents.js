// Analyze All The Things

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
const twitter = document.querySelector('.ft-js-share-link');

// Newsletter Signups
// (handled in basket-client.js)

if (typeof gtag === 'function') {
  
  //  Global nav
  mainNavLinks.forEach(item => {
    item.addEventListener('click', event => {
      gtag("event", "link_click", {
        label: item.innerText,
        position: 'global nav'
      });
    })
  })

  // Download clicks in the global nav
  navDownload.addEventListener('click', event => {
    gtag('event', 'download_click', {
      position: 'global nav'
    });
  })

  // Search
  searchSubmit.addEventListener('click', event => {
    gtag("event", "search", {
      search_term: searchField.value
    });
  })

  // Mega CTA
  if (megaCta) {
    megaCta.addEventListener('click', event => {
      gtag('event', 'cta_click', {
        label: megaCta.innerText,
        position: "mega"
      });
    });
  }

  // Inline CTA
  inlineCtas.forEach(item => {
    item.addEventListener('click', event => {
      gtag('event', 'cta_click', {
        label: item.querySelector('.ft-c-inline-cta__content h3').innerText,
        position: "inline"
      });
    })
  })

  // Previous article
  if (prev) {
    prev.addEventListener('click', event => {
      gtag('event', 'link_click', {
        label: prev.querySelector('div p').innerText,
        position: "previous"
      });
    })
  }

  // Next article
  if (next) {
    next.addEventListener('click', event => {
      gtag('event', 'link_click', {
        label: next.querySelector('div p').innerText,
        position: "next" 
      });
    })
  }

  // Related Articles
  related.forEach(item => {
    item.addEventListener('click', event => {
      gtag('event', 'link_click', {
        label: item.querySelector('.mzp-c-card-title').innerText,
        position: "related"
      });
    })
  })

  // Social Share
  if (twitter) {
    twitter.addEventListener('click', event => {
      gtag('event', 'social_share', {
        label: 'Twitter'
      });
    })
  }
}  
