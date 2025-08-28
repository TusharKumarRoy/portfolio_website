<?php
// Quick debug script - save this as debug.php in your root directory
echo "<h2>Portfolio Debug Report</h2>";

// 1. Check if files exist
echo "<h3>1. File Existence Check</h3>";
$files = [
    'includes/functions.php',
    'config/database.php'
];

foreach($files as $file) {
    if(file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file NOT FOUND<br>";
    }
}

// 2. Test database connection
echo "<h3>2. Database Connection Test</h3>";
try {
    require_once 'config/database.php';
    $pdo = getDBConnection();
    echo "✅ Database connection successful<br>";
    
    // Test if contact_messages table exists
    $stmt = $pdo->query("SHOW TABLES LIKE 'contact_messages'");
    if($stmt->rowCount() > 0) {
        echo "✅ contact_messages table exists<br>";
        
        // Check table structure
        $stmt = $pdo->query("DESCRIBE contact_messages");
        $columns = $stmt->fetchAll();
        echo "<strong>Table structure:</strong><br>";
        foreach($columns as $column) {
            echo "- " . $column['Field'] . " (" . $column['Type'] . ")<br>";
        }
    } else {
        echo "❌ contact_messages table NOT FOUND<br>";
    }
    
} catch(Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// 3. Test form submission
echo "<h3>3. Form Submission Test</h3>";
if($_POST) {
    echo "<strong>POST data received:</strong><br>";
    foreach($_POST as $key => $value) {
        echo "$key: " . htmlspecialchars($value) . "<br>";
    }
    
    if(isset($_POST['test_contact'])) {
        echo "<br><strong>Testing contact message insertion...</strong><br>";
        try {
            require_once 'includes/functions.php';
            
            $data = [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'subject' => 'Test Subject',
                'message' => 'This is a test message from debug script'
            ];
            
            $result = addContactMessage($data);
            
            if($result) {
                echo "✅ Contact message inserted successfully!<br>";
            } else {
                echo "❌ Failed to insert contact message<br>";
            }
            
        } catch(Exception $e) {
            echo "❌ Error: " . $e->getMessage() . "<br>";
        }
    }
} else {
    echo "No POST data received yet.<br>";
}

// 4. Test form
echo "<h3>4. Test Contact Form</h3>";
?>

<form method="POST" style="background: #f5f5f5; padding: 20px; margin: 20px 0;">
    <h4>Test Form Submission</h4>
    <input type="text" name="name" placeholder="Name" value="Test User" style="display: block; margin: 10px 0; padding: 8px; width: 200px;"><br>
    <input type="email" name="email" placeholder="Email" value="test@example.com" style="display: block; margin: 10px 0; padding: 8px; width: 200px;"><br>
    <input type="text" name="subject" placeholder="Subject" value="Test Subject" style="display: block; margin: 10px 0; padding: 8px; width: 200px;"><br>
    <textarea name="message" placeholder="Message" style="display: block; margin: 10px 0; padding: 8px; width: 200px; height: 80px;">Test message</textarea><br>
    <button type="submit" name="test_contact" style="padding: 10px 20px; background: #007cba; color: white; border: none; cursor: pointer;">Test Submit</button>
</form>

<?php
// 5. Check recent contact messages
echo "<h3>5. Recent Contact Messages</h3>";
try {
    if(isset($pdo)) {
        $stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
        $messages = $stmt->fetchAll();
        
        if($messages) {
            echo "<table border='1' style='border-collapse: collapse;'>";
            echo "<tr><th>ID</th><th>Name</th><th>Email</th><th>Subject</th><th>Created</th></tr>";
            foreach($messages as $msg) {
                echo "<tr>";
                echo "<td>" . $msg['id'] . "</td>";
                echo "<td>" . htmlspecialchars($msg['name']) . "</td>";
                echo "<td>" . htmlspecialchars($msg['email']) . "</td>";
                echo "<td>" . htmlspecialchars($msg['subject']) . "</td>";
                echo "<td>" . $msg['created_at'] . "</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No messages found in database.<br>";
        }
    }
} catch(Exception $e) {
    echo "❌ Error retrieving messages: " . $e->getMessage() . "<br>";
}

// 6. PHP Configuration
echo "<h3>6. PHP Configuration</h3>";
echo "PHP Version: " . phpversion() . "<br>";
echo "Error Reporting: " . error_reporting() . "<br>";
echo "Display Errors: " . ini_get('display_errors') . "<br>";
echo "Log Errors: " . ini_get('log_errors') . "<br>";
echo "Error Log: " . ini_get('error_log') . "<br>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
</style>