<?php
class Course {
    private $conn;
    private $table = 'courses';

    public $course_id;
    public $course_code;
    public $course_name;
    public $description;
    public $capacity;
    public $semester;
    public $year;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAllCourses() {
        $query = "SELECT c.*, 
                    (SELECT COUNT(*) FROM enrollments WHERE course_id = c.course_id AND status = 'Active') as enrolled_count
                 FROM " . $this->table . " c
                 WHERE c.semester = :semester 
                 AND c.year = :year
                 ORDER BY c.course_code";

        $stmt = $this->conn->prepare($query);
        
        // Default to current year and upcoming semester
        $currentMonth = date('n');
        $semester = $this->getCurrentSemester($currentMonth);
        $year = date('Y');

        $stmt->bindParam(':semester', $semester);
        $stmt->bindParam(':year', $year);
        
        $stmt->execute();
        return $stmt;
    }

    private function getCurrentSemester($month) {
        if ($month >= 1 && $month <= 4) return 'Spring';
        if ($month >= 5 && $month <= 7) return 'Summer';
        return 'Fall';
    }

    public function checkEnrollment($user_id, $course_id) {
        $query = "SELECT * FROM enrollments 
                 WHERE user_id = :user_id 
                 AND course_id = :course_id 
                 AND status = 'Active'";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function enroll($user_id, $course_id) {
        // First check if there's space in the course
        $query = "SELECT capacity, 
                    (SELECT COUNT(*) FROM enrollments 
                     WHERE course_id = :course_id AND status = 'Active') as enrolled_count
                 FROM courses WHERE course_id = :course_id2";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':course_id2', $course_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result['enrolled_count'] >= $result['capacity']) {
            // Course is full, add to waitlist
            return $this->addToWaitlist($user_id, $course_id);
        }

        // Proceed with enrollment
        $query = "INSERT INTO enrollments (user_id, course_id, status) 
                 VALUES (:user_id, :course_id, 'Active')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        
        return $stmt->execute();
    }

    private function addToWaitlist($user_id, $course_id) {
        // Get current waitlist position
        $query = "SELECT COALESCE(MAX(position), 0) + 1 as next_position 
                 FROM waitlist WHERE course_id = :course_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $position = $result['next_position'];

        // Add to waitlist
        $query = "INSERT INTO waitlist (user_id, course_id, position, status) 
                 VALUES (:user_id, :course_id, :position, 'Waiting')";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':position', $position);
        
        return $stmt->execute();
    }

    public function getUserEnrollments($user_id) {
        $query = "SELECT c.*, e.enrollment_id, e.enrollment_date, e.status as enrollment_status
                 FROM " . $this->table . " c
                 JOIN enrollments e ON c.course_id = e.course_id
                 WHERE e.user_id = :user_id AND e.status = 'Active'
                 ORDER BY c.semester, c.course_code";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function getUserWaitlist($user_id) {
        $query = "SELECT c.*, w.waitlist_id, w.position, w.waitlist_date
                 FROM " . $this->table . " c
                 JOIN waitlist w ON c.course_id = w.course_id
                 WHERE w.user_id = :user_id AND w.status = 'Waiting'
                 ORDER BY c.semester, c.course_code";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        return $stmt;
    }

    public function dropCourse($enrollment_id, $user_id) {
        // Verify the enrollment belongs to the user
        $query = "UPDATE enrollments 
                 SET status = 'Dropped' 
                 WHERE enrollment_id = :enrollment_id 
                 AND user_id = :user_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':enrollment_id', $enrollment_id);
        $stmt->bindParam(':user_id', $user_id);
        
        if($stmt->execute()) {
            // Check waitlist and notify next student
            $this->processWaitlist($enrollment_id);
            return true;
        }
        return false;
    }

    private function processWaitlist($enrollment_id) {
        // Get course_id from the dropped enrollment
        $query = "SELECT course_id FROM enrollments WHERE enrollment_id = :enrollment_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':enrollment_id', $enrollment_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if($result) {
            // Get next person on waitlist
            $query = "SELECT * FROM waitlist 
                     WHERE course_id = :course_id 
                     AND status = 'Waiting' 
                     ORDER BY position 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':course_id', $result['course_id']);
            $stmt->execute();
            $waitlist = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if($waitlist) {
                // Update waitlist status to notified
                $query = "UPDATE waitlist 
                         SET status = 'Notified' 
                         WHERE waitlist_id = :waitlist_id";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':waitlist_id', $waitlist['waitlist_id']);
                $stmt->execute();
            }
        }
    }
} 