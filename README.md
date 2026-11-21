# Student Course Management REST API

A REST API project developed for CSC 640 using PHP, NGINX, MySQL, and Docker. The project demonstrates REST API development, database integration, authentication, and software engineering practices.

## Project Information

- **Course project option:** Option 2
- **Backend:** PHP
- **Web server:** NGINX
- **Database:** MySQL
- **Containerization:** Docker
- **Repository:** [CSC640 Project](https://github.com/kalkidantesfaye34-svg/csc640-project)
- **Canvas progress page:** [Individual Project Progress Page](https://nku.instructure.com/courses/92884/pages/individual-project-progress-page-of-kalkidan-alemu-in-csc640)

## Problem

Students can have difficulty keeping track of courses, assignments, deadlines, and grades while managing multiple classes. This project aims to provide an API that helps students organize their academic information.

## Solution

The Student Course Management REST API allows students to register and log in, retrieve their courses, and eventually manage courses, assignments, deadlines, and grades.

The application uses:

- **NGINX** to receive HTTP requests and forward PHP requests.
- **PHP** to process API requests and implement application logic.
- **MySQL** to store student accounts, courses, assignments, and authentication tokens.
- **Docker** to run the application services.

## Project Progress

### Stage 1 — Foundation and Read-Only API

Stage 1 establishes the application environment, database, and authentication system.

| Feature | Current status |
|---|---|
| Docker, PHP, and NGINX environment | Set up |
| MySQL database and four tables | Created |
| Student registration | Tested successfully |
| Student login | Tested successfully |
| Password hashing | Implemented |
| Bearer-token generation | Implemented |
| Bearer-token verification | Requires testing |
| Authenticated course retrieval | Requires testing |
| Acceptance tests and saved results | In progress |
| HTML/JavaScript test page | Planned |
| API tutorial and Stage 1 evaluation | Planned |

### Stage 1 API Routes

| Method | Endpoint | Authentication | Status |
|---|---|---|---|
| POST | `/api/register` | Public | Implemented and tested |
| POST | `/api/login` | Public | Implemented and tested |
| GET | `/api/courses` | Bearer token | Requires testing |
| GET | `/api/courses/{id}` | Bearer token | Requires testing |

### Stage 2 — Full Course and Assignment Management

Stage 2 will extend the API with complete course and assignment management.

| Method | Endpoint | Authentication |
|---|---|---|
| POST | `/api/courses` | Bearer token |
| PUT | `/api/courses/{id}` | Bearer token |
| DELETE | `/api/courses/{id}` | Bearer token |
| GET | `/api/assignments` | Bearer token |
| GET | `/api/assignments/{id}` | Bearer token |
| POST | `/api/assignments` | Bearer token |
| PUT | `/api/assignments/{id}` | Bearer token |
| DELETE | `/api/assignments/{id}` | Bearer token |

Stage 2 will also include grade recording and updating, input validation, and ownership checks.

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

Open the API in your browser:

[http://localhost:8080](http://localhost:8080)

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
- Protected endpoints must reject missing, invalid, or expired tokens.
- Students should only access or modify records they own.
- Database queries should use prepared statements.
- Passwords, tokens, and sensitive database errors should not be exposed in API responses.

Protected requests use the following HTTP header:

```http
Authorization: Bearer YOUR_TOKEN
```

## Testing

See `acceptance.md` for endpoint acceptance criteria and testing procedures.

Testing will cover:

1. Docker Compose configuration and service startup.
2. NGINX forwarding requests to PHP.
3. PHP database connectivity.
4. Student registration and login.
5. Bearer-token generation and verification.
6. Successful and unsuccessful course retrieval.
7. Rejection of missing, invalid, and expired tokens.
8. Database persistence and CRUD operations.
9. Student ownership restrictions.
10. Assignment management and grade recording.

Actual test results will be documented as the implementation progresses.

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
│   └── index.php
├── Dockerfile
├── docker-compose.yml
├── .dockerignore
├── README.md
└── acceptance.md
```

Additional testing files, documentation, and milestone plans will be added as the project progresses.