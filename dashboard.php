<?php
session_start();
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'classes/Course.php';

checkAuth();

$database = new Database();
$db = $database->getConnection();
$course = new Course($db);

// Get user's enrollments
$enrollments = $course->getUserEnrollments($_SESSION['user_id']);
$waitlisted = $course->getUserWaitlist($_SESSION['user_id']);

include 'includes/header.php';
?>

<div class="dashboard-container">
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h2>
    
    <div class="dashboard-grid">
        <!-- Current Enrollments Section -->
        <div class="dashboard-section">
            <h3>Current Enrollments</h3>
            <?php if ($enrollments->rowCount() > 0): ?>
                <div class="enrollment-list">
                    <?php while ($row = $enrollments->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="enrollment-card">
                            <div class="course-info">
                                <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                                <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                            </div>
                            <div class="semester-info">
                                <?php echo $row['semester']; ?> <?php echo $row['year']; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-data">You are not enrolled in any courses.</p>
                <a href="course-registration.php" class="btn btn-primary">Register for Courses</a>
            <?php endif; ?>
        </div>

        <!-- Waitlist Section -->
        <div class="dashboard-section">
            <h3>Waitlisted Courses</h3>
            <?php if ($waitlisted->rowCount() > 0): ?>
                <div class="waitlist-list">
                    <?php while ($row = $waitlisted->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="waitlist-card">
                            <div class="course-info">
                                <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                                <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                            </div>
                            <div class="position-info">
                                Position: <?php echo $row['position']; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="no-data">You are not on any waitlists.</p>
            <?php endif; ?>
        </div>

        <!-- Quick Links Section -->
        <div class="dashboard-section">
            <h3>Quick Links</h3>
            <div class="quick-links">
                <a href="course-registration.php" class="quick-link">
                    <i class="fas fa-book"></i>
                    Course Registration
                </a>
                <a href="my-courses.php" class="quick-link">
                    <i class="fas fa-graduation-cap"></i>
                    My Courses
                </a>
                <a href="profile.php" class="quick-link">
                    <i class="fas fa-user"></i>
                    Profile
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 