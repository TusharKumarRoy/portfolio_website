<?php
require_once '../includes/functions.php';

requireAdmin();

$success_message = '';
$error_message = '';
$project = null;

// Get project ID
$project_id = (int)($_GET['id'] ?? 0);

if ($project_id <= 0) {
    header('Location: dashboard.php');
    exit();
}

// Get project data
$project = getProject($project_id);

if (!$project) {
    header('Location: dashboard.php?error=project_not_found');
    exit();
}


if ($_POST) {
   
    $title = sanitize($_POST['title'] ?? '');
    $short_description = sanitize($_POST['short_description'] ?? '');
    $technologies = sanitize($_POST['technologies'] ?? '');
    $project_url = sanitize($_POST['project_url'] ?? '');
    $github_url = sanitize($_POST['github_url'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_visible = isset($_POST['is_visible']) ? 1 : 0;
    

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
    
    if (!empty($project_url) && !filter_var($project_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'Project URL is not valid';
    }
    
    if (!empty($github_url) && !filter_var($github_url, FILTER_VALIDATE_URL)) {
        $errors[] = 'GitHub URL is not valid';
    }
    
    // Handle image upload
    $image_path = $project['image_path'];
    if (isset($_FILES['project_image']) && $_FILES['project_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $upload_result = uploadProjectImage($_FILES['project_image']);
        if ($upload_result['success']) {

            if (!empty($project['image_path'])) {
                $old_image_path = __DIR__ . '/../assets/images/' . $project['image_path'];
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
            }
            $image_path = $upload_result['filename'];
        } else {
            $errors[] = 'Image upload failed: ' . $upload_result['message'];
        }
    }
    
   
    if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {
        if (!empty($project['image_path'])) {
            $old_image_path = __DIR__ . '/../assets/images/' . $project['image_path'];
            if (file_exists($old_image_path)) {
                unlink($old_image_path);
            }
        }
        $image_path = '';
    }
    
    // If no errors, update project
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
        
        if (updateProject($project_id, $project_data)) {
            $success_message = 'Project updated successfully!';
            $project = getProject($project_id);
        } else {
            $error_message = 'Failed to update project. Please try again.';
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
    <title>Edit Project - Admin</title>
    <link rel="stylesheet" href="../assets/css/admin_dashboard.css">
    <link rel="stylesheet" href="../assets/css/admin_edit_project.css">
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
            <h1>Edit Project</h1>
            <a href="dashboard.php" class="btn btn-back">← Back to Dashboard</a>
        </div>

        <!-- Success/Error Messages -->
        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form method="POST" enctype="multipart/form-data" id="editProjectForm">
                <div class="form-group">
                    <label for="title">Project Title *</label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           required
                           placeholder="Enter project title"
                           value="<?php echo htmlspecialchars($project['title']); ?>">
                </div>

                <div class="form-group">
                    <label for="short_description">Project Description *</label>
                    <textarea id="short_description" 
                              name="short_description" 
                              required
                              placeholder="Brief description of your project"><?php echo htmlspecialchars($project['short_description']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="technologies">Technologies Used *</label>
                    <input type="text" 
                           id="technologies" 
                           name="technologies" 
                           required
                           placeholder="e.g., HTML, CSS, JavaScript, PHP, React"
                           value="<?php echo htmlspecialchars($project['technologies']); ?>">
                </div>

                <div class="form-group">
                    <label for="project_image">Project Image</label>
                    
                    <?php if (!empty($project['image_path'])): ?>
                        <div class="current-image" id="currentImageDiv">
                            <p><strong>Current Image:</strong></p>
                            <img src="../assets/images/<?php echo htmlspecialchars($project['image_path']); ?>" 
                                 alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <div class="image-actions">
                                <button type="button" class="remove-image-btn" onclick="removeCurrentImage()">Remove Current Image</button>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <input type="file" 
                           id="project_image" 
                           name="project_image" 
                           accept="image/*"
                           onchange="previewImage(this)">
                    <input type="hidden" id="remove_image" name="remove_image" value="0">
                    <small style="color: #666;">Supported formats: JPG, JPEG, PNG, GIF. Max size: 5MB</small>
                    <img id="imagePreview" class="image-preview" alt="Preview">
                </div>

                <div class="form-group half">
                    <label for="project_url">Project URL</label>
                    <input type="url" 
                           id="project_url" 
                           name="project_url" 
                           placeholder="https://example.com"
                           value="<?php echo htmlspecialchars($project['project_url']); ?>">
                </div>

                <div class="form-group half">
                    <label for="github_url">GitHub URL</label>
                    <input type="url" 
                           id="github_url" 
                           name="github_url" 
                           placeholder="https://github.com/username/project"
                           value="<?php echo htmlspecialchars($project['github_url']); ?>">
                </div>

                <div class="form-group half">
                    <label for="display_order">Display Order</label>
                    <input type="number" 
                           id="display_order" 
                           name="display_order" 
                           min="0"
                           placeholder="0"
                           value="<?php echo htmlspecialchars($project['display_order']); ?>">
                    <small style="color: #666;">Lower numbers appear first</small>
                </div>

                <div class="form-group half">
                    <div class="checkbox-group">
                        <input type="checkbox" 
                               id="is_visible" 
                               name="is_visible" 
                               value="1"
                               <?php echo $project['is_visible'] ? 'checked' : ''; ?>>
                        <label for="is_visible">Make project visible on portfolio</label>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Update Project</button>
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
        
        function removeCurrentImage() {
            if (confirm('Are you sure you want to remove the current image?')) {
                document.getElementById('remove_image').value = '1';
                document.getElementById('currentImageDiv').style.display = 'none';
            }
        }
    </script>
</body>
</html>