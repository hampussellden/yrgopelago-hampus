![](https://media.giphy.com/media/YczRJkWkGKy5i/giphy.gif)

# Glacier Island

This Island is the go to winter-resort of Yrgopelago. Here we find the very nice Neversummer hotel.

# Neversummer hotel

This hotel hosts 3 lodges of different levels, budget, standard and luxury.
The hotels inhouse ski shop provides quick access to equipment rental at a small costs.
The neaby city has an active night life for the people who are still energized after a day of skiing and apres.

# Instructions

If your project requires some installation or similar, please inform your user 'bout it. For instance, if you want a more decent indentation of your .php files, you could edit [.editorconfig]('/.editorconfig').

## API Location

The API is available at `https://bosse.ai/neversummer/api/bookings` Responses are sent as JSON.

### Make a booking by POST request

| key          | value    | description                   | input                     |
| ------------ | -------- | ----------------------------- | ------------------------- |
| transferCode | code     | 123-4567-8901-2345-678        | 'transferCode': 'code'    |
| name         | username | john                          | 'name’: ‘username’        |
| arrival      | date     | yyyy-mm-dd                    | ‘arrival’: ‘2023-01-01’   |
| departure    | date     | yyyy-mm-dd                    | 'departure': '2023-01-31' |
| room         | budget   | roomname                      | 'room': 'standard'        |
| features[]   | integer  | only when features wanted     | 'features[]': 1           |
| features[]   | integer  | if more than 1 feature wanted | 'features[]': 2           |

### Look at current bookings using GET request

#### Parameters

| Param         | description                          |
| ------------- | ------------------------------------ |
| id            | integer                              |
| guest_id      | integer                              |
| start_date    | yyyy-mm-dd                           |
| end_date      | yyyy-mm-dd                           |
| room_id       | integer                              |
| transfer_code | 88888888-4444-4444-4444-121212121212 |

# Code review

1. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
2. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
3. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
4. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
5. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
6. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
7. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
8. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
9. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
10. example.js:10-15 - Remember to think about X and this could be refactored using the amazing Y function.
