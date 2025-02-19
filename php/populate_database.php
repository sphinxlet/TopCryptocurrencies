<?php 
    include "db_connect.php";

    function populateDatabase() {
        //api call
        $apiUrl = "https://pro-api.coinmarketcap.com/v1/cryptocurrency/listings/latest?limit=50";
        $apiKey = '824ed8bc-cff3-4115-93fd-8d085bf8bab2';

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-CMC_PRO_API_KEY: $apiKey",
            "Accept: application/json"
        ]);

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        $response = curl_exec($ch);

        if ($response === false) {
            error_log('cURL Error: ' . curl_error($ch));
            curl_close($ch);
            return ["error" => "Error fetching data from the API"];
        }

        curl_close($ch);

        //api result
        $data = json_decode($response, true);

        if ($data === null || !isset($data['data'])) {
            error_log("Error: Invalid or missing data in API response");
            return ["error" => "Invalid response from API"];
        }

        //call function to connect to database
        $conn = dbConnect(); 

        if ($conn) {
            try {
                pg_query($conn, "BEGIN");

                foreach ($data['data'] as $crypto) {
                    $id = (int)$crypto['id'];
                    $name = pg_escape_string($conn, $crypto['name']);
                    $symbol = pg_escape_string($conn, $crypto['symbol']);
                    $price = filter_var($crypto['quote']['USD']['price'], FILTER_VALIDATE_FLOAT);
                    $market_cap = filter_var($crypto['quote']['USD']['market_cap'], FILTER_VALIDATE_FLOAT);
                    $volume = filter_var($crypto['quote']['USD']['volume_24h'], FILTER_VALIDATE_FLOAT);

                    if ($price === false || $market_cap === false || $volume === false) {
                        continue; 
                    }

                    //insert selected api data into database & update existing ones
                    $query = "INSERT INTO cryptocurrency_prices (id, name, symbol, price, market_cap, volume) 
                            VALUES ($1, $2, $3, $4, $5, $6) 
                            ON CONFLICT (symbol)
                            DO UPDATE SET 
                                price = EXCLUDED.price,
                                market_cap = EXCLUDED.market_cap,
                                volume = EXCLUDED.volume";

                    $params = array($id, $name, $symbol, $price, $market_cap, $volume);

                    $result = pg_query_params($conn, $query, $params);

                    if (!$result) {
                        error_log("Error inserting/updating cryptocurrency data: " . pg_last_error($conn));
                        pg_query($conn, "ROLLBACK");
                        return ["error" => "Error updating database"];
                    }
                }

                //check which items exist in database (by symbol)
                $current_symbols = array_map(function($crypto) {
                    return $crypto['symbol'];
                }, $data['data']);

                if (count($current_symbols) < 50) {
                    //delete items that are no longer in the top 50
                    $placeholders = implode(',', array_fill(0, count($current_symbols), '$1'));

                    $delete_query = "DELETE FROM cryptocurrency_prices 
                                    WHERE symbol NOT IN ($placeholders)";
    
                    $params = $current_symbols;
    
                    $delete_result = pg_query_params($conn, $delete_query, $params);
    
                    if (!$delete_result) {
                        error_log("Error deleting old cryptocurrency data: " . pg_last_error($conn));
                        pg_query($conn, "ROLLBACK");
                        return ["error" => "Error cleaning up old data"];
                    }
                }

                pg_query($conn, "COMMIT");

                return ["success" => "Database populated successfully"];

            } catch (Exception $e) {
                pg_query($conn, "ROLLBACK");
                error_log("Exception: " . $e->getMessage());
                return ["error" => "An error occurred while processing the data"];
            }
        } else {
            error_log("Error connecting to the database");
            return ["error" => "Database connection failed"];
        }
    }

    if (isset($_GET['action']) && $_GET['action'] == 'filterByName') {
        header('Content-Type: application/json');
    
        echo json_encode(filterByName());
        exit;
    }
