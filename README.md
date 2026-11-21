# CSC640 Student Course Management REST API

## Project Information

**Course:** CSC640  
**Project Option:** Option 2 – Student Course Management REST API  
**Repository:** [CSC640 Project](https://github.com/kalkidantesfaye34-svg/csc640-project)  
**Canvas progress page:** [Individual Project Progress Page](https://nku.instructure.com/courses/92884/pages/individual-project-progress-page-of-kalkidan-alemu-in-csc640)

**Submitted version:** Completed Stage 1 of the CSC640 Student Course Management REST API project.

## Project Overview

The Student Course Management REST API allows students to register, log in, manage courses, manage assignments, and track assignment grades.

The project uses PHP, MySQL, NGINX, and Docker to provide a REST API with token-based authentication.

## Technology Stack

- PHP 8.3
- MySQL 8.0
- NGINX
- Docker and Docker Compose
- HTML and JavaScript for browser-based API testing
- REST API with JSON requests and responses

## Stage 1 Implementation

Stage 1 includes the implementation of all 12 approved API operations, including authentication, course management, assignment management, and grade tracking.

### Authentication

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Register a student |
| POST | `/api/login` | Log in and receive an access token |

### Course Management

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/courses` | Retrieve all courses |
| GET | `/api/courses/{id}` | Retrieve one course |
| POST | `/api/courses` | Create a course |
| PUT | `/api/courses/{id}` | Update a course |
| DELETE | `/api/courses/{id}` | Delete a course |

### Assignment and Grade Management

| Method | Endpoint | Description |
|---|---|---|
| GET | `/api/assignments` | Retrieve all assignments |
| GET | `/api/assignments/{id}` | Retrieve one assignment |
| POST | `/api/assignments` | Create an assignment |
| PUT | `/api/assignments/{id}` | Update an assignment and its grade |
| DELETE | `/api/assignments/{id}` | Delete an assignment |

## Database Structure

The project uses MySQL with four tables:

- `users` – Stores student account information.
- `courses` – Stores course information.
- `assignments` – Stores assignment information and grades.
- `api_tokens` – Stores hashed authentication tokens and expiration information.

The database schema is located at:

`database/schema.sql`

The database connection settings are located at:

`config/database.php`

## Project Setup and Execution

### Requirements

Install the following:

- Docker Desktop
- Docker Compose
- Git

### Clone the Repository

```bash
git clone https://github.com/kalkidantesfaye34-svg/csc640-project.git
cd csc640-project
```

### Verify Docker

```bash
docker --version
docker compose version
docker compose config
```

### Build and Start the Containers

```bash
docker compose up -d --build
```

### Check Running Containers

```bash
docker compose ps
```

The API runs through NGINX on port `8080`.

### Access the Browser-Based Test Page

Open:

[http://localhost:8080/test.html](http://localhost:8080/test.html)

### View Container Logs

```bash
docker compose logs
```

### Stop the Containers

```bash
docker compose down
```

**Important:** Do not use `docker compose down -v` unless you intend to delete the database volume and its stored data.

## Authentication and Security

The API uses bearer-token authentication to protect course and assignment endpoints.

Security features include:

- Passwords are stored using PHP's `password_hash()`.
- Password verification uses `password_verify()`.
- Login generates a random access token.
- The database stores a SHA-256 hash of each token rather than the original token.
- Tokens expire after 24 hours.
- Protected endpoints reject missing, invalid, and expired tokens.
- Students can only access their own courses and assignments.

Authentication endpoints are public. Course and assignment endpoints require a valid bearer token.

## Testing

The project includes automated acceptance tests and a browser-based testing page.

### Run Automated Acceptance Tests

Start the Docker containers, then run:

```bash
docker compose exec php php tests/acceptance.php
```

### Acceptance Test Results

**35/35 acceptance checks passed.**

The 35 checks cover:

- Successful authentication and API operations.
- Missing bearer tokens.
- Invalid bearer tokens.
- Expired bearer tokens.
- Requests for nonexistent courses and assignments.

The 35 checks represent automated acceptance tests, not 35 separate API endpoints. The API contains 12 operations.

Test results are documented in:

`tests/stage1-test-results.md`

Acceptance criteria are documented in:

`acceptance.md`

### Browser-Based API Testing

The browser-based test page is located at:

`src/test.html`

It provides controls for testing all 12 API operations and displays HTTP status codes and JSON responses.

The page can be accessed at:

[http://localhost:8080/test.html](http://localhost:8080/test.html)

## API Tutorial

The tutorial explains all 12 API operations, including HTTP methods, endpoint paths, request inputs, responses, authentication requirements, and examples of successful and failed requests.

- [View the API Tutorial (Markdown)](presentation/stage1-api-tutorial.md)
- [View the API Tutorial (PDF)](presentation/Stage1-api-tutorial.pdf)

## Project Structure

```text
csc640-project/
├── config/
│   └── database.php
├── database/
│   └── schema.sql
├── nginx/
│   └── default.conf
├── presentation/
│   ├── stage1-api-tutorial.md
│   └── Stage1-api-tutorial.pdf
├── src/
│   ├── index.php
│   └── test.html
├── tests/
│   ├── acceptance.php
│   └── stage1-test-results.md
├── .dockerignore
├── Dockerfile
├── docker-compose.yml
├── README.md
└── acceptance.md
```

## Project Status

Stage 1 of the Student Course Management REST API project is complete.

All 12 approved API operations have been implemented and tested. Authentication, course management, assignment management, and grade tracking are included.

The automated acceptance test suite reports 35 passed checks and 0 failed checks.

The project includes Docker-based setup instructions, a MySQL database schema, a browser-based API testing page, automated acceptance tests, and a Marp tutorial in Markdown and PDF formats.

Stage 2 has not yet been completed.

## Conclusion

This project demonstrates the development of a REST API using PHP, MySQL, NGINX, and Docker. It includes token-based authentication, protected endpoints, database operations, automated testing, and documentation.