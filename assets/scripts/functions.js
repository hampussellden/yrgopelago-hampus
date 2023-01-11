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
const countCheckboxes = (features, roomId) => {
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

  var count = 0;
  if (feature1check) {
    count++;
  }
  if (feature2check) {
    count++;
  }
  if (feature3check) {
    count++;
  }
  return count;
};
const countDays = (departure, arrival) => {
  const departureDate = departure.value.split('-');
  const arrivalDate = arrival.value.split('-');
  const departureDay = departureDate[2];
  const arrivalDay = arrivalDate[2];
  const days = Number(departureDay) - Number(arrivalDay);
  const totalDays = days + 1;
  return totalDays;
};
const getDayCosts = (roomCost, departure, arrival) => {
  const totalDays = countDays(departure, arrival);
  const cost = roomCost * totalDays;
  return cost;
};
const getDiscountsPercent = (totalDays) => {
  if (totalDays >= 4) {
    return 0.8;
  } else {
    return 1;
  }
};
const getDiscountsInteger = (features, roomId) => {
  if (countCheckboxes(features, roomId) >= 2) {
    return 2;
  } else {
    return 0;
  }
};
const currentPrice = document.querySelector('h3.current-price');

const updateLiveCost = (
  featureCost,
  daysCosts,
  discountPercent,
  discountInteger
) => {
  const totalCost =
    (featureCost + daysCosts) * discountPercent - discountInteger;
  currentPrice.innerHTML = `current booking: $${totalCost.toFixed(2)}`;
};

const calculateForm = (features, roomCost, departure, arrival) => {
  updateLiveCost(
    getCheckboxStatus(features, roomId),
    getDayCosts(roomCost, departure, arrival),
    getDiscountsPercent(countDays(departure, arrival)),
    getDiscountsInteger(features, roomId)
  );
};
