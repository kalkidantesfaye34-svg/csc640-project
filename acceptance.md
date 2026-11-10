
# Student Course Management API

## Project Overview

The Student Course Management API is a REST API designed to help students organize their academic information.

The system allows students to manage courses, assignments, deadlines, and grades.

## Acceptance Criteria

This document describes the expected success and failure results for the API endpoints.

## 1. Authentication

* **Description:** Register a new student.
* **Success Case (201 Created):**
* **Input:** Valid JSON payload with unique `email`, `name`, and `password`.
* **Expected Output:** Returns JSON containing success message and user details (excluding password).


* **Failure Case (400 Bad Request):**
* **Input:** Missing required fields (e.g., missing `email` or `password`).
* **Expected Output:** Returns error message indicating missing fields.


* **Failure Case (409 Conflict):**
* **Input:** An email address that is already registered in the system.
* **Expected Output:** Returns error message stating the email is already in use.



### `POST /api/login`

* **Description:** Log in a student and generate an authentication token.
* **Success Case (200 OK):**
* **Input:** Valid registered `email` and correct `password`.
* **Expected Output:** Returns a bearer token (JWT or API token) along with student info.


* **Failure Case (401 Unauthorized):**
* **Input:** Incorrect password or non-existent email.
* **Expected Output:** Returns invalid credentials error message.



---

## 2. Course Endpoints

### `GET /api/courses`

* **Description:** View all courses.
* **Success Case (200 OK):**
* **Input:** None (Public or authenticated request).
* **Expected Output:** Returns a JSON array containing all available courses.



### `GET /api/courses/{id}`

* **Description:** View a specific course by ID.
* **Success Case (200 OK):**
* **Input:** Valid course ID parameter (e.g., `/api/courses/1`).
* **Expected Output:** Returns details of the requested course.


* **Failure Case (404 Not Found):**
* **Input:** A course ID that does not exist in the database.
* **Expected Output:** Returns course not found error message.



### `POST /api/courses`

* **Description:** Create a new course (Bearer-token-protected).
* **Success Case (201 Created):**
* **Input:** Valid header with `Authorization: Bearer <token>` and valid course payload (`title`, `code`, `description`).
* **Expected Output:** Returns created course details and 201 status.


* **Failure Case (401 Unauthorized - Missing Token):**
* **Input:** Request sent without the `Authorization` header.
* **Expected Output:** Returns unauthorized/missing token error.


* **Failure Case (403 Forbidden - Invalid Token):**
* **Input:** Request sent with an expired, malformed, or invalid bearer token.
* **Expected Output:** Returns invalid or expired token error.



### `PUT /api/courses/{id}`

* **Description:** Edit an existing course (Bearer-token-protected).
* **Success Case (200 OK):**
* **Input:** Valid bearer token, valid course ID, and updated course fields.
* **Expected Output:** Returns updated course object.


* **Failure Case (401 Unauthorized):**
* **Input:** Request missing bearer token.
* **Expected Output:** Returns unauthorized error.


* **Failure Case (404 Not Found):**
* **Input:** Valid token, but course ID does not exist.
* **Expected Output:** Returns course not found error.



### `DELETE /api/courses/{id}`

* **Description:** Delete a course (Bearer-token-protected).
* **Success Case (204 No Content):**
* **Input:** Valid bearer token and existing course ID.
* **Expected Output:** Empty response body with 204 status.


* **Failure Case (401 Unauthorized):**
* **Input:** Request missing bearer token.
* **Expected Output:** Returns unauthorized error.


* **Failure Case (404 Not Found):**
* **Input:** Valid token, but course ID does not exist.
* **Expected Output:** Returns course not found error.



---

## 3. Assignment Endpoints

### `GET /api/assignments`

* **Description:** View all assignments.
* **Success Case (200 OK):**
* **Input:** None (or authenticated request).
* **Expected Output:** Returns a JSON array of all assignments.



### `GET /api/assignments/{id}`

* **Description:** View a specific assignment.
* **Success Case (200 OK):**
* **Input:** Valid assignment ID parameter.
* **Expected Output:** Returns details of the requested assignment.


* **Failure Case (404 Not Found):**
* **Input:** Non-existent assignment ID.
* **Expected Output:** Returns assignment not found error.



### `POST /api/assignments`

* **Description:** Create an assignment (Bearer-token-protected).
* **Success Case (201 Created):**
* **Input:** Valid bearer token and valid assignment payload (`course_id`, `title`, `due_date`, `points`).
* **Expected Output:** Returns created assignment details.


* **Failure Case (401 Unauthorized):**
* **Input:** Missing or invalid bearer token.
* **Expected Output:** Returns unauthorized error.


* **Failure Case (400 Bad Request):**
* **Input:** Valid token, but missing required fields or referencing a non-existent `course_id`.
* **Expected Output:** Returns validation error message.



### `PUT /api/assignments/{id}`

* **Description:** Edit an assignment (Bearer-token-protected).
* **Success Case (200 OK):**
* **Input:** Valid bearer token, existing assignment ID, and updated fields.
* **Expected Output:** Returns updated assignment object.


* **Failure Case (401 Unauthorized):**
* **Input:** Missing or invalid bearer token.
* **Expected Output:** Returns unauthorized error.


* **Failure Case (404 Not Found):**
* **Input:** Valid token, but assignment ID does not exist.
* **Expected Output:** Returns assignment not found error.



### `DELETE /api/assignments/{id}`

* **Description:** Delete an assignment (Bearer-token-protected).
* **Success Case (204 No Content):**
* **Input:** Valid bearer token and existing assignment ID.
* **Expected Output:** Empty response body with 204 status.


* **Failure Case (401 Unauthorized):**
* **Input:** Missing or invalid bearer token.
* **Expected Output:** Returns unauthorized error.


* **Failure Case (404 Not Found):**
* **Input:** Valid token, but assignment ID does not exist.
* **Expected Output:** Returns assignment not found error.



## Actual Test Results

To be completed after implementing and testing the API.

