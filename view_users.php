<?php
// Database configuration
$config = [
    'host' => 'localhost',
    'db'   => 'onlinecomputerstore_db',
    'user' => 'root',
    'pass' => ''
];

try {
    // Connect to the database
    $conn = new mysqli($config['host'], $config['user'], $config['pass'], $config['db']);

    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }

    // Fetch all users from the database
    $query = "SELECT id, username, role, created_at FROM users ORDER BY id";
    $result = $conn->query($query);

    if (!$result) {
        throw new Exception("Error fetching users: " . $conn->error);
    }

    // Display the results
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Users in Database</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f5f5f5;
            }
            .container {
                background-color: white;
                padding: 20px;
                border-radius: 5px;
                box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            }
            h1, h2 {
                color: #333;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background-color: #f2f2f2;
                font-weight: bold;
            }
            tr:hover {
                background-color: #f5f5f5;
            }
            .admin {
                color: #d32f2f;
                font-weight: bold;
            }
            .user {
                color: #388e3c;
            }
            .actions {
                margin-top: 20px;
            }
            .btn {
                display: inline-block;
                padding: 10px 15px;
                background-color: #4CAF50;
                color: white;
                text-decoration: none;
                border-radius: 4px;
                margin-right: 10px;
            }
            .btn:hover {
                background-color: #45a049;
            }
            .btn-danger {
                background-color: #f44336;
            }
            .btn-danger:hover {
                background-color: #d32f2f;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <h1>Users in Database</h1>
            <p>This page shows all users registered in the database.</p>";

    if ($result->num_rows > 0) {
        echo "<table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Role</th>
                    <th>Created At</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            $roleClass = $row['role'] === 'admin' ? 'admin' : 'user';
            echo "<tr>
                    <td>" . htmlspecialchars($row['id']) . "</td>
                    <td>" . htmlspecialchars($row['username']) . "</td>
                    <td class='" . $roleClass . "'>" . htmlspecialchars($row['role']) . "</td>
                    <td>" . htmlspecialchars($row['created_at']) . "</td>
                  </tr>";
        }

        echo "</table>";
    } else {
        echo "<p>No users found in the database.</p>";
    }

    echo "<div class='actions'>
            <a href='insert_users.php' class='btn'>Add Test Users</a>
            <a href='test_login.php' class='btn'>Test Login</a>
            <a href='login.html' class='btn'>Go to Login Page</a>
          </div>
        </div>
    </body>
    </html>";

} catch (Exception $e) {
    echo "<div style='color: red; padding: 20px;'>Error: " . $e->getMessage() . "</div>";
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?> 