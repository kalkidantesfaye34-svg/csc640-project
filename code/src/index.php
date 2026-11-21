<?php

header("Content-Type: application/json");

require __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


// =====================================
// HELPER FUNCTIONS
// =====================================

function respond(int $status, array $data): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

function getRequestData(): ?array
{
    $data = json_decode(file_get_contents("php://input"), true);

    return is_array($data) ? $data : null;
}

function validDate($date): bool
{
    if ($date === null) {
        return true;
    }

    if (!is_string($date)) {
        return false;
    }

    $parsed = DateTime::createFromFormat('!Y-m-d', $date);
    $errors = DateTime::getLastErrors();

    return $parsed !== false
        && $parsed->format('Y-m-d') === $date
        && ($errors === false || (
            $errors['warning_count'] === 0
            && $errors['error_count'] === 0
        ));
}

function validGrade($grade): bool
{
    return $grade === null ||
        (is_numeric($grade) && $grade >= 0 && $grade <= 100);
}


// =====================================
// AUTHENTICATE STUDENT USING BEARER TOKEN
// =====================================

function authenticate(PDO $pdo): int
{
    $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (empty($authorization) && function_exists('getallheaders')) {
        $headers = getallheaders();
        $authorization = $headers['Authorization']
            ?? $headers['authorization']
            ?? '';
    }

    if (!preg_match('/^Bearer\s+([a-f0-9]{64})$/i', trim($authorization), $matches)) {
        respond(401, ["error" => "Missing or invalid bearer token"]);
    }

    $tokenHash = hash('sha256', $matches[1]);

    $statement = $pdo->prepare(
        "SELECT user_id
         FROM api_tokens
         WHERE token_hash = :token_hash
         AND expires_at > NOW()"
    );

    $statement->execute([
        'token_hash' => $tokenHash
    ]);

    $token = $statement->fetch(PDO::FETCH_ASSOC);

    if (!$token) {
        respond(401, ["error" => "Invalid or expired bearer token"]);
    }

    return (int) $token['user_id'];
}


// =====================================
// REGISTER A NEW STUDENT
// POST /api/register
// =====================================

if ($path === '/api/register' && $method === 'POST') {

    $data = getRequestData();

    if (
        !$data ||
        !isset($data['name'], $data['email'], $data['password']) ||
        !is_string($data['name']) ||
        !is_string($data['email']) ||
        !is_string($data['password']) ||
        trim($data['name']) === '' ||
        trim($data['email']) === '' ||
        $data['password'] === ''
    ) {
        respond(400, ["error" => "Name, email, and password are required"]);
    }

    if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
        respond(400, ["error" => "Invalid email address"]);
    }

    $passwordHash = password_hash($data['password'], PASSWORD_DEFAULT);

    try {
        $checkEmail = $pdo->prepare(
            "SELECT id FROM users WHERE email = :email"
        );

        $checkEmail->execute([
            'email' => trim($data['email'])
        ]);

        if ($checkEmail->fetch()) {
            respond(409, ["error" => "Email is already registered"]);
        }

        $statement = $pdo->prepare(
            "INSERT INTO users (name, email, password_hash)
             VALUES (:name, :email, :password_hash)"
        );

        $statement->execute([
            'name' => trim($data['name']),
            'email' => trim($data['email']),
            'password_hash' => $passwordHash
        ]);

        respond(201, [
            "message" => "Student registered successfully",
            "user_id" => $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Registration failed"]);
    }
}


// =====================================
// STUDENT LOGIN
// POST /api/login
// =====================================

if ($path === '/api/login' && $method === 'POST') {

    $data = getRequestData();

    if (
        !$data ||
        !isset($data['email'], $data['password']) ||
        !is_string($data['email']) ||
        !is_string($data['password']) ||
        trim($data['email']) === '' ||
        $data['password'] === ''
    ) {
        respond(400, ["error" => "Email and password are required"]);
    }

    try {
        $statement = $pdo->prepare(
            "SELECT id, password_hash FROM users WHERE email = :email"
        );

        $statement->execute([
            'email' => trim($data['email'])
        ]);

        $user = $statement->fetch(PDO::FETCH_ASSOC);

        if (
            !$user ||
            !password_verify($data['password'], $user['password_hash'])
        ) {
            respond(401, ["error" => "Invalid email or password"]);
        }

        $token = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expiresAt = date('Y-m-d H:i:s', time() + 86400);

        $statement = $pdo->prepare(
            "INSERT INTO api_tokens (user_id, token_hash, expires_at)
             VALUES (:user_id, :token_hash, :expires_at)"
        );

        $statement->execute([
            'user_id' => $user['id'],
            'token_hash' => $tokenHash,
            'expires_at' => $expiresAt
        ]);

        respond(200, [
            "message" => "Login successful",
            "token_type" => "Bearer",
            "token" => $token,
            "expires_at" => $expiresAt
        ]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Login failed"]);
    }
}


// =====================================
// GET ALL COURSES
// GET /api/courses
// =====================================

if ($path === '/api/courses' && $method === 'GET') {

    $userId = authenticate($pdo);

    try {
        $statement = $pdo->prepare(
            "SELECT id, course_name, course_code, created_at
             FROM courses
             WHERE user_id = :user_id
             ORDER BY id"
        );

        $statement->execute(['user_id' => $userId]);

        respond(200, [
            "courses" => $statement->fetchAll(PDO::FETCH_ASSOC)
        ]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not retrieve courses"]);
    }
}


// =====================================
// GET ONE COURSE
// GET /api/courses/{id}
// =====================================

if (preg_match('#^/api/courses/([1-9][0-9]*)$#', $path, $matches)
    && $method === 'GET') {

    $userId = authenticate($pdo);
    $courseId = (int) $matches[1];

    try {
        $statement = $pdo->prepare(
            "SELECT id, course_name, course_code, created_at
             FROM courses
             WHERE id = :course_id AND user_id = :user_id"
        );

        $statement->execute([
            'course_id' => $courseId,
            'user_id' => $userId
        ]);

        $course = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            respond(404, ["error" => "Course not found"]);
        }

        respond(200, ["course" => $course]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not retrieve course"]);
    }
}


// =====================================
// CREATE A COURSE
// POST /api/courses
// =====================================

if ($path === '/api/courses' && $method === 'POST') {

    $userId = authenticate($pdo);
    $data = getRequestData();

    if (
        !$data ||
        !isset($data['course_name']) ||
        !is_string($data['course_name']) ||
        trim($data['course_name']) === ''
    ) {
        respond(400, ["error" => "Course name is required"]);
    }

    $courseCode = $data['course_code'] ?? null;

    if ($courseCode !== null && !is_string($courseCode)) {
        respond(400, ["error" => "Course code must be text or null"]);
    }

    try {
        $statement = $pdo->prepare(
            "INSERT INTO courses (user_id, course_name, course_code)
             VALUES (:user_id, :course_name, :course_code)"
        );

        $statement->execute([
            'user_id' => $userId,
            'course_name' => trim($data['course_name']),
            'course_code' => $courseCode
        ]);

        respond(201, [
            "message" => "Course created successfully",
            "course_id" => (int) $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not create course"]);
    }
}


// =====================================
// UPDATE A COURSE
// PUT /api/courses/{id}
// =====================================

if (preg_match('#^/api/courses/([1-9][0-9]*)$#', $path, $matches)
    && $method === 'PUT') {

    $userId = authenticate($pdo);
    $courseId = (int) $matches[1];
    $data = getRequestData();

    if (!$data) {
        respond(400, ["error" => "A valid JSON request body is required"]);
    }

    try {
        $check = $pdo->prepare(
            "SELECT id FROM courses
             WHERE id = :course_id AND user_id = :user_id"
        );

        $check->execute([
            'course_id' => $courseId,
            'user_id' => $userId
        ]);

        if (!$check->fetch()) {
            respond(404, ["error" => "Course not found"]);
        }

        $fields = [];
        $parameters = [
            'course_id' => $courseId,
            'user_id' => $userId
        ];

        if (array_key_exists('course_name', $data)) {
            if (!is_string($data['course_name']) || trim($data['course_name']) === '') {
                respond(400, ["error" => "Course name cannot be empty"]);
            }

            $fields[] = "course_name = :course_name";
            $parameters['course_name'] = trim($data['course_name']);
        }

        if (array_key_exists('course_code', $data)) {
            if ($data['course_code'] !== null && !is_string($data['course_code'])) {
                respond(400, ["error" => "Course code must be text or null"]);
            }

            $fields[] = "course_code = :course_code";
            $parameters['course_code'] = $data['course_code'];
        }

        if (empty($fields)) {
            respond(400, ["error" => "No course fields provided to update"]);
        }

        $sql = "UPDATE courses SET " . implode(', ', $fields) .
            " WHERE id = :course_id AND user_id = :user_id";

        $statement = $pdo->prepare($sql);
        $statement->execute($parameters);

        respond(200, ["message" => "Course updated successfully"]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not update course"]);
    }
}


// =====================================
// DELETE A COURSE
// DELETE /api/courses/{id}
// =====================================

if (preg_match('#^/api/courses/([1-9][0-9]*)$#', $path, $matches)
    && $method === 'DELETE') {

    $userId = authenticate($pdo);
    $courseId = (int) $matches[1];

    try {
        $statement = $pdo->prepare(
            "DELETE FROM courses
             WHERE id = :course_id AND user_id = :user_id"
        );

        $statement->execute([
            'course_id' => $courseId,
            'user_id' => $userId
        ]);

        if ($statement->rowCount() === 0) {
            respond(404, ["error" => "Course not found"]);
        }

        respond(200, ["message" => "Course deleted successfully"]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not delete course"]);
    }
}


// =====================================
// GET ALL ASSIGNMENTS FOR STUDENT
// GET /api/assignments
// =====================================

if ($path === '/api/assignments' && $method === 'GET') {

    $userId = authenticate($pdo);

    try {
        $statement = $pdo->prepare(
            "SELECT a.id, a.course_id, a.title, a.description,
                    a.due_date, a.grade, a.created_at
             FROM assignments a
             INNER JOIN courses c ON a.course_id = c.id
             WHERE c.user_id = :user_id
             ORDER BY a.id"
        );

        $statement->execute(['user_id' => $userId]);

        respond(200, [
            "assignments" => $statement->fetchAll(PDO::FETCH_ASSOC)
        ]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not retrieve assignments"]);
    }
}


// =====================================
// GET ONE ASSIGNMENT
// GET /api/assignments/{id}
// =====================================

if (preg_match('#^/api/assignments/([1-9][0-9]*)$#', $path, $matches)
    && $method === 'GET') {

    $userId = authenticate($pdo);
    $assignmentId = (int) $matches[1];

    try {
        $statement = $pdo->prepare(
            "SELECT a.id, a.course_id, a.title, a.description,
                    a.due_date, a.grade, a.created_at
             FROM assignments a
             INNER JOIN courses c ON a.course_id = c.id
             WHERE a.id = :assignment_id AND c.user_id = :user_id"
        );

        $statement->execute([
            'assignment_id' => $assignmentId,
            'user_id' => $userId
        ]);

        $assignment = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$assignment) {
            respond(404, ["error" => "Assignment not found"]);
        }

        respond(200, ["assignment" => $assignment]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not retrieve assignment"]);
    }
}


// =====================================
// CREATE AN ASSIGNMENT
// POST /api/assignments
// =====================================

if ($path === '/api/assignments' && $method === 'POST') {

    $userId = authenticate($pdo);
    $data = getRequestData();

    if (
        !$data ||
        !isset($data['course_id'], $data['title']) ||
        !filter_var($data['course_id'], FILTER_VALIDATE_INT) ||
        (int) $data['course_id'] < 1 ||
        !is_string($data['title']) ||
        trim($data['title']) === ''
    ) {
        respond(400, ["error" => "A valid course_id and title are required"]);
    }

    $description = $data['description'] ?? null;
    $dueDate = $data['due_date'] ?? null;
    $grade = $data['grade'] ?? null;

    if ($description !== null && !is_string($description)) {
        respond(400, ["error" => "Description must be text or null"]);
    }

    if (!validDate($dueDate)) {
        respond(400, ["error" => "Due date must use YYYY-MM-DD format or null"]);
    }

    if (!validGrade($grade)) {
        respond(400, ["error" => "Grade must be a number between 0 and 100, or null"]);
    }

    try {
        $check = $pdo->prepare(
            "SELECT id FROM courses
             WHERE id = :course_id AND user_id = :user_id"
        );

        $check->execute([
            'course_id' => (int) $data['course_id'],
            'user_id' => $userId
        ]);

        if (!$check->fetch()) {
            respond(404, ["error" => "Course not found"]);
        }

        $statement = $pdo->prepare(
            "INSERT INTO assignments
             (course_id, title, description, due_date, grade)
             VALUES (:course_id, :title, :description, :due_date, :grade)"
        );

        $statement->execute([
            'course_id' => (int) $data['course_id'],
            'title' => trim($data['title']),
            'description' => $description,
            'due_date' => $dueDate,
            'grade' => $grade
        ]);

        respond(201, [
            "message" => "Assignment created successfully",
            "assignment_id" => (int) $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not create assignment"]);
    }
}


// =====================================
// UPDATE AN ASSIGNMENT AND ITS GRADE
// PUT /api/assignments/{id}
// =====================================

if (preg_match('#^/api/assignments/([1-9][0-9]*)$#', $path, $matches)
    && $method === 'PUT') {

    $userId = authenticate($pdo);
    $assignmentId = (int) $matches[1];
    $data = getRequestData();

    if (!$data) {
        respond(400, ["error" => "A valid JSON request body is required"]);
    }

    try {
        $check = $pdo->prepare(
            "SELECT a.id
             FROM assignments a
             INNER JOIN courses c ON a.course_id = c.id
             WHERE a.id = :assignment_id AND c.user_id = :user_id"
        );

        $check->execute([
            'assignment_id' => $assignmentId,
            'user_id' => $userId
        ]);

        if (!$check->fetch()) {
            respond(404, ["error" => "Assignment not found"]);
        }

        $fields = [];
        $parameters = [
            'assignment_id' => $assignmentId,
            'user_id' => $userId
        ];

        if (array_key_exists('title', $data)) {
            if (!is_string($data['title']) || trim($data['title']) === '') {
                respond(400, ["error" => "Assignment title cannot be empty"]);
            }

            $fields[] = "title = :title";
            $parameters['title'] = trim($data['title']);
        }

        if (array_key_exists('description', $data)) {
            if ($data['description'] !== null && !is_string($data['description'])) {
                respond(400, ["error" => "Description must be text or null"]);
            }

            $fields[] = "description = :description";
            $parameters['description'] = $data['description'];
        }

        if (array_key_exists('due_date', $data)) {
            if (!validDate($data['due_date'])) {
                respond(400, ["error" => "Due date must use YYYY-MM-DD format or null"]);
            }

            $fields[] = "due_date = :due_date";
            $parameters['due_date'] = $data['due_date'];
        }

        if (array_key_exists('grade', $data)) {
            if (!validGrade($data['grade'])) {
                respond(400, ["error" => "Grade must be a number between 0 and 100, or null"]);
            }

            $fields[] = "grade = :grade";
            $parameters['grade'] = $data['grade'];
        }

        if (empty($fields)) {
            respond(400, ["error" => "No assignment fields provided to update"]);
        }

        $sql = "UPDATE assignments SET " . implode(', ', $fields) .
            " WHERE id = :assignment_id
              AND course_id IN (
                  SELECT id FROM courses WHERE user_id = :user_id
              )";

        $statement = $pdo->prepare($sql);
        $statement->execute($parameters);

        respond(200, ["message" => "Assignment updated successfully"]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not update assignment"]);
    }
}


// =====================================
// DELETE AN ASSIGNMENT
// DELETE /api/assignments/{id}
// =====================================

if (preg_match('#^/api/assignments/([1-9][0-9]*)$#', $path, $matches)
    && $method === 'DELETE') {

    $userId = authenticate($pdo);
    $assignmentId = (int) $matches[1];

    try {
        $statement = $pdo->prepare(
            "DELETE a FROM assignments a
             INNER JOIN courses c ON a.course_id = c.id
             WHERE a.id = :assignment_id AND c.user_id = :user_id"
        );

        $statement->execute([
            'assignment_id' => $assignmentId,
            'user_id' => $userId
        ]);

        if ($statement->rowCount() === 0) {
            respond(404, ["error" => "Assignment not found"]);
        }

        respond(200, ["message" => "Assignment deleted successfully"]);

    } catch (PDOException $e) {
        respond(500, ["error" => "Could not delete assignment"]);
    }
}


// =====================================
// UNKNOWN ENDPOINT
// =====================================

respond(404, ["error" => "Endpoint not found"]);