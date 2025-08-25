<?php
require_once '../includes/functions.php';

requireAdmin();

$success_message = '';
$error_message = '';
$form_data = [];

// Handle form submission
if ($_POST) {
    $form_data = $_POST; // Keep form data for display if errors occur
    
    // Validate required fields
    $title = sanitize($_POST['title'] ?? '');
    $short_description = sanitize($_POST['short_description'] ?? '');
    $technologies = sanitize($_POST['technologies'] ?? '');
    $project_url = sanitize($_POST['project_url'] ?? '');
    $github_url = sanitize($_POST['github_url'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    
    // Validation
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Project title is required';
    }
    
    if (empty($short_description)) {
        $errors[] = 'Project description is required';
    }
    
    if (empty($technologies)) {
        $errors[] = 'Technologies field is required';
    }
    
    // Validate URLs if provided
    if (!empty($project_url) && !filter_var($project_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Project URL is not valid';
    }
    
    if (!empty($github_url) && !filter_var($github_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'GitHub URL is not valid';
    }
    
    // Handle image upload
    $image_path = '';
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = uploadProjectImage($_FILES['project_image']);
        if ($upload_result['success']) {
            $image_path = $upload_result['filename'];
        } else {
            $errors[] = 'Image upload failed: ' . $upload_result['message'];
        }
    }
    
    // If no errors, save project
    if (empty($errors)) {
        $project_data = [
            'title' => $title,
            'short_description' => $short_description,
            'technologies' => $technologies,
            'image_path' => $image_path,
            'project_url' => $project_url,
            'github_url' => $github_url,
            'display_order' => $display_order,
            'is_visible' => $is_visible
        ];
        
        if (addProject($project_data)) {
            $success_message = 'Project added successfully!';
            $form_data = []; // Clear form data on success
        } else {
            $error_message = 'Failed to add project. Please try again.';
        }
    } else {
        $error_message = implode('<br>', $errors);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Project - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <style>
        .form-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }
        
        .form-group textarea {
            height: 100px;
            resize: vertical;
        }
        
        .form-group.half {
            display: inline-block;
            width: 48%;
        }
        
        .form-group.half:first-child {
            margin-right: 4%;
        }
        
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .checkbox-group input[type="checkbox"] {
            width: auto;
        }
        
        .form-actions {
            margin-top: 30px;
            display: flex;
            gap: 10px;
        }
        
        .btn-cancel {
            background: #6c757d;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            display: inline-block;
        }
        
        .btn-cancel:hover {
            background: #5a6268;
        }
        
        .image-preview {
            margin-top: 10px;
            max-width: 200px;
            max-height: 200px;
            border: 1px solid #ddd;
            border-radius: 5px;
            display: none;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 5px;
        }
        
        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }
        
        .alert-error {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            margin: 0;
            color: #333;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <h1>Portfolio Dashboard</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['admin_username']); ?></span>
                <a href="logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="page-header">
            <h1>Add New Project</h1>
            <a href="dashboard.php" class="btn">← Back to Dashboard</a>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
                <br><a href="dashboard.php">Go back to dashboard</a> or <a href="add_project.php">add another project</a>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Project Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required
                           placeholder="Enter project title"
                           value="<?php echo htmlspecialchars($form_data['title'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="short_description">Project Description *</label>
                    <textarea id="short_description" 
                              name="short_description" 
                              required
                              placeholder="Brief description of your project"><?php echo htmlspecialchars($form_data['short_description'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="technologies">Technologies Used *</label>
                    <input type="text" 
                           id="technologies" 
                           name="technologies" 
                           required
                           placeholder="e.g., HTML, CSS, JavaScript, PHP, React"
                           value="<?php echo htmlspecialchars($form_data['technologies'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="project_image">Project Image</label>
                    <input type="file" 
                           id="project_image" 
                           name="project_image" 
                           accept="image/*"
                           onchange="previewImage(this)">
                    <small style="color: #666;">Supported formats: JPG, JPEG, PNG, GIF. Max size: 5MB</small>
                    <img id="imagePreview" class="image-preview" alt="Preview">
                </div>

                <div class="form-group half">
                    <label for="project_url">Project URL</label>
                    <input type="url" 
                           id="project_url" 
                           name="project_url" 
                           placeholder="https://example.com"
                           value="<?php echo htmlspecialchars($form_data['project_url'] ?? ''); ?>">
                </div>

                <div class="form-group half">
                    <label for="github_url">GitHub URL</label>
                    <input type="url" 
                           id="github_url" 
                           name="github_url" 
                           placeholder="https://github.com/username/project"
                           value="<?php echo htmlspecialchars($form_data['github_url'] ?? ''); ?>">
                </div>

                <div class="form-group half">
                    <label for="display_order">Display Order</label>
                    <input type="number" 
                           id="display_order" 
                           name="display_order" 
                           min="0"
                           placeholder="0"
                           value="<?php echo htmlspecialchars($form_data['display_order'] ?? '0'); ?>">
                    <small style="color: #666;">Lower numbers appear first</small>
                </div>

                <div class="form-group half">
                    <div class="checkbox-group">
                        <input type="checkbox" 
                               id="is_visible" 
                               name="is_visible" 
                               value="1"
                               <?php echo (isset($form_data['is_visible']) || !isset($form_data['title'])) ? 'checked' : ''; ?>>
                        <label for="is_visible">Make project visible on portfolio</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Add Project</button>
                    <a href="dashboard.php" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                preview.style.display = 'none';
            }
        }
    </script>
</body>
</html>