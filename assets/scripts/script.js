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
const arrival = document.querySelector('#arrival');
const departure = document.querySelector('#departure');
// const features = getFeaturesInfo(roomId);
// const features = fetch('././app/posts/features.json')
//   .then((res) => res.json())
//   .then((data) => {
//     data = data.filter((d) => d.room_id == roomId);
//     return data;
//   });
const roomCost = getRoomCost(roomId);

// const roomCost = fetch('././app/posts/rooms.json')
//   .then((res) => res.json())
//   .then((data) => {
//     data = data.filter((d) => d.id == roomId);
//     return data[0]['cost_per_day'];
//   });

form.addEventListener('change', () =>
  calculateForm(features, roomCost, departure, arrival)
);
