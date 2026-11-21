# Stage 1 API Test Results

## Project

Student Course Management REST API

## Environment

- Backend: PHP
- Web Server: NGINX
- Database: MySQL
- Containerization: Docker

## Authentication Tests

| Test | Expected Result | Actual Result | Status |
|---|---|---|---|
| Login with valid credentials | 200 OK | 200 OK | PASS |
| Request without bearer token | 401 Unauthorized | 401 Unauthorized | PASS |
| Request with invalid token | 401 Unauthorized | 401 Unauthorized | PASS |
| Request with expired token | 401 Unauthorized | 401 Unauthorized | PASS |
| Request with valid token | 200 OK | 200 OK | PASS |

## Course Retrieval Tests

| Test | Expected Result | Actual Result | Status |
|---|---|---|---|
| Retrieve all courses | 200 OK | 200 OK | PASS |
| Retrieve one course by ID | 200 OK | 200 OK | PASS |
| Access another student's course | 404 Not Found | 404 Not Found | PASS |

## Ownership and Security

The API checks the bearer token to identify the authenticated student. Course retrieval queries restrict access to courses belonging to that student.

A student attempting to access another student's course receives a 404 Not Found response.

## Automated Acceptance Tests

The automated acceptance test script was executed inside the Docker PHP container.

**Overall Result: PASS (35/35 tests)**

All 35 automated tests passed with zero failures.

The tests covered:

- Student registration and login
- All 12 API endpoint operations
- Course creation, retrieval, updating, and deletion
- Assignment creation, retrieval, updating, and deletion
- Assignment grade recording and updating
- Missing bearer-token rejection
- Invalid bearer-token rejection
- Expired bearer-token rejection
- Handling nonexistent courses and assignments

The API was tested through NGINX using the Docker environment.

## Browser-Based API Tests

The HTML/JavaScript test page (`src/test.html`) was used to test the API through the browser.

**Browser Testing Result: PASS**

The following operations were tested successfully:

| API Operation | Result |
|---|---|
| Student registration | PASS |
| Student login | PASS |
| Get all courses | PASS |
| Get course by ID | PASS |
| Create course | PASS |
| Update course | PASS |
| Delete course | PASS |
| Get all assignments | PASS |
| Get assignment by ID | PASS |
| Create assignment | PASS |
| Update assignment and grade | PASS |
| Delete assignment | PASS |

Additional checks:

- Deleted assignment retrieval returned HTTP 404 as expected.
- Course and assignment operations returned successful HTTP responses.
- The browser test page displayed HTTP status codes and JSON responses.
- The API was accessed through NGINX in the Docker environment.

**Final Browser Testing Status: All 12 API operations tested successfully.**

## Overall Result

All listed authentication, security, retrieval, automated acceptance, and browser-based API tests passed.

**Final Status: PASS**

## Updated Automated Acceptance Test Results

**Run date:** November 20, 2026  
**Command executed:**

```bash
docker compose exec php php tests/acceptance.php
```

**Result: 35 passed, 0 failed**

### Test coverage

| Test category | Assertions | Result |
|---|---:|---|
| Registration, login, and course/assignment operations | 12 | PASS |
| Missing-token rejection across protected endpoints | 10 | PASS |
| Invalid-token rejection across protected endpoints | 10 | PASS |
| Expired-token rejection | 1 | PASS |
| Nonexistent course and assignment responses | 2 | PASS |
| **Total** | **35** | **35 passed, 0 failed** |


### Overall Result

**PASS — All 35 automated acceptance assertions passed.**

