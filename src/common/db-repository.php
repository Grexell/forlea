<?php
$servername = 'localhost';
$username = 'root';
$password = 'root';
$connection = new mysqli($servername, $username, $password);

// todo move to services
function get_categories() {
    return ['category 1', 'category 2', 'category 3'];
}

function get_products() {
    return [];
}

function get_product_info() {
    return [];
}
?>