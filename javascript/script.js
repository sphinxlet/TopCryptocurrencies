function formatNumber(num) {
    return Number(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// onchange function in search input
function searchCrypto(e) {
    $('#cryptoTable tbody').html('<tr><td colspan="6" class="text-center"><p>Loading...</p></td></tr>');

    const value = e.target.value;
    
    $.ajax({
        url: '../php/search_filter.php',
        type: 'GET',
        dataType: 'json', 
        data: {
            action: 'filterByName',
            search_input: value,
        },
        success: function(response) {
            if (Array.isArray(response) && response.length > 0) {
                let tableBody = $('#cryptoTable tbody');
                tableBody.empty();

                //pupoulation of crypto table
                response.forEach(function(currency, index) {
                    if (currency.id && currency.name && currency.symbol && currency.price && currency.market_cap && currency.volume) {
                        let row = `
                            <tr>
                                <td><p>${index + 1}</p></td>
                                <td>
                                    <img class="-crypto-logo" src="https://s2.coinmarketcap.com/static/img/coins/64x64/${currency.id}.png" width="30" loading="lazy" decoding="async" fetchpriority="low" alt="${currency.symbol} logo">
                                    <span>${currency.name}</span>
                                </td>
                                <td>${currency.symbol}</td>
                                <td><p class="text-end">$ ${formatNumber(currency.price)}</p></td>
                                <td><p class="text-end">$ ${formatNumber(currency.market_cap)}</p></td>
                                <td><p class="text-end">$ ${formatNumber(currency.volume)}</p></td>
                            </tr>
                        `;
                    
                        tableBody.append(row);
                    } else {
                        console.error('Invalid currency data:', currency);
                    }
                });
            } else {
                $('#cryptoTable tbody').html('<tr><td colspan="6" class="text-center"><p>No matching cryptocurrencies found</p></td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#cryptoTable tbody').html('<tr><td colspan="6" class="text-center"><p>Failed to load data. Please try again later.</p></td></tr>');
        }
    });
}

$(document).ready(function() {
    refreshTable();
});

//refresh button function
function refreshTable() {
    $('#cryptoTable tbody').html('<tr><td colspan="6" class="text-center"><p>Loading...</p></td></tr>');

    // Populate the database
    $.ajax({
        url: '../php/populate_database.php',
        type: 'GET',
        data: {
            action: 'populateDatabase'
        },
        success: function(response) {
            console.log('Database population successful');
        },
        error: function(xhr, status, error) {
            console.log(`Error populating database: ${status} - ${error}`);
        }
    });

    // Fetch the cryptocurrencies data
    $.ajax({
        url: '../php/get_cryptocurrencies.php',
        type: 'GET',
        dataType: 'json',
        data: {
            action: 'getCryptocurrencies'
        },
        success: function(response) {
            if (Array.isArray(response) && response.length > 0) {
                let tableBody = $('#cryptoTable tbody');
                tableBody.empty();

                //populate crypto table
                response.forEach(function(currency, index) {
                    if (currency.id && currency.name && currency.symbol && currency.price && currency.market_cap && currency.volume) {
                        let row = `
                            <tr>
                                <td><p>${index + 1}</p></td>
                                <td>
                                    <img class="-crypto-logo" src="https://s2.coinmarketcap.com/static/img/coins/64x64/${currency.id}.png" width="30" loading="lazy" decoding="async" fetchpriority="low" alt="${currency.symbol} logo">
                                    <span>${currency.name}</span>
                                </td>
                                <td>${currency.symbol}</td>
                                <td><p class="text-end">$ ${formatNumber(currency.price)}</p></td>
                                <td><p class="text-end">$ ${formatNumber(currency.market_cap)}</p></td>
                                <td><p class="text-end">$ ${formatNumber(currency.volume)}</p></td>
                            </tr>
                        `;
                        tableBody.append(row);
                    } else {
                        console.error('Invalid currency data:', currency);
                    }
                });
            } else {
                $('#cryptoTable tbody').html('<tr><td colspan="6" class="text-center"><p>No data available.</p></td></tr>');
            }
        },
        error: function(xhr, status, error) {
            $('#cryptoTable tbody').html('<tr><td colspan="6" class="text-center"><p>Failed to load data. Please try again later.</p></td></tr>');
        }
    });
}
