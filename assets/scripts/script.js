const imgSection = document.querySelector('section.images');
const pageHref = window.location.href;
// const websiteHostName = 'http://bosse.ai/neversummer/';
const websiteHostName = 'http://localhost:4000/';

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
