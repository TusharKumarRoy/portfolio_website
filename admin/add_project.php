<?php
require_once '../includes/functions.php';

requireAdmin();

$success_message = '';
$error_message = '';
$form_data = [];


if ($_POST) {
    $form_data = $_POST;
    
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
            $form_data = [];
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
    <link rel="stylesheet" href="../assets/css/admin_add_project.css">
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
            <a href="dashboard.php" class="btn btn-back ">← Back to Dashboard</a>
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
            <form method="POST" enctype="multipart/form-data" id="projectForm">
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
                    <small>Supported formats: JPG, JPEG, PNG, GIF. Max size: 5MB</small>
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
                    <small>Lower numbers appear first</small>
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
                    <button type="submit" class="btn btn-sm btn-success" id="submitBtn">Add Project</button>
                    <a href="dashboard.php" class="btn btn-sm btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const file = input.files[0];
            
            if (file) {
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('File size must be less than 5MB');
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
                // Check file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, JPEG, PNG, GIF)');
                    input.value = '';
                    preview.style.display = 'none';
                    return;
                }
                
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

        // Form validation
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            
            // Add loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Adding Project...';
            
            // Basic client-side validation
            const requiredFields = ['title', 'short_description', 'technologies'];
            let isValid = true;
            
            requiredFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                    field.classList.add('success');
                }
            });
            
            // Validate URLs if provided
            const urlFields = ['project_url', 'github_url'];
            urlFields.forEach(fieldName => {
                const field = document.getElementById(fieldName);
                if (field.value && !isValidUrl(field.value)) {
                    field.classList.add('error');
                    isValid = false;
                } else if (field.value) {
                    field.classList.remove('error');
                    field.classList.add('success');
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                submitBtn.classList.remove('loading');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Add Project';
                alert('Please correct the highlighted fields');
            }
        });

        // URL validation function
        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        // Real-time validation
        const inputs = document.querySelectorAll('input, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('error');
                    this.classList.remove('success');
                } else if (this.value.trim()) {
                    this.classList.remove('error');
                    this.classList.add('success');
                }
            });
        });

        // Auto-resize textarea
        const textarea = document.getElementById('short_description');
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(this.scrollHeight, 100) + 'px';
        });
    </script>
</body>
</html>