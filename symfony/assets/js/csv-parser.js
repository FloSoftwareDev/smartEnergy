const fs = require('fs');
const csv = require('csv-parser');

fs.createReadStream('data.csv')
  .pipe(csv())
  .on('data', (row) => {
    console.log(row);
  });