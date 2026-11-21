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

## Overall Result

All authentication and course retrieval tests listed above passed.

## Notes

These tests were performed using the local Docker environment with PHP, NGINX, and MySQL.