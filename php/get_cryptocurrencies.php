<?php
    include "db_connect.php";

    function getCryptocurrencies() {
        $currencies = [];
    
        //call function to connect to database
        $conn = dbConnect();
    
        if ($conn) {
            //get all items from database
            $query = "SELECT * FROM cryptocurrency_prices ORDER BY market_cap DESC";
            
            $result = pg_query($conn, $query);
    
            if ($result) {
                if (pg_num_rows($result) > 0) {
                    while ($row = pg_fetch_assoc($result)) {
                        $currencies[] = array_map('htmlspecialchars', $row);
                    }

                    //resturn result
                    return $currencies;
                } else {
                    return [];
                }
            } else {
                error_log("Error executing query: " . pg_last_error($conn));
                return ["error" => "Failed to fetch cryptocurrency data"];
            }
        } else {
            error_log("Error connecting to database");
            return ["error" => "Database connection failed"];
        }
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'getCryptocurrencies') {
        header('Content-Type: application/json');
    
        $cryptocurrencies = getCryptocurrencies();

        // return result
        echo json_encode($cryptocurrencies);
        exit; 
    }
    
?>
