<?php

// Acceptance tests for the Student Course Management REST API.
// Run inside the PHP container:
// docker compose exec php php tests/acceptance.php

require __DIR__ . '/../config/database.php';

$apiUrl = 'http://nginx/api';
$passed = 0;
$failed = 0;

/**
 * Send an HTTP request to the API and return its status and decoded JSON.
 */
function sendRequest($path, $method = 'GET', $data = null, $token = null)
{
    global $apiUrl;

    $headers = ['Content-Type: application/json'];

    if ($token !== null) {
        $headers[] = 'Authorization: Bearer ' . $token;
    }

    $options = [
        'http' => [
            'method' => $method,
            'header' => implode("\r\n", $headers),
            'ignore_errors' => true,
            'timeout' => 10
        ]
    ];

    if ($data !== null) {
        $json = json_encode($data);

        if ($json === false) {
            return [
                'status' => 0,
                'data' => null,
                'raw' => 'Could not encode request JSON'
            ];
        }

        $options['http']['content'] = $json;
    }

    $context = stream_context_create($options);

    $responseText = @file_get_contents(
        $apiUrl . $path,
        false,
        $context
    );

    $statusCode = 0;

    if (
        isset($http_response_header[0]) &&
        preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)
    ) {
        $statusCode = (int) $matches[1];
    }

    return [
        'status' => $statusCode,
        'data' => json_decode($responseText ?: '', true),
        'raw' => $responseText
    ];
}

/**
 * Print and count one test result.
 */
function checkTest($name, $condition)
{
    global $passed, $failed;

    if ($condition) {
        echo "PASS: " . $name . PHP_EOL;
        $passed++;
    } else {
        echo "FAIL: " . $name . PHP_EOL;
        $failed++;
    }
}

/**
 * Check whether a list contains a record with the requested ID.
 */
function listContainsId($records, $id)
{
    if (!is_array($records)) {
        return false;
    }

    foreach ($records as $record) {
        if (
            is_array($record) &&
            isset($record['id']) &&
            (int) $record['id'] === (int) $id
        ) {
            return true;
        }
    }

    return false;
}

/**
 * Compare a numeric grade whether MySQL returns a string or number.
 */
function gradeEquals($actual, $expected)
{
    return is_numeric($actual) &&
        abs((float) $actual - (float) $expected) < 0.001;
}

// Generate unique test data so the script can run repeatedly.
$randomNumber = bin2hex(random_bytes(4));

$email = 'acceptance' . $randomNumber . '@example.com';
$password = 'TestStudent123!';

echo PHP_EOL . "Starting Student Course Management API acceptance tests..." . PHP_EOL;


// --------------------------------------------------
// TEST 1: STUDENT REGISTRATION
// --------------------------------------------------

$registration = sendRequest(
    '/register',
    'POST',
    [
        'name' => 'Acceptance Test Student',
        'email' => $email,
        'password' => $password
    ]
);

checkTest(
    'POST /api/register - Register a student successfully',
    $registration['status'] === 201 &&
    is_array($registration['data'])
);


// --------------------------------------------------
// TEST 2: STUDENT LOGIN
// --------------------------------------------------

$login = sendRequest(
    '/login',
    'POST',
    [
        'email' => $email,
        'password' => $password
    ]
);

checkTest(
    'POST /api/login - Login returns a usable bearer token',
    $login['status'] === 200 &&
    is_array($login['data']) &&
    !empty($login['data']['token']) &&
    (
        !isset($login['data']['token_type']) ||
        strtolower($login['data']['token_type']) === 'bearer'
    )
);

$token = $login['data']['token'] ?? '';

if ($token === '') {
    echo PHP_EOL . "Login failed; protected endpoint tests cannot continue." . PHP_EOL;
    exit(1);
}


// --------------------------------------------------
// TEST 3: COURSE CREATION
// --------------------------------------------------

$courseCreation = sendRequest(
    '/courses',
    'POST',
    [
        'course_name' => 'Acceptance Test Course',
        'course_code' => 'TEST100'
    ],
    $token
);

$courseId = $courseCreation['data']['course_id'] ?? null;

checkTest(
    'POST /api/courses - Create a course and receive its ID',
    $courseCreation['status'] === 201 &&
    !empty($courseId)
);

if (!$courseId) {
    echo PHP_EOL . "Course creation failed; remaining resource tests cannot continue." . PHP_EOL;
    exit(1);
}


// --------------------------------------------------
// TEST 4: ASSIGNMENT CREATION
// --------------------------------------------------

$assignmentCreation = sendRequest(
    '/assignments',
    'POST',
    [
        'course_id' => (int) $courseId,
        'title' => 'Acceptance Test Assignment',
        'description' => 'Testing assignment creation',
        'due_date' => '2026-12-15',
        'grade' => 85
    ],
    $token
);

$assignmentId = $assignmentCreation['data']['assignment_id'] ?? null;

checkTest(
    'POST /api/assignments - Create an assignment and receive its ID',
    $assignmentCreation['status'] === 201 &&
    !empty($assignmentId)
);

if (!$assignmentId) {
    echo PHP_EOL . "Assignment creation failed; remaining resource tests cannot continue." . PHP_EOL;
    exit(1);
}


// --------------------------------------------------
// TEST 5: RETRIEVE ALL COURSES
// --------------------------------------------------

$allCourses = sendRequest(
    '/courses',
    'GET',
    null,
    $token
);

$courseList = $allCourses['data']['courses'] ?? [];

checkTest(
    'GET /api/courses - List includes the newly created course',
    $allCourses['status'] === 200 &&
    listContainsId($courseList, $courseId)
);


// --------------------------------------------------
// TEST 6: RETRIEVE ONE COURSE
// --------------------------------------------------

$oneCourse = sendRequest(
    '/courses/' . $courseId,
    'GET',
    null,
    $token
);

$courseRecord = $oneCourse['data']['course'] ?? [];

checkTest(
    'GET /api/courses/{id} - Returns the requested course details',
    $oneCourse['status'] === 200 &&
    is_array($courseRecord) &&
    isset($courseRecord['id']) &&
    (int) $courseRecord['id'] === (int) $courseId &&
    ($courseRecord['course_name'] ?? null) === 'Acceptance Test Course' &&
    ($courseRecord['course_code'] ?? null) === 'TEST100'
);


// --------------------------------------------------
// TEST 7: UPDATE A COURSE
// --------------------------------------------------

$courseUpdate = sendRequest(
    '/courses/' . $courseId,
    'PUT',
    [
        'course_name' => 'Updated Acceptance Test Course',
        'course_code' => 'TEST200'
    ],
    $token
);

// Verify that the update was actually saved.
$updatedCourseRead = sendRequest(
    '/courses/' . $courseId,
    'GET',
    null,
    $token
);

$updatedCourse = $updatedCourseRead['data']['course'] ?? [];

checkTest(
    'PUT /api/courses/{id} - Update persists the new course name and code',
    $courseUpdate['status'] === 200 &&
    $updatedCourseRead['status'] === 200 &&
    ($updatedCourse['course_name'] ?? null) === 'Updated Acceptance Test Course' &&
    ($updatedCourse['course_code'] ?? null) === 'TEST200'
);


// --------------------------------------------------
// TEST 8: RETRIEVE ALL ASSIGNMENTS
// --------------------------------------------------

$allAssignments = sendRequest(
    '/assignments',
    'GET',
    null,
    $token
);

$assignmentList = $allAssignments['data']['assignments'] ?? [];

checkTest(
    'GET /api/assignments - List includes the newly created assignment',
    $allAssignments['status'] === 200 &&
    listContainsId($assignmentList, $assignmentId)
);


// --------------------------------------------------
// TEST 9: RETRIEVE ONE ASSIGNMENT
// --------------------------------------------------

$oneAssignment = sendRequest(
    '/assignments/' . $assignmentId,
    'GET',
    null,
    $token
);

$assignmentRecord = $oneAssignment['data']['assignment'] ?? [];

checkTest(
    'GET /api/assignments/{id} - Returns the requested assignment details',
    $oneAssignment['status'] === 200 &&
    is_array($assignmentRecord) &&
    isset($assignmentRecord['id']) &&
    (int) $assignmentRecord['id'] === (int) $assignmentId &&
    ($assignmentRecord['title'] ?? null) === 'Acceptance Test Assignment' &&
    gradeEquals($assignmentRecord['grade'] ?? null, 85)
);


// --------------------------------------------------
// TEST 10: UPDATE AN ASSIGNMENT AND ITS GRADE
// --------------------------------------------------

$assignmentUpdate = sendRequest(
    '/assignments/' . $assignmentId,
    'PUT',
    [
        'title' => 'Updated Acceptance Test Assignment',
        'grade' => 92
    ],
    $token
);

// Verify that the updated title and grade were saved.
$updatedAssignmentRead = sendRequest(
    '/assignments/' . $assignmentId,
    'GET',
    null,
    $token
);

$updatedAssignment = $updatedAssignmentRead['data']['assignment'] ?? [];

checkTest(
    'PUT /api/assignments/{id} - Updated title and grade are saved',
    $assignmentUpdate['status'] === 200 &&
    $updatedAssignmentRead['status'] === 200 &&
    ($updatedAssignment['title'] ?? null) === 'Updated Acceptance Test Assignment' &&
    gradeEquals($updatedAssignment['grade'] ?? null, 92)
);


// --------------------------------------------------
// TEST 11: DELETE AN ASSIGNMENT
// --------------------------------------------------

$assignmentDelete = sendRequest(
    '/assignments/' . $assignmentId,
    'DELETE',
    null,
    $token
);

$deletedAssignmentRead = sendRequest(
    '/assignments/' . $assignmentId,
    'GET',
    null,
    $token
);

checkTest(
    'DELETE /api/assignments/{id} - Delete succeeds and later GET returns 404',
    $assignmentDelete['status'] === 200 &&
    $deletedAssignmentRead['status'] === 404
);


// --------------------------------------------------
// TEST 12: DELETE A COURSE
// --------------------------------------------------

$courseDelete = sendRequest(
    '/courses/' . $courseId,
    'DELETE',
    null,
    $token
);

$deletedCourseRead = sendRequest(
    '/courses/' . $courseId,
    'GET',
    null,
    $token
);

checkTest(
    'DELETE /api/courses/{id} - Delete succeeds and later GET returns 404',
    $courseDelete['status'] === 200 &&
    $deletedCourseRead['status'] === 404
);


// --------------------------------------------------
// SECURITY TESTS: MISSING AND INVALID TOKENS
// --------------------------------------------------

$protectedRoutes = [
    ['GET', '/courses'],
    ['GET', '/courses/99999999'],
    ['POST', '/courses'],
    ['PUT', '/courses/99999999'],
    ['DELETE', '/courses/99999999'],
    ['GET', '/assignments'],
    ['GET', '/assignments/99999999'],
    ['POST', '/assignments'],
    ['PUT', '/assignments/99999999'],
    ['DELETE', '/assignments/99999999']
];

foreach ($protectedRoutes as $route) {

    $method = $route[0];
    $path = $route[1];

    // Test without a bearer token.
    $missingToken = sendRequest(
        $path,
        $method
    );

    checkTest(
        $method . ' ' . $path . ' - Reject missing bearer token with 401',
        $missingToken['status'] === 401
    );

    // Test with an invalid bearer token.
    $invalidToken = sendRequest(
        $path,
        $method,
        null,
        'this-is-an-invalid-token'
    );

    checkTest(
        $method . ' ' . $path . ' - Reject invalid bearer token with 401',
        $invalidToken['status'] === 401
    );
}


// --------------------------------------------------
// EXPIRED TOKEN TEST
// --------------------------------------------------

$expiredToken = bin2hex(random_bytes(32));
$expiredTokenHash = hash('sha256', $expiredToken);

// Find the acceptance-test student's database ID.
$userStatement = $pdo->prepare(
    'SELECT id FROM users WHERE email = :email'
);

$userStatement->execute(['email' => $email]);

$testUser = $userStatement->fetch(PDO::FETCH_ASSOC);

if (!$testUser) {
    echo "Could not find the acceptance-test user for expired-token test." . PHP_EOL;
    exit(1);
}

// Insert an expired token for this student.
$tokenStatement = $pdo->prepare(
    'INSERT INTO api_tokens (user_id, token_hash, expires_at)
     VALUES (:user_id, :token_hash, DATE_SUB(NOW(), INTERVAL 1 DAY))'
);

$tokenStatement->execute([
    'user_id' => (int) $testUser['id'],
    'token_hash' => $expiredTokenHash
]);

$expiredRequest = sendRequest(
    '/courses',
    'GET',
    null,
    $expiredToken
);

checkTest(
    'Reject an expired bearer token with 401',
    $expiredRequest['status'] === 401
);


// --------------------------------------------------
// FAILURE TESTS: NONEXISTENT RESOURCES
// --------------------------------------------------

$missingCourse = sendRequest(
    '/courses/99999999',
    'GET',
    null,
    $token
);

checkTest(
    'GET /api/courses/{id} - Return 404 for a nonexistent course',
    $missingCourse['status'] === 404
);

$missingAssignment = sendRequest(
    '/assignments/99999999',
    'GET',
    null,
    $token
);

checkTest(
    'GET /api/assignments/{id} - Return 404 for a nonexistent assignment',
    $missingAssignment['status'] === 404
);


// --------------------------------------------------
// FINAL RESULTS
// --------------------------------------------------

echo PHP_EOL;

echo "====================================" . PHP_EOL;
echo "       ACCEPTANCE TEST RESULTS      " . PHP_EOL;
echo "====================================" . PHP_EOL;

echo "Passed: " . $passed . PHP_EOL;
echo "Failed: " . $failed . PHP_EOL;
echo "Total:  " . ($passed + $failed) . PHP_EOL;

if ($failed === 0) {
    echo PHP_EOL . "ALL ACCEPTANCE TESTS PASSED!" . PHP_EOL;
} else {
    echo PHP_EOL . "SOME TESTS FAILED. Review the results." . PHP_EOL;
    exit(1);
}

?>

