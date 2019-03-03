<?php

function get_db_connection()
{
    $servername = 'localhost';
    $username = 'root';
    $password = 'root';
    $dbname = 'forlea';

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function get_global_or_new_connection()
{
    if (empty($GLOBALS['db_connection'])) {
        $conn = get_db_connection();
        $GLOBALS['db_connection'] = $conn;
    } else {
        $conn = $GLOBALS['db_connection'];
    }

    return $conn;
}

function get_global_or_new_statement($key, $statement)
{
    $conn = get_global_or_new_connection();
    $stmt = $conn->prepare($statement);
    return $stmt;
}

function get_categories()
{
    $connection = get_db_connection();
    $sql = "SELECT * FROM category";
    if (!$result = $connection->query($sql)) {
        echo $connection->error;
    }
    $categories = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $category = new stdClass();
            $category->id = $row['id'];
            $category->name = $row['name'];
            array_push($categories, $category);
        }
    }
    $connection->close();

    return $categories;
}

?>