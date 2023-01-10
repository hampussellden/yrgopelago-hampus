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

const getRoomCost = (roomId) => {
  const result = rooms.filter((room) => room.id == roomId);
  return result[0]['cost_per_day'];
};

const getCheckboxStatus = (features, roomId) => {
  const offers = features.filter((d) => d.room_id == roomId);

  // const feature1 = document.getElementById(offers[0]['name']).checked;
  if (document.getElementById(offers[0]['name']).checked) {
    feature1check = true;
  } else {
    feature1check = false;
  }

  if (document.getElementById(offers[1]['name']).checked) {
    feature2check = true;
  } else {
    feature2check = false;
  }
  if (document.getElementById(offers[2]['name']).checked) {
    feature3check = true;
  } else {
    feature3check = false;
  }

  var cost = 0;
  if (feature1check) {
    cost = cost + offers[0]['cost'];
  }
  if (feature2check) {
    cost = cost + offers[1]['cost'];
  }
  if (feature3check) {
    cost = cost + offers[2]['cost'];
  }
  return cost;
};

const getDayCosts = (roomCost, departure, arrival) => {
  const departureDate = departure.value.split('-');
  const arrivalDate = arrival.value.split('-');
  const departureDay = departureDate[2];
  const arrivalDay = arrivalDate[2];
  var minDay = 0;
  const totalDays = Number(departureDay) - Number(arrivalDay);
  if (totalDays == 0) {
    minDay = 1;
  }
  const cost = roomCost * (totalDays + minDay);
  return cost;
};
const currentPrice = document.querySelector('h3.current-price');

const updateLiveCost = (featureCost, daysCosts) => {
  const totalCost = featureCost + daysCosts;
  if (totalCost < 0 || NaN) {
    currentPrice.style.display.none;
  } else {
    currentPrice.innerHTML = `current booking: $${totalCost}`;
  }
};

const calculateForm = (features, roomCost, departure, arrival) => {
  updateLiveCost(
    getCheckboxStatus(features, roomId),
    getDayCosts(roomCost, departure, arrival)
  );
};
