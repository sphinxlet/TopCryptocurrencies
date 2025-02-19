# TopCryptocurrencies
 
This project retrieves the top 50 cryptocurrencies from the CoinMarketCap API and stores the data in a PostgreSQL database.

Features

- Fetches cryptocurrency data including name, symbol, price, market cap, and volume.

- Stores the data in a PostgreSQL table cryptocurrency_prices.

Prerequisites

- PHP installed on your system

- PostgreSQL database setup

- Access to the CoinMarketCap API

Installation

1. Clone the repository:

git clone sphinxlet/TopCryptocurrencies

2. Set up the PostgreSQL database:

Create a table by executing the following SQL command:

CREATE TABLE cryptocurrency_prices (
    id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    symbol VARCHAR(10) NOT NULL,
    price FLOAT NOT NULL,
    market_cap FLOAT NOT NULL,
    volume FLOAT NOT NULL
);

3. Configure environment variables:

Edit the config.php file in the root directory and add your database details.

4. Run the Project

Start a local PHP server to run the project with the front-end:

php -S 127.0.0.1:8000

Then, open your browser and go to http://127.0.0.1:8000

Alternatively, if you need to fetch and store data separately, you can execute the PHP script:

php index.php

Usage

- Upon execution, the script connects to the CoinMarketCap API, retrieves the top 50 cryptocurrencies, and inserts their details into the PostgreSQL database.

Acknowledgements

- CoinMarketCap for cryptocurrency data.
