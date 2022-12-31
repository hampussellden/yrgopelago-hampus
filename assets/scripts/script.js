const aside = document.querySelector('aside');
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
  ],
  standard: [
    'assets/images/standard/standard-1.jpeg',
    'assets/images/standard/standard-2.jpeg',
    'assets/images/standard/standard-3.jpeg',
    'assets/images/standard/standard-4.jpeg',
    'assets/images/standard/standard-5.jpeg',
    'assets/images/standard/standard-6.jpeg',
    'assets/images/standard/standard-7.jpeg',
  ],
  luxury: [
    'assets/images/luxury/luxury-1.jpeg',
    'assets/images/luxury/luxury-2.jpeg',
    'assets/images/luxury/luxury-3.jpeg',
    'assets/images/luxury/luxury-4.jpeg',
    'assets/images/luxury/luxury-5.jpeg',
    'assets/images/luxury/luxury-6.png',
    'assets/images/luxury/luxury-7.jpeg',
  ],
};
let roomId = 1;
if (pageHref == websiteHostName + 'standard.php') {
  roomId = 2;
} else if (pageHref == websiteHostName + 'luxury.php') {
  roomId = 3;
}
console.log(pageHref);
console.log(websiteHostName + 'index.php');
console.log(roomId);

const fillAside = (id) => {
  switch (id) {
    case 1:
      images.budget.forEach((img) => {
        const div = document.createElement('div');
        div.classList.add('img-container');
        const image = document.createElement('img');
        image.src = img;
        div.appendChild(image);
        aside.appendChild(div);
      });
      break;
    case 2:
      images.standard.forEach((img) => {
        const div = document.createElement('div');
        div.classList.add('img-container');
        const image = document.createElement('img');
        image.src = img;
        div.appendChild(image);
      });
      break;
    case 3:
      images.luxury.forEach((img) => {
        const div = document.createElement('div');
        div.classList.add('img-container');
        const image = document.createElement('img');
        image.src = img;
        div.appendChild(image);
      });
      break;

    default:
      break;
  }
};

fillAside(roomId);
