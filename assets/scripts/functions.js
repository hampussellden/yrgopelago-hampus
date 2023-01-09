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

const calculateForm = ($roomId) => {};
