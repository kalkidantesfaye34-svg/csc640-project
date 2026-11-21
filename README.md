# Student Course Management REST API

A REST API project developed for CSC 640 using PHP, NGINX, MySQL, and Docker. The project demonstrates REST API development, database integration, authentication, CRUD operations, and software engineering practices.

## Project Information

- **Course project option:** Option 2
- **Backend:** PHP
- **Web server:** NGINX
- **Database:** MySQL
- **Containerization:** Docker
- **Repository:** [CSC640 Project](https://github.com/kalkidantesfaye34-svg/csc640-project)
- **Canvas progress page:** [Individual Project Progress Page](https://nku.instructure.com/courses/92884/pages/individual-project-progress-page-of-kalkidan-alemu-in-csc640)

## Problem

Students can have difficulty keeping track of courses, assignments, deadlines, and grades while managing multiple classes. This project provides an API that helps students organize their academic information.

## Solution

The Student Course Management REST API allows students to register and log in, manage their courses and assignments, track deadlines, and record assignment grades.

The application uses:

- **NGINX** to receive HTTP requests and forward PHP requests.
- **PHP** to process API requests and implement application logic.
- **MySQL** to store student accounts, courses, assignments, and authentication tokens.
- **Docker** to run the application services.

## Project Progress

### Stage 1 — Foundation and Read-Only API

Stage 1 established the application environment, database, authentication system, and initial course retrieval functionality.

| Feature | Status |
|---|---|
| Docker, PHP, and NGINX environment | Completed and tested |
| MySQL database and four tables | Created |
| Student registration | Implemented and tested |
| Student login | Implemented and tested |
| Password hashing | Implemented |
| Bearer-token generation | Implemented |
| Bearer-token verification | Implemented and tested |
| Authenticated course retrieval | Implemented and tested |
| Automated acceptance tests | 35/35 passed |
| HTML/JavaScript test page | Implemented and browser-tested |
| API tutorial in Markdown and PDF | Completed |
| Stage 1 test results | Documented |

### Stage 1 API Routes

| Method | Endpoint | Authentication | Status |
|---|---|---|---|
| POST | `/api/register` | Public | Tested |
| POST | `/api/login` | Public | Tested |
| GET | `/api/courses` | Bearer token | Tested |
| GET | `/api/courses/{id}` | Bearer token | Tested |

### Stage 2 — Full Course and Assignment Management

Stage 2 extends the API with complete course and assignment management, including grade recording.

All eight additional endpoints have been implemented and tested.

| Method | Endpoint | Authentication | Status |
|---|---|---|---|
| POST | `/api/courses` | Bearer token | Tested |
| PUT | `/api/courses/{id}` | Bearer token | Tested |
| DELETE | `/api/courses/{id}` | Bearer token | Tested |
| GET | `/api/assignments` | Bearer token | Tested |
| GET | `/api/assignments/{id}` | Bearer token | Tested |
| POST | `/api/assignments` | Bearer token | Tested |
| PUT | `/api/assignments/{id}` | Bearer token | Tested |
| DELETE | `/api/assignments/{id}` | Bearer token | Tested |

Grade recording and updating are supported through the assignment creation and update endpoints.

## Database Structure

The MySQL database is named `student_management` and contains four tables:

- **users:** Stores student names, unique email addresses, and password hashes.
- **courses:** Stores courses associated with their student owners.
- **assignments:** Stores assignment titles, descriptions, due dates, and grades. Each assignment belongs to a course.
- **api_tokens:** Stores hashed authentication tokens and their expiration dates.

The database schema is located in `database/schema.sql`.

## Environment and Startup

Make sure Docker Desktop is running. From the project root, execute:

```bash
# Check Docker and Docker Compose
docker --version
docker compose version

# Validate the Compose configuration
docker compose config

# Build and start the application
docker compose up -d --build

# Check running services
docker compose ps

# View service logs
docker compose logs --tail=100
```

Open the browser-based API test page:

[http://localhost:8080/test.html](http://localhost:8080/test.html)

NGINX receives requests on port `8080` and forwards PHP requests to the PHP service.

To stop the services:

```bash
docker compose down
```

Avoid using `docker compose down -v` unless you intentionally want to delete the database volume and its stored data.

## Authentication and Security

The API uses bearer-token authentication for protected operations.

- Registration stores a password hash instead of a plaintext password.
- Login verifies the submitted password and generates a bearer token.
- Tokens are generated using secure random data.
- The database stores a hash of each token rather than the original token.
- Tokens have an expiration time.
- Protected endpoints reject missing, invalid, or expired tokens.
- Students can only access or modify records they own.
- Database queries use prepared statements.

Protected requests use the following HTTP header:

```http
Authorization: Bearer YOUR_TOKEN
```

## Testing

The project includes an automated PHP acceptance test script and an HTML/JavaScript browser-based test page.

### Automated Acceptance Tests

The acceptance test script is located at:

`tests/acceptance.php`

Run the tests inside the Docker PHP container:

```bash
docker compose exec php php tests/acceptance.php
```

**Recorded result: 35/35 tests passed.**

The automated tests cover:

- Student registration and login
- All 12 API endpoint operations
- Course creation, retrieval, updating, and deletion
- Assignment creation, retrieval, updating, and deletion
- Assignment grade recording and updating
- Missing, invalid, and expired bearer-token rejection
- Handling nonexistent courses and assignments

### Browser-Based Tests

The HTML/JavaScript test page is located at:

`src/test.html`

Open it at:

`http://localhost:8080/test.html`

The page allows users to test registration, login, course management, assignment management, and grade updates. It displays HTTP status codes and JSON API responses.

All 12 API operations were successfully tested through the browser.

### Test Results Documentation

Detailed test results are saved in:

`tests/stage1-test-results.md`

Endpoint acceptance criteria and testing procedures are documented in:

`acceptance.md`

## API Tutorial

The project includes an API tutorial in Markdown and PDF formats.

- `presentation/stage1-api-tutorial.md`
- `presentation/Stage1-api-tutorial.pdf`

The tutorial explains the application environment, API endpoints, authentication, request methods, and testing procedures.

## Repository Structure

```text
csc640-project/
├── config/
│   └── database.php
├── database/
│   └── schema.sql
├── nginx/
│   └── default.conf
├── src/
│   ├── index.php
│   └── test.html
├── presentation/
│   ├── stage1-api-tutorial.md
│   └── Stage1-api-tutorial.pdf
├── tests/
│   ├── acceptance.php
│   └── stage1-test-results.md
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── README.md
└── acceptance.md
```

## Project Status

The PHP, NGINX, and MySQL application environment is operational. All 12 API operations have been implemented and tested. The automated acceptance test suite passed all 35 tests, and browser-based testing successfully verified all 12 API operations.

The project includes database schema documentation, automated tests, saved test results, and an API tutorial in Markdown and PDF formats.