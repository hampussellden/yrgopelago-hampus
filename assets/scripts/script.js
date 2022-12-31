const imgSection = document.querySelector('section.images');
const pageHref = window.location.href;
// const websiteHostName = 'http://bosse.ai/yrgopelago-hampus/';
const websiteHostName = 'http://localhost:4000/';

const images = {
  budget: [
    'assets/images/budget/budget-1.jpeg',
    'assets/images/budget/budget-2.jpeg',
    'assets/images/budget/budget-3.jpeg',
    'assets/images/budget/budget-4.png',
    'assets/images/budget/budget-5.jpeg',
    'assets/images/budget/budget-6.jpeg',
    'assets/images/budget/budget-7.jpeg',
    'assets/images/budget/budget-8.jpeg',
    'assets/images/budget/budget-9.jpeg',
  ],
  standard: [
    'assets/images/standard/standard-1.jpeg',
    'assets/images/standard/standard-2.jpeg',
    'assets/images/standard/standard-3.jpeg',
    'assets/images/standard/standard-4.jpeg',
    'assets/images/standard/standard-5.jpeg',
    'assets/images/standard/standard-6.jpeg',
    'assets/images/standard/standard-7.jpeg',
    'assets/images/standard/standard-8.jpeg',
    'assets/images/standard/standard-9.jpeg',
  ],
  luxury: [
    'assets/images/luxury/luxury-1.jpeg',
    'assets/images/luxury/luxury-2.jpeg',
    'assets/images/luxury/luxury-3.jpeg',
    'assets/images/luxury/luxury-4.jpeg',
    'assets/images/luxury/luxury-5.jpeg',
    'assets/images/luxury/luxury-6.png',
    'assets/images/luxury/luxury-7.jpeg',
    'assets/images/luxury/luxury-8.jpeg',
    'assets/images/luxury/luxury-9.jpeg',
  ],
};
let roomId = 1;
if (pageHref == websiteHostName + 'standard.php') {
  roomId = 2;
} else if (pageHref == websiteHostName + 'luxury.php') {
  roomId = 3;
}

const createImages = (array) => {
  array.forEach((img) => {
    const div = document.createElement('div');
    div.classList.add('img-container');
    const image = document.createElement('img');
    image.src = img;
    div.appendChild(image);
    imgSection.appendChild(div);
  });
};

const getImages = (id) => {
  switch (id) {
    case 1:
      createImages(images.budget);
      break;
    case 2:
      createImages(images.standard);
      break;
    case 3:
      createImages(images.luxury);
      break;
  }
};

getImages(roomId);
getImages(roomId);
