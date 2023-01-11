const imgSection = document.querySelector('section.images');
const pageHref = window.location.href;
const websiteHostName = 'https://bosse.ai/neversummer/';
// const websiteHostName = 'http://localhost:4000/';

if (
  pageHref === websiteHostName + 'index.php' ||
  pageHref === websiteHostName
) {
  var roomId = 1;
  getImages(roomId);
  getImages(roomId);
} else if (pageHref === websiteHostName + 'standard.php') {
  var roomId = 2;
  getImages(roomId);
  getImages(roomId);
} else if (pageHref === websiteHostName + 'luxury.php') {
  var roomId = 3;
  getImages(roomId);
  getImages(roomId);
}

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
if (typeof roomId !== 'undefined') {
  const form = document.querySelector('form:not(.admin)');
  const arrival = document.querySelector('#arrival');
  const departure = document.querySelector('#departure');
  const roomCost = getRoomCost(roomId);

  form.addEventListener('change', () =>
    calculateForm(features, roomCost, departure, arrival)
  );
}
