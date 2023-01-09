const imgSection = document.querySelector('section.images');
const pageHref = window.location.href;
const websiteHostName = 'https://bosse.ai/neversummer/';
// const websiteHostName = 'http://localhost:4000/';

let roomId = 1;
if (pageHref == websiteHostName + 'standard.php') {
  roomId = 2;
} else if (pageHref == websiteHostName + 'luxury.php') {
  roomId = 3;
}

getImages(roomId);
getImages(roomId);

const navItems = document.querySelectorAll('li.navbar-item');
const navLinks = document.querySelectorAll('a.nav-link');

navItems.forEach((item) => {
  const link = item.querySelector('a');
  if (pageHref == link.href) {
    item.classList.add('active');
  } else {
    item.classList.remove('active');
  }
});
//if on index but index.php isnt showing in the url
const navIndex = document.querySelector('.navbar ul:first-child li');
if (pageHref == websiteHostName) {
  navIndex.classList.add('active');
}

// Live cost calculator
const form = document.querySelector('form');

form.addEventListener('onchange', calculateForm());
