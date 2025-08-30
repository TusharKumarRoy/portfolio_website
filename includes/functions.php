<?php 
/**
 * CORE FUNCTIONS
 */


require_once __DIR__ . '/../config/database.php';

date_default_timezone_set('Asia/Dhaka');

// start session if not yet started

if(session_status() === PHP_SESSION_NONE){
    session_start();
}

/**
 * SECURITY FUNCTIONS
 */


function sanitize($data){
    return htmlspecialchars(strip_tags(trim($data)), ENT_QUOTES, 'UTF-8');
}

function isValidEmail($email){
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}



/**
 * ADMIN FUNCTIONS
 */


function verifyAdmin($username , $password){
    $sql = "SELECT id, username, password_hash FROM admin WHERE username = ?";
    $user = fetchRow($sql, [$username]);

    if($user && password_verify($password, $user['password_hash'])){
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_username'] = $user['username'];
        $_SESSION['admin_logged_in'] = true;
        return true;
    }
    return false;
}

function isAdminLoggedIn(){
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin(){
    if(!isAdminLoggedIn()){
        header('Location: login.php');
        exit;
    }
}

function logoutAdmin(){
    session_destroy();
    header('Location: login.php');
    exit;
}




/**
 * PROJECT FUNCTIONS
 */


function getAllVisibleProjects(){
    $sql = "SELECT * FROM projects WHERE is_visible = 1 ORDER BY display_order ASC, created_at DESC";
    return fetchAll($sql) ?: [];
}

function getAllProjectsForAdmin(){
    $sql = "SELECT * FROM projects ORDER BY display_order ASC, created_at DESC";
    return fetchAll($sql) ?: [];
}

function getProject($id){
    $sql = "SELECT * FROM projects WHERE id = ?";
    return fetchRow($sql, [$id]);
}


function addProject($data){
    $sql = "INSERT INTO projects (title, short_description, technologies, image_path, project_url, github_url, display_order, is_visible)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

    $params = [
        $data['title'],
        $data['short_description'],
        $data['technologies'],
        $data['image_path'] ?? '',
        $data['project_url'] ?? '',
        $data['github_url'] ?? '',
        $data['display_order'] ?? 0,
        $data['is_visible'] ?? true
    ];

    return executeQuery($sql, $params) !== false;
}


function updateProject($id, $data){
    $sql = "UPDATE projects
            SET title = ?,
                short_description = ?,
                technologies = ?,
                image_path = ?,
                project_url = ?,
                github_url = ?,
                display_order = ?,
                is_visible = ?
            WHERE id = ?";
    
    $params = [
        $data['title'],
        $data['short_description'],
        $data['technologies'],
        $data['image_path'] ?? '',
        $data['project_url'] ?? '',
        $data['github_url'] ?? '',
        $data['display_order'] ?? 0,
        $data['is_visible'] ?? true,
        $id
    ];

    return executeQuery($sql, $params) !== false;
}


function deleteProject($id) {
    $project = getProject($id);
    
    if (!$project) {
        return ['success' => false, 'message' => 'Project not found'];
    }
    
    $sql = "DELETE FROM projects WHERE id = ?";
    $result = executeQuery($sql, [$id]);
    
    if ($result !== false) {
        
        if (!empty($project['image_path'])) {
            $imagePath = __DIR__ . '/../assets/images/' . $project['image_path'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
        
        return ['success' => true, 'message' => 'Project deleted successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to delete project'];
}


function toggleProjectVisibility($id){
    $sql = "UPDATE projects
            SET is_visible = NOT is_visible
            WHERE id = ?";
    return executeQuery($sql, [$id]) !== false;
}

/**
 * CONTACT FUNCTIONS
 */


function addContactMessage($data){
    $sql = "INSERT INTO contact_messages (name, email, subject , message)
            VALUES(?,?,?,?)";
    $params = [
        $data['name'],
        $data['email'],
        $data['subject'],
        $data['message']
    ];

    return executeQuery($sql, $params) !== false;
}

function getAllContactMessages() {
    $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
    return fetchAll($sql) ?: [];
}

function getContactMessage($id) {
    $sql = "SELECT * FROM contact_messages WHERE id = ?";
    return fetchRow($sql, [$id]);
}

function getUnreadContactCount() {
    $sql = "SELECT COUNT(*) as count FROM contact_messages WHERE is_read = 0";
    $result = fetchRow($sql);
    return $result ? $result['count'] : 0;
}

function markMessageAsRead($id) {
    $sql = "UPDATE contact_messages SET is_read = 1 WHERE id = ?";
    return executeQuery($sql, [$id]) !== false;
}

function deleteContactMessage($id) {
    // First check if message exists
    $message = getContactMessage($id);
    
    if (!$message) {
        return ['success' => false, 'message' => 'Message not found'];
    }
    
    $sql = "DELETE FROM contact_messages WHERE id = ?";
    $result = executeQuery($sql, [$id]);
    
    if ($result !== false) {
        return ['success' => true, 'message' => 'Message deleted successfully'];
    }
    
    return ['success' => false, 'message' => 'Failed to delete message'];
}




/**
 * FILE UPLOAD FUNCTIONS
 */
function uploadProjectImage($file) {
    $uploadDir = __DIR__ . '/../assets/images/uploads/';
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Upload failed'];
    }
    
    if ($file['size'] > $maxSize) {
        return ['success' => false, 'message' => 'File too large'];
    }
    
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    $filename = uniqid() . '.' . $extension;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => 'uploads/' . $filename];
    }
    
    return ['success' => false, 'message' => 'Upload failed'];
}

?>