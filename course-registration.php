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

// Handle course enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['enroll'])) {
    $course_id = $_POST['course_id'];
    
    // Check if already enrolled
    if (!$course->checkEnrollment($_SESSION['user_id'], $course_id)) {
        if ($course->enroll($_SESSION['user_id'], $course_id)) {
            $message = "Successfully enrolled in course!";
            $messageType = "success";
        } else {
            $message = "Error enrolling in course. Please try again.";
            $messageType = "error";
        }
    } else {
        $message = "You are already enrolled in this course.";
        $messageType = "error";
    }
}

// Get all available courses
$result = $course->getAllCourses();

include 'includes/header.php';
?>

<div class="course-registration">
    <h2>Course Registration</h2>
    
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <?php echo $message; ?>
        </div>
    <?php endif; ?>

    <div class="semester-info">
        <h3>Available Courses</h3>
    </div>

    <div class="courses-grid">
        <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="course-card">
                <div class="course-header">
                    <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                    <span class="course-status <?php echo strtolower($row['status']); ?>">
                        <?php echo $row['status']; ?>
                    </span>
                </div>
                
                <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                <p><?php echo htmlspecialchars($row['description']); ?></p>
                
                <div class="course-details">
                    <span>Semester: <?php echo $row['semester']; ?> <?php echo $row['year']; ?></span>
                    <span>Available Seats: <?php echo $row['capacity'] - $row['enrolled_count']; ?></span>
                </div>

                <form method="POST" class="enroll-form">
                    <input type="hidden" name="course_id" value="<?php echo $row['course_id']; ?>">
                    <button type="submit" name="enroll" class="btn btn-primary"
                            <?php echo ($course->checkEnrollment($_SESSION['user_id'], $row['course_id'])) ? 'disabled' : ''; ?>>
                        <?php echo ($course->checkEnrollment($_SESSION['user_id'], $row['course_id'])) ? 'Enrolled' : 'Enroll'; ?>
                    </button>
                </form>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 