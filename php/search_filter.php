<?php 
    include "db_connect.php";

    function filterByName() {
        //function to connect to database
        $conn = dbConnect();
    
        if ($conn) {
            $search_input = isset($_GET['search_input']) ? pg_escape_string($conn, $_GET['search_input']) : '';
    
            //select all items from database that are like search input
            $query = "SELECT * FROM cryptocurrency_prices WHERE LOWER(name) LIKE LOWER('%$search_input%') ORDER BY market_cap DESC";
    
            $result = pg_query($conn, $query);
    
            if ($result) {
                if (pg_num_rows($result) > 0) {
                    $currencies = [];
    
                    while ($row = pg_fetch_assoc($result)) {
                        $currencies[] = $row;
                    }

                    //return result
                    return $currencies;
                } else {
                    return ["message" => "No cryptocurrencies found matching your search"];
                }
            } else {
                error_log("Error executing query: " . pg_last_error($conn));
                return ["error" => "Error executing the query"];
            }
        } else {
            error_log("Error connecting to the database");
            return ["error" => "Database connection failed"];
        }
    }
    
    if (isset($_GET['action']) && $_GET['action'] == 'filterByName') {
        header('Content-Type: application/json');
    
        // return result
        echo json_encode(filterByName());
        exit;
    }
    
