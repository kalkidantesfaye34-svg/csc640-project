---
marp: true
theme: default
paginate: true
---

# Student Course Management API

## Complete API Tutorial

**Technology:** PHP, NGINX, MySQL, Docker

**Stage 1 Implementation**

---

# 1. Project Overview

The Student Course Management API allows students to:

- Register and log in securely.
- View and manage their courses.
- Create and manage assignments.
- Record and update assignment grades.
- Track assignment descriptions and due dates.

---

The API uses:

- **PHP** to process API requests.
- **NGINX** to receive HTTP requests.
- **MySQL** to store application data.
- **Docker** to run the application services.

---

# 2. Starting the API

Open a terminal in the project directory and run:

```bash
docker compose up -d --build
```

The API is available at:

`http://localhost:8080`

The browser-based testing page is:

`http://localhost:8080/test.html`

To stop the containers:

```bash
docker compose down
```

The Docker Compose configuration starts the PHP, NGINX, and MySQL services.

---

# 3. Authentication

The API uses bearer-token authentication to protect student information.

Registration creates a student account. Login verifies the student's credentials and generates an authentication token.
Include the token in the HTTP request header for protected endpoints:

```http
Authorization: Bearer YOUR_TOKEN
```

### Authentication responses

- `200 OK` — Login successful.
- `401 Unauthorized` — Missing, invalid, or expired token.
- `400 Bad Request` — Required login information is missing.
  
Tokens expire after 24 hours.

The API stores a SHA-256 hash of the token rather than the original token.

---

# 4. Register a Student

**Method:** `POST`

**Path:** `/api/register`

**Authentication:** Not required

Creates a new student account.

### Request body

```json
{
  "name": "Alex Student",
  "email": "alex@example.com",
  "password": "Student123!"
}
```
---

### Successful response

**Status:** `201 Created`

```json
{
  "message": "User registered successfully",
  "user_id": 1
}
```

The API validates the submitted information and hashes the password before saving the account.

### Possible errors

- `400 Bad Request` — Missing or invalid information.
- `409 Conflict` — Email address is already registered.

---

# 5. Student Login

**Method:** `POST`

**Path:** `/api/login`

**Authentication:** Not required

Verifies the student's email and password and generates a bearer token.

---
### Request body
```json
{
  "email": "alex@example.com",
  "password": "Student123!"
}
```
### Successful response
**Status:** `200 OK`
---
```json
{
  "message": "Login successful",
  "token_type": "Bearer",
  "token": "YOUR_GENERATED_TOKEN",
  "expires_at": "TOKEN_EXPIRATION_DATE"
}
```
---
The generated token must be included in the Authorization header when accessing protected endpoints.
### Possible errors

- `400 Bad Request` — Missing login information.
- `401 Unauthorized` — Incorrect email or password.

---

# 6. Retrieve All Courses

**Method:** `GET`

**Path:** `/api/courses`

**Authentication:** Required

Retrieves all courses belonging to the authenticated student.

### Request header

```http
Authorization: Bearer YOUR_TOKEN
```
---
### Successful response

**Status:** `200 OK`

```json
{
  "courses": [
    {
      "id": 1,
      "user_id": 1,
      "course_name": "CSC 640",
      "course_code": "CSC640"
    }
  ]
}
```
---
If the student has no courses, the response contains an empty courses array:

```json
{
  "courses": []
}
```

### Possible errors

- `401 Unauthorized` — Missing, invalid, or expired token.

---

# 7. Retrieve One Course

**Method:** `GET`

**Path:** `/api/courses/{id}`

**Authentication:** Required

Retrieves an individual course using its ID.

### Example request

```http
GET /api/courses/1
Authorization: Bearer YOUR_TOKEN
```
---
### Successful response

**Status:** `200 OK`

```json
{
  "course": {
    "id": 1,
    "user_id": 1,
    "course_name": "CSC 640",
    "course_code": "CSC640"
  }
}
```

The API verifies that the course belongs to the authenticated student.

### Possible errors

- `401 Unauthorized` — Missing, invalid, or expired token.
- `404 Not Found` — Course does not exist or does not belong to the student.

---

# 8. Create a Course

**Method:** `POST`

**Path:** `/api/courses`

**Authentication:** Required

Creates a new course for the authenticated student.

### Request header

```http
Authorization: Bearer YOUR_TOKEN
```
---
### Request body

```json
{
  "course_name": "Introduction to Computer Science",
  "course_code": "CSC101"
}
```
---
### Successful response

**Status:** `201 Created`

```json
{
  "message": "Course created successfully",
  "course_id": 1
}
```

The course is associated with the authenticated student's account.
---
### Possible errors

- `400 Bad Request` — Missing or invalid course information.
- `401 Unauthorized` — Missing, invalid, or expired token.

---

# 9. Update a Course

**Method:** `PUT`

**Path:** `/api/courses/{id}`

**Authentication:** Required

Updates an existing course belonging to the authenticated student.

### Example request

```http
PUT /api/courses/1
Authorization: Bearer YOUR_TOKEN
```
---
### Request body

```json
{
  "course_name": "Introduction to Computer Science II",
  "course_code": "CSC102"
}
```

### Successful response

**Status:** `200 OK`

```json
{
  "message": "Course updated successfully"
}
```
---
### Possible errors

- `400 Bad Request` — Missing or invalid information.
- `401 Unauthorized` — Missing, invalid, or expired token.
- `404 Not Found` — Course does not exist or does not belong to the student.

---

# 10. Delete a Course

**Method:** `DELETE`

**Path:** `/api/courses/{id}`

**Authentication:** Required

Deletes a course belonging to the authenticated student.

### Example request

```http
DELETE /api/courses/1
Authorization: Bearer YOUR_TOKEN
```
---
### Successful response

**Status:** `200 OK`

```json
{
  "message": "Course deleted successfully"
}
```
Associated assignments are also deleted through the database's foreign-key cascade.
---


### Possible errors

- `401 Unauthorized` — Missing, invalid, or expired token.
- `404 Not Found` — Course does not exist or does not belong to the student.

---

# 11. Retrieve All Assignments

**Method:** `GET`

**Path:** `/api/assignments`

**Authentication:** Required

Retrieves assignments associated with the authenticated student's courses.

### Example request

```http
GET /api/assignments
Authorization: Bearer YOUR_TOKEN
```
---
### Successful response

**Status:** `200 OK`

```json
{
  "assignments": [
    {
      "id": 1,
      "course_id": 1,
      "title": "Programming Assignment 1",
      "description": "Introduction to PHP",
      "due_date": "2026-12-15",
      "grade": "85.00"
    }
  ]
}
```

If there are no assignments, the response contains an empty assignments array.
---
### Possible errors

- `401 Unauthorized` — Missing, invalid, or expired token.

---

# 12. Retrieve One Assignment

**Method:** `GET`

**Path:** `/api/assignments/{id}`

**Authentication:** Required

Retrieves one assignment using its ID.

### Example request

```http
GET /api/assignments/1
Authorization: Bearer YOUR_TOKEN
```
---
### Successful response

**Status:** `200 OK`

```json
{
  "assignment": {
    "id": 1,
    "course_id": 1,
    "title": "Programming Assignment 1",
    "description": "Introduction to PHP",
    "due_date": "2026-12-15",
    "grade": "85.00"
  }
}
```

### Possible errors

- `401 Unauthorized` — Missing, invalid, or expired token.
- `404 Not Found` — Assignment does not exist or is not accessible to the student.

---

# 13. Create an Assignment

**Method:** `POST`

**Path:** `/api/assignments`

**Authentication:** Required

Creates an assignment associated with one of the authenticated student's courses.

### Request body

```json
{
  "course_id": 1,
  "title": "Programming Assignment 1",
  "description": "Introduction to PHP",
  "due_date": "2026-12-15",
  "grade": 85
}
```

The grade can be recorded when the assignment is created. It may also be updated later.

### Successful response

**Status:** `201 Created`

```json
{
  "message": "Assignment created successfully",
  "assignment_id": 1
}
```

### Possible errors

- `400 Bad Request` — Missing or invalid assignment information.
- `401 Unauthorized` — Missing, invalid, or expired token.
- `404 Not Found` — The specified course does not exist or does not belong to the student.

---

# 14. Update an Assignment

**Method:** `PUT`

**Path:** `/api/assignments/{id}`

**Authentication:** Required

Updates an assignment belonging to one of the authenticated student's courses.

### Example request

```http
PUT /api/assignments/1
Authorization: Bearer YOUR_TOKEN
```
---
### Request body

```json
{
  "title": "Programming Assignment 1 - Updated",
  "description": "Updated PHP assignment",
  "due_date": "2026-12-20",
  "grade": 92
}
```

### Successful response

**Status:** `200 OK`

```json
{
  "message": "Assignment updated successfully"
}
```

This endpoint allows students to update assignment information and record or change their personal grades.

### Possible errors

- `400 Bad Request` — Missing or invalid assignment information.
- `401 Unauthorized` — Missing, invalid, or expired token.
- `404 Not Found` — Assignment does not exist or is not accessible to the student.

---

# 15. Delete an Assignment

**Method:** `DELETE`

**Path:** `/api/assignments/{id}`

**Authentication:** Required

Deletes an assignment belonging to the authenticated student's course.

### Example request

```http
DELETE /api/assignments/1
Authorization: Bearer YOUR_TOKEN
```
---
### Successful response

**Status:** `200 OK`

```json
{
  "message": "Assignment deleted successfully"
}
```

### Possible errors

- `401 Unauthorized` — Missing, invalid, or expired token.
- `404 Not Found` — Assignment does not exist or is not accessible to the student.

---

# 16. HTTP Status Codes

The API uses standard HTTP status codes to communicate the result of a request.

| Status | Meaning |
|---|---|
| `200 OK` | Request completed successfully |
| `201 Created` | New resource created successfully |
| `400 Bad Request` | Invalid or missing request information |
| `401 Unauthorized` | Authentication required or failed |
| `404 Not Found` | Requested resource not found or not accessible |
| `409 Conflict` | Resource conflicts with existing data |

---

# 17. Successful and Failed API Examples

This section demonstrates successful and unsuccessful API requests and their HTTP responses.

## Example 1: Successful Login
**Request:**

```http
POST /api/login
Content-Type: application/json
```
```json
{
  "email": "alex@example.com",
  "password": "Student123!"
}
```
Response: `200 OK`

---

```json
{
  "message": "Login successful",
  "token_type": "Bearer",
  "token": "YOUR_GENERATED_TOKEN",
  "expires_at": "TOKEN_EXPIRATION_DATE"
}
```

## Example 2: Failed Login

**Request:**

```http
POST /api/login
Content-Type: application/json
```

```json
{
  "email": "alex@example.com",
  "password": "WrongPassword"
}
```
---
**Response: `401 Unauthorized`**

```json
{
  "error": "Invalid email or password"
}
```
---
## Example 3: Missing Authentication Token

**Request:**

```http
GET /api/courses
```
Response: `401 Unauthorized`
---
```json
{
  "error": "Missing or invalid bearer token"
}
```
---
## Example 4: Course Not Found

**Request:**

```http
GET /api/courses/99999
Authorization: Bearer YOUR_TOKEN
```
**Response: `404 Not Found`**

```json
{
  "error": "Course not found"
}
```
---


# 18. Browser-Based API Testing

The project includes an HTML and JavaScript test page:

`http://localhost:8080/test.html`

The test page provides controls for all 12 API operations.

Users can:

- Register a student.
- Log in and obtain a bearer token.
- Create, retrieve, update, and delete courses.
- Create, retrieve, update, and delete assignments.
- Record and update assignment grades.
- View HTTP status codes and JSON responses.


---

# 19. Automated Acceptance Tests

The automated acceptance test file is:

`tests/acceptance.php`

Run the tests using:

```bash
docker compose exec php php tests/acceptance.php
```
---
The acceptance tests verify:
1. Student registration.
2. Student login.
3. Course creation.
4. Course retrieval.
5. Course updating.
6. Course deletion.
7. Assignment creation.
8. Assignment retrieval.
---
9. Assignment updating.
10. Assignment deletion.
11. Missing bearer-token rejection.
12. Invalid bearer-token rejection.
13. Expired bearer-token rejection.
14. Nonexistent course and assignment responses.
---
The tests also check that updated course and assignment information is saved correctly.

### Saved test results

The test results are documented in:

`tests/stage1-test-results.md`

**Recorded acceptance test result: 35 passed, 0 failed.**

---

# 20. Database Structure

The project uses MySQL with four database tables.

| Table | Purpose |
|---|---|
| `users` | Stores student account information |
| `courses` | Stores courses associated with students |
| `assignments` | Stores assignments and grades |
| `api_tokens` | Stores hashed authentication tokens and expiration dates |

---
The database schema is documented in:

database/schema.sql

The PHP database connection is configured in:

config/database.php

The database uses foreign keys to maintain relationships between students, courses, and assignments.

---

# 21. Security Features

The API includes several security measures:

- Passwords are stored using PHP password hashing.
- Database queries use prepared statements.
- Login generates a secure random bearer token.
- Only a hash of the token is stored in the database.
- Tokens expire after 24 hours.
- Protected endpoints reject missing, invalid, or expired tokens.
- Students can only access their own courses and associated assignments.
- Unauthorized access to another student's resources returns `404 Not Found`.

---
# 22. Completed Project Summary

The Student Course Management API now supports:

- Student registration and login.
- Bearer-token authentication.
- Full course CRUD operations.
- Full assignment CRUD operations.
- Assignment grade recording and updating.
- MySQL database integration.
- NGINX and PHP deployment through Docker.
- Browser-based testing.
- Automated acceptance testing.
**All 12 approved API operations have been implemented.**

---

# 23. Conclusion

The project provides a REST API that allows students to manage their courses, assignments, deadlines, and personal grades.

The implementation uses PHP, NGINX, MySQL, and Docker, with authentication and ownership checks to protect student data.

The automated acceptance tests and browser-based testing page provide ways to verify API functionality.

