<?php
session_start();
require_once 'includes/auth_check.php';
require_once 'config/database.php';
require_once 'classes/Course.php';

checkAuth();

$database = new Database();
$db = $database->getConnection();
$course = new Course($db);

$message = '';
$messageType = '';

// Handle course drop
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['drop'])) {
    $enrollment_id = $_POST['enrollment_id'];
    
    if ($course->dropCourse($enrollment_id, $_SESSION['user_id'])) {
        $message = "Course successfully dropped.";
        $messageType = "success";
    } else {
        $message = "Error dropping course. Please try again.";
        $messageType = "error";
    }
}

// Get user's enrollments and waitlisted courses
$enrollments = $course->getUserEnrollments($_SESSION['user_id']);
$waitlisted = $course->getUserWaitlist($_SESSION['user_id']);

include 'includes/header.php';
?>

<div class="my-courses">
    <h2>My Courses</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="courses-section">
        <h3>Current Enrollments</h3>
        <?php if ($enrollments->rowCount() > 0): ?>
            <div class="courses-grid">
                <?php while ($row = $enrollments->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="course-card">
                        <div class="course-header">
                            <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                            <span class="enrollment-date">
                                Enrolled: <?php echo date('M d, Y', strtotime($row['enrollment_date'])); ?>
                            </span>
                        </div>
                        
                        <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="course-details">
                            <span>Semester: <?php echo $row['semester']; ?> <?php echo $row['year']; ?></span>
                        </div>

                        <form method="POST" class="drop-form" onsubmit="return confirm('Are you sure you want to drop this course?');">
                            <input type="hidden" name="enrollment_id" value="<?php echo $row['enrollment_id']; ?>">
                            <button type="submit" name="drop" class="btn btn-danger">Drop Course</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-courses">You are not currently enrolled in any courses.</p>
        <?php endif; ?>
    </div>

    <div class="courses-section waitlist-section">
        <h3>Waitlisted Courses</h3>
        <?php if ($waitlisted->rowCount() > 0): ?>
            <div class="courses-grid">
                <?php while ($row = $waitlisted->fetch(PDO::FETCH_ASSOC)): ?>
                    <div class="course-card waitlist">
                        <div class="course-header">
                            <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                            <span class="waitlist-position">
                                Position: <?php echo $row['position']; ?>
                            </span>
                        </div>
                        
                        <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="course-details">
                            <span>Semester: <?php echo $row['semester']; ?> <?php echo $row['year']; ?></span>
                            <span>Added: <?php echo date('M d, Y', strtotime($row['waitlist_date'])); ?></span>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="no-courses">You are not currently on any waitlists.</p>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 