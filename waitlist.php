<?php
session_start();
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'classes/Course.php';

checkAuth();

$database = new Database();
$db = $database->getConnection();
$course = new Course($db);

$waitlisted = $course->getUserWaitlist($_SESSION['user_id']);

include 'includes/header.php';
?>

<div class="waitlist-container">
    <h2>My Waitlisted Courses</h2>

    <?php if ($waitlisted->rowCount() > 0): ?>
        <div class="waitlist-grid">
            <?php while ($row = $waitlisted->fetch(PDO::FETCH_ASSOC)): ?>
                <div class="waitlist-card">
                    <div class="waitlist-header">
                        <div class="course-info">
                            <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                            <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                        </div>
                        <div class="position-badge">
                            Position: <?php echo $row['position']; ?>
                        </div>
                    </div>
                    
                    <div class="waitlist-details">
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <div class="detail-row">
                            <span>Semester: <?php echo $row['semester']; ?> <?php echo $row['year']; ?></span>
                            <span>Waitlisted: <?php echo date('M d, Y', strtotime($row['waitlist_date'])); ?></span>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <p>You are not currently on any waitlists.</p>
            <a href="course-registration.php" class="btn btn-primary">Browse Courses</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?> 