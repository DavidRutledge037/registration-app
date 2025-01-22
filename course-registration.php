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

    <div class="semester-sections">
        <!-- Spring 2025 Section -->
        <div class="semester-section">
            <h3>Spring 2025 Courses</h3>
            <div class="courses-grid">
                <?php 
                $spring_courses = $course->getCoursesBySemester('Spring', 2025);
                while ($row = $spring_courses->fetch(PDO::FETCH_ASSOC)): 
                    $isEnrolled = $course->checkEnrollment($_SESSION['user_id'], $row['course_id']);
                    $availableSeats = $row['capacity'] - $row['enrolled_count'];
                    $statusClass = $isEnrolled ? 'enrolled' : ($availableSeats <= 0 ? 'full' : '');
                ?>
                    <div class="course-card <?php echo $statusClass; ?>">
                        <?php if ($isEnrolled): ?>
                            <div class="status-banner">
                                Currently Enrolled
                            </div>
                        <?php endif; ?>
                        
                        <div class="course-header">
                            <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                            <span class="semester-badge">
                                <?php echo $row['semester']; ?> <?php echo $row['year']; ?>
                            </span>
                        </div>
                        
                        <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="course-details">
                            <span class="seats-info <?php echo $availableSeats <= 5 ? 'low-seats' : ''; ?>">
                                Available Seats: <?php echo $availableSeats; ?>
                            </span>
                            <?php if ($isEnrolled): ?>
                                <span class="enrollment-status">Enrolled</span>
                            <?php elseif ($availableSeats <= 0): ?>
                                <span class="waitlist-status">Waitlist Available</span>
                            <?php endif; ?>
                        </div>

                        <form method="POST" class="enroll-form">
                            <input type="hidden" name="course_id" value="<?php echo $row['course_id']; ?>">
                            <button type="submit" name="enroll" class="btn btn-primary <?php echo $isEnrolled ? 'enrolled-btn' : ''; ?>"
                                    <?php echo $isEnrolled ? 'disabled' : ''; ?>>
                                <?php 
                                if ($isEnrolled) {
                                    echo 'Already Enrolled';
                                } elseif ($availableSeats <= 0) {
                                    echo 'Join Waitlist';
                                } else {
                                    echo 'Enroll';
                                }
                                ?>
                            </button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Fall 2025 Section -->
        <div class="semester-section future-semester">
            <h3>Fall 2025 Courses</h3>
            <div class="enrollment-notice">
                Fall 2025 enrollment begins June 23, 2025
            </div>
            <div class="courses-grid">
                <?php 
                $fall_courses = $course->getCoursesBySemester('Fall', 2025);
                while ($row = $fall_courses->fetch(PDO::FETCH_ASSOC)): 
                ?>
                    <div class="course-card disabled">
                        <div class="course-header">
                            <h4><?php echo htmlspecialchars($row['course_code']); ?></h4>
                            <span class="semester-badge">
                                <?php echo $row['semester']; ?> <?php echo $row['year']; ?>
                            </span>
                        </div>
                        
                        <h5><?php echo htmlspecialchars($row['course_name']); ?></h5>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        
                        <div class="course-details">
                            <span class="seats-info">
                                Capacity: <?php echo $row['capacity']; ?> seats
                            </span>
                        </div>

                        <button class="btn btn-secondary" disabled>
                            Enrollment Not Yet Open
                        </button>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?> 