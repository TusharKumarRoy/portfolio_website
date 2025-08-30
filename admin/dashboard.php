<?php
require_once '../includes/functions.php';

requireAdmin();

$success_message = '';
$error_message = '';

// Handle actions
if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'toggle_project':
            if (isset($_GET['id'])) {
                toggleProjectVisibility($_GET['id']);
                header('Location: dashboard.php');
                exit();
            }
            break;
        case 'delete_project':
            if (isset($_GET['id'])) {
                $result = deleteProject($_GET['id']);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
            }
            break;
        case 'mark_read':
            if (isset($_GET['id'])) {
                markMessageAsRead($_GET['id']);
                header('Location: dashboard.php');
                exit();
            }
            break;
        case 'delete_message':
            if (isset($_GET['id'])) {
                $result = deleteContactMessage($_GET['id']);
                if ($result['success']) {
                    $success_message = $result['message'];
                } else {
                    $error_message = $result['message'];
                }
            }
            break;
    }
}

// Get data for dashboard
$projects = getAllProjectsForAdmin();
$messages = getAllContactMessages();
$unreadCount = getUnreadContactCount();

// Calculate stats
$totalProjects = count($projects);
$visibleProjects = count(array_filter($projects, function($p) { return $p['is_visible']; }));
$hiddenProjects = $totalProjects - $visibleProjects;
$totalMessages = count($messages);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard - Portfolio</title>
<link rel="stylesheet" href="../assets/css/admin_dashboard.css">
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

<?php /*
    Success/Error Messages
    <?php if ($success_message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
    <?php endif; ?>
    <?php if ($error_message): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>
*/ ?>


    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card"><div class="stat-number"><?php echo $totalProjects; ?></div><div class="stat-label">Total Projects</div></div>
        <div class="stat-card"><div class="stat-number"><?php echo $visibleProjects; ?></div><div class="stat-label">Visible Projects</div></div>
        <div class="stat-card"><div class="stat-number"><?php echo $hiddenProjects; ?></div><div class="stat-label">Hidden Projects</div></div>
        <div class="stat-card"><div class="stat-number"><?php echo $unreadCount; ?></div><div class="stat-label">Unread Messages</div></div>
    </div>

    <!-- Projects Section -->
    <section>
        <div class="section-header">
            <h2>Projects Management</h2>
            <a href="add_project.php" class="btn btn-success">+ Add New Project</a>
        </div>
        <div class="table-container">
            <?php if (empty($projects)): ?>
                <div class="empty-state">
                    <h3>No projects yet</h3>
                    <p>Start by adding your first project!</p>
                    <br>
                    <a href="add_project.php" class="btn btn-success">Add New Project</a>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Technologies</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <?php if ($project['image_path']): ?>
                                    <img src="../assets/images/<?php echo htmlspecialchars($project['image_path']); ?>" 
                                         alt="<?php echo htmlspecialchars($project['title']); ?>" class="project-img">
                                <?php else: ?>
                                    <div class="project-img" style="background:#e9ecef;display:flex;align-items:center;justify-content:center;font-size:12px;color:#6c757d;">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo htmlspecialchars($project['title']); ?></strong><br>
                                <small style="color:#7f8c8d;"><?php echo htmlspecialchars(substr($project['short_description'],0,50)); ?>...</small>
                            </td>
                            <td><?php echo htmlspecialchars($project['technologies']); ?></td>
                            <td><span class="status-badge <?php echo $project['is_visible'] ? 'status-visible':'status-hidden'; ?>"><?php echo $project['is_visible']?'Visible':'Hidden'; ?></span></td>
                            <td><?php echo $project['display_order']; ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-success">Edit</a>
                                    <a href="dashboard.php?action=toggle_project&id=<?php echo $project['id']; ?>" 
                                       class="btn btn-sm <?php echo $project['is_visible'] ? 'btn-danger':'btn-success'; ?>"
                                       onclick="return confirm('Are you sure you want to <?php echo $project['is_visible'] ? 'hide':'show'; ?> this project?')">
                                       <?php echo $project['is_visible'] ? 'Hide':'Show'; ?>
                                    </a>
                                    <?php if ($project['project_url']): ?>
                                        <a href="<?php echo htmlspecialchars($project['project_url']); ?>" target="_blank" class="btn btn-sm btn-visit">Visit</a>
                                    <?php endif; ?>
                                    <a href="dashboard.php?action=delete_project&id=<?php echo $project['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to DELETE this project? This action cannot be undone!')">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>

    <!-- Messages Section -->
    <section>
        <div class="section-header">
            <h2>Contact Messages (<?php echo $totalMessages; ?>)</h2>
        </div>
        <div class="table-container">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <h3>No messages yet</h3>
                    <p>Messages from your contact form will appear here.</p>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th><th>Email</th><th>Date</th><th>Status</th><th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $message): ?>
                        <tr style="<?php echo !$message['is_read'] ? 'background:#fff3cd;' : ''; ?>">
                            <td><strong><?php echo htmlspecialchars($message['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($message['email']); ?></td>
                            <td><?php echo date('M j, Y', strtotime($message['created_at']) + (12*3600) + (60*60)); ?></td>
                            <td><span class="status-badge <?php echo $message['is_read'] ? 'status-read':'status-unread'; ?>"><?php echo $message['is_read'] ? 'Read':'New'; ?></span></td>
                            <td>
                                <div class="action-buttons">
                                    <!-- VIEW BUTTON USING DATA ATTRIBUTES -->
                                    <button 
                                        class="btn btn-sm view-btn"
                                        data-id="<?php echo $message['id']; ?>"
                                        data-name="<?php echo htmlspecialchars($message['name'], ENT_QUOTES); ?>"
                                        data-email="<?php echo htmlspecialchars($message['email'], ENT_QUOTES); ?>"
                                        data-subject="<?php echo htmlspecialchars($message['subject'] ?: 'No Subject', ENT_QUOTES); ?>"
                                        data-message="<?php echo htmlspecialchars($message['message'], ENT_QUOTES); ?>"
                                        data-date="<?php echo date('M j, Y g:i A', strtotime($message['created_at']) + (12*3600) + (60*60)); ?>"
                                    >View</button>
                                    <a href="dashboard.php?action=delete_message&id=<?php echo $message['id']; ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Are you sure you want to delete this message? This action cannot be undone!')">
                                        Delete
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </section>
</div>

<script src="../assets/js/admin_script.js"></script>
<script src="/assets/js/shortcut_toggle.js"></script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.view-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const email = button.dataset.email;
            const subject = button.dataset.subject;
            const message = button.dataset.message;
            const date = button.dataset.date;

            // Remove existing modal if any
            const existingModal = document.getElementById('messageModal');
            if (existingModal) existingModal.remove();

            // Create modal
            const modalHTML = `
                <div id="messageModal" class="modal-overlay">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>Message Details</h3>
                        </div>
                        <div class="modal-body">
                            <div class="message-form">
                                <div class="form-group"><label>Name:</label><input type="text" value="${name}" readonly></div>
                                <div class="form-group"><label>Email:</label><input type="email" value="${email}" readonly></div>
                                <div class="form-group"><label>Subject:</label><input type="text" value="${subject}" readonly></div>
                                <div class="form-group"><label>Date:</label><input type="text" value="${date}" readonly></div>
                                <div class="form-group"><label>Message:</label><textarea readonly rows="8">${message}</textarea></div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <a href="https://mail.google.com/mail/?view=cm&to=${encodeURIComponent(email)}" target="_blank" class="btn btn-sm btn-success">Reply via Email</a>
                            <button class="btn btn-sm close-modal">Close</button>
                            <a href="dashboard.php?action=mark_read&id=${id}" class="btn btn-sm btn-success">Mark as Read</a>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHTML);

            const modal = document.getElementById('messageModal');
            modal.querySelectorAll('.close-modal').forEach(btn => btn.addEventListener('click', () => modal.remove()));
            modal.addEventListener('click', e => { if(e.target === modal) modal.remove(); });
            document.addEventListener('keydown', function escHandler(e) { if(e.key==='Escape'){ modal.remove(); document.removeEventListener('keydown', escHandler); } });
        });
    });
});
</script>

</body>
</html>
