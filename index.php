<?php
session_start();
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'classes/User.php';

// Only logged-in users can access this page
checkAuth();

$database = new Database();
$db = $database->getConnection();

include 'includes/header.php';
?>

<div class="dashboard-container">
    <div class="welcome-section">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['first_name']); ?>!</h1>
        <p>Manage your course enrollments and academic journey here.</p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>My Courses</h3>
            <p>View and manage your current course enrollments</p>
            <a href="my-courses.php" class="btn btn-primary">View Courses</a>
        </div>

        <div class="dashboard-card">
            <h3>Course Registration</h3>
            <p>Browse and register for new courses</p>
            <a href="course-registration.php" class="btn btn-primary">Register for Courses</a>
        </div>

        <div class="dashboard-card">
            <h3>My Waitlist</h3>
            <p>Check your position on course waitlists</p>
            <a href="waitlist.php" class="btn btn-primary">View Waitlist</a>
        </div>

        <div class="dashboard-card">
            <h3>My Profile</h3>
            <p>Update your personal information</p>
            <a href="profile.php" class="btn btn-primary">Edit Profile</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 