<?php

header("Content-Type: application/json");

require __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);


// =====================================
// AUTHENTICATE STUDENT USING BEARER TOKEN
// =====================================

function authenticate(PDO $pdo): int
{
    // Read Authorization header
    $authorization = $_SERVER['HTTP_AUTHORIZATION'] ?? '';

    if (empty($authorization) && function_exists('getallheaders')) {
        $headers = getallheaders();
        $authorization = $headers['Authorization']
            ?? $headers['authorization']
            ?? '';
    }

    // Check Bearer token format
    if (!preg_match('/^Bearer\s+([a-f0-9]{64})$/i', trim($authorization), $matches)) {
        http_response_code(401);
        echo json_encode(["error" => "Missing or invalid bearer token"]);
        exit;
    }

    // Hash token to compare with database
    $tokenHash = hash('sha256', $matches[1]);

    // Find valid, non-expired token
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
        http_response_code(401);
        echo json_encode(["error" => "Invalid or expired bearer token"]);
        exit;
    }

    return (int) $token['user_id'];
}


// =====================================
// REGISTER A NEW STUDENT
// POST /api/register
// =====================================

if ($path === '/api/register' && $method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($data) ||
        !isset($data['name']) ||
        !isset($data['email']) ||
        !isset($data['password']) ||
        !is_string($data['name']) ||
        !is_string($data['email']) ||
        !is_string($data['password']) ||
        trim($data['name']) === '' ||
        trim($data['email']) === '' ||
        $data['password'] === ''
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Name, email, and password are required"]);
        exit;
    }

    if (!filter_var(trim($data['email']), FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid email address"]);
        exit;
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
            http_response_code(409);
            echo json_encode(["error" => "Email is already registered"]);
            exit;
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

        http_response_code(201);

        echo json_encode([
            "message" => "Student registered successfully",
            "user_id" => $pdo->lastInsertId()
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Registration failed"]);
    }

    exit;
}


// =====================================
// STUDENT LOGIN
// POST /api/login
// =====================================

if ($path === '/api/login' && $method === 'POST') {

    $data = json_decode(file_get_contents("php://input"), true);

    if (
        !is_array($data) ||
        !isset($data['email'], $data['password']) ||
        !is_string($data['email']) ||
        !is_string($data['password']) ||
        trim($data['email']) === '' ||
        $data['password'] === ''
    ) {
        http_response_code(400);
        echo json_encode(["error" => "Email and password are required"]);
        exit;
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
            http_response_code(401);
            echo json_encode(["error" => "Invalid email or password"]);
            exit;
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

        http_response_code(200);

        echo json_encode([
            "message" => "Login successful",
            "token_type" => "Bearer",
            "token" => $token,
            "expires_at" => $expiresAt
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Login failed"]);
    }

    exit;
}


// =====================================
// GET ALL COURSES FOR LOGGED-IN STUDENT
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

        $statement->execute([
            'user_id' => $userId
        ]);

        $courses = $statement->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode([
            "courses" => $courses
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Could not retrieve courses"]);
    }

    exit;
}


// =====================================
// GET ONE COURSE FOR LOGGED-IN STUDENT
// GET /api/courses/{id}
// =====================================

if (preg_match('#^/api/courses/([1-9][0-9]*)$#', $path, $matches) && $method === 'GET') {

    $userId = authenticate($pdo);
    $courseId = (int) $matches[1];

    try {
        $statement = $pdo->prepare(
            "SELECT id, course_name, course_code, created_at
             FROM courses
             WHERE id = :course_id
             AND user_id = :user_id"
        );

        $statement->execute([
            'course_id' => $courseId,
            'user_id' => $userId
        ]);

        $course = $statement->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            http_response_code(404);
            echo json_encode(["error" => "Course not found"]);
            exit;
        }

        http_response_code(200);
        echo json_encode([
            "course" => $course
        ]);

    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Could not retrieve course"]);
    }

    exit;
}


// UNKNOWN ENDPOINT


http_response_code(404);

echo json_encode([
    "error" => "Endpoint not found"
]);

