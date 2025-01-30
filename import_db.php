<?php
// Database credentials from your Railway dashboard
$host = 'mysql.railway.internal';
$username = 'root';  // Your Railway DB username
$password = 'YTHbOgINtFZqRZcABqbrRxiPOtnJcqQE';  // Your Railway DB password
$database = 'railway';  // Your Railway DB name

// Create a connection to MySQL
$conn = new mysqli($host, $username, $password, $database);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the SQL file
$sql = file_get_contents('cms (5).sql');

// Split the SQL script into individual queries
$queries = explode(';', $sql);

// Execute each SQL query
foreach ($queries as $query) {
    $query = trim($query);
    if (!empty($query)) {
        if ($conn->query($query) === TRUE) {
            echo "Query executed successfully: $query\n";
        } else {
            echo "Error executing query: $query\n";
        }
    }
}

// Close the connection
$conn->close();
?>
