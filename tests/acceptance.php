<?php

// Connect to the project database.
require __DIR__ . '/../config/database.php';

$apiUrl = 'http://nginx/api';

$passed = 0;
$failed = 0;


// Send a request to the API and return its response.
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
        $options['http']['content'] = json_encode($data);
    }

    $context = stream_context_create($options);

    $responseText = @file_get_contents(
        $apiUrl . $path,
        false,
        $context
    );

    $statusCode = 0;

    if (isset($http_response_header[0])) {
        preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches);

        if (isset($matches[1])) {
            $statusCode = (int) $matches[1];
        }
    }

    return [
        'status' => $statusCode,
        'data' => json_decode($responseText ?: '', true)
    ];
}


// Record a test result.
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


// Create a unique test student for each run.
$randomNumber = bin2hex(random_bytes(4));

$email = 'acceptance' . $randomNumber . '@example.com';
$password = 'TestStudent123!';


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
    'POST /api/register - Register a student',
    $registration['status'] === 201
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
    'POST /api/login - Login with valid credentials',
    $login['status'] === 200 &&
    isset($login['data']['token'])
);

$token = $login['data']['token'] ?? '';

if ($token === '') {
    echo PHP_EOL . "Login failed. Remaining tests cannot run." . PHP_EOL;
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

checkTest(
    'POST /api/courses - Create a course',
    $courseCreation['status'] === 201 &&
    isset($courseCreation['data']['course_id'])
);

$courseId = $courseCreation['data']['course_id'] ?? null;

if (!$courseId) {
    echo PHP_EOL . "Course creation failed. Remaining tests cannot run." . PHP_EOL;
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

checkTest(
    'POST /api/assignments - Create an assignment',
    $assignmentCreation['status'] === 201 &&
    isset($assignmentCreation['data']['assignment_id'])
);

$assignmentId = $assignmentCreation['data']['assignment_id'] ?? null;

if (!$assignmentId) {
    echo PHP_EOL . "Assignment creation failed. Remaining tests cannot run." . PHP_EOL;
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

checkTest(
    'GET /api/courses - Retrieve all courses',
    $allCourses['status'] === 200 &&
    isset($allCourses['data']['courses'])
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

checkTest(
    'GET /api/courses/{id} - Retrieve one course',
    $oneCourse['status'] === 200 &&
    isset($oneCourse['data']['course'])
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

checkTest(
    'PUT /api/courses/{id} - Update a course',
    $courseUpdate['status'] === 200
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

checkTest(
    'GET /api/assignments - Retrieve all assignments',
    $allAssignments['status'] === 200 &&
    isset($allAssignments['data']['assignments'])
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

checkTest(
    'GET /api/assignments/{id} - Retrieve one assignment',
    $oneAssignment['status'] === 200 &&
    isset($oneAssignment['data']['assignment'])
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

checkTest(
    'PUT /api/assignments/{id} - Update assignment and grade',
    $assignmentUpdate['status'] === 200
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

checkTest(
    'DELETE /api/assignments/{id} - Delete an assignment',
    $assignmentDelete['status'] === 200
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

checkTest(
    'DELETE /api/courses/{id} - Delete a course',
    $courseDelete['status'] === 200
);


// --------------------------------------------------
// SECURITY TESTS
// Test missing and invalid tokens on all 10
// protected course and assignment routes.
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
        $method . ' ' . $path . ' - Reject missing token',
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
        $method . ' ' . $path . ' - Reject invalid token',
        $invalidToken['status'] === 401
    );
}


// --------------------------------------------------
// EXPIRED TOKEN TEST
// --------------------------------------------------

$expiredToken = bin2hex(random_bytes(32));
$expiredTokenHash = hash('sha256', $expiredToken);

$statement = $pdo->prepare(
    'INSERT INTO api_tokens (user_id, token_hash, expires_at)
     VALUES (:user_id, :token_hash, DATE_SUB(NOW(), INTERVAL 1 DAY))'
);

// Find the test student's database ID.
$userStatement = $pdo->prepare(
    'SELECT id FROM users WHERE email = :email'
);

$userStatement->execute(['email' => $email]);

$testUser = $userStatement->fetch(PDO::FETCH_ASSOC);

if (!$testUser) {
    echo "Could not find the test student for expired-token test." . PHP_EOL;
    exit(1);
}

// Create an expired token for the test student.
$statement->execute([
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
    'Reject an expired bearer token',
    $expiredRequest['status'] === 401
);


// --------------------------------------------------
// FAILURE TESTS FOR NONEXISTENT RESOURCES
// --------------------------------------------------

$missingCourse = sendRequest(
    '/courses/99999999',
    'GET',
    null,
    $token
);

checkTest(
    'Return 404 for a nonexistent course',
    $missingCourse['status'] === 404
);

$missingAssignment = sendRequest(
    '/assignments/99999999',
    'GET',
    null,
    $token
);

checkTest(
    'Return 404 for a nonexistent assignment',
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

