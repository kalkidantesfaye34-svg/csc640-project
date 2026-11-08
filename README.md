# csc640-project
A REST API project developed for CSC 640 using PHP and MySQL, demonstrating API development, authentication, and software engineering practices.
# Student Course Management REST API

A Stage 1 PHP REST API project for managing student courses and assignments.

## Project information

- **Course project option:** Option 2
- **Backend:** PHP
- **Web server:** NGINX
- **Database:** MySQL
- **Repository:** https://github.com/kalkidantesfaye34-svg/csc640-project
- **Canvas progress page:** https://nku.instructure.com/courses/92884/pages/individual-project-progress-page-of-kalkidan-alemu-in-csc640

## Problem

Students can have difficulty keeping track of courses, assignments, deadlines, and grades while managing multiple classes. This project aims to provide an API for organizing academic information.

## Solution

The Student Course Management REST API provides endpoints to register and log in students, view courses and assignments, and create, update, or delete course and assignment records. PHP handles API logic, NGINX receives HTTP requests and passes PHP requests to the application, and MySQL stores persistent data.

## Stage 1 API routes

| Method | Endpoint | Authentication |
|---|---|---|
| POST | `/api/register` | Public |
| POST | `/api/login` | Public |
| GET | `/api/courses` | Public |
| GET | `/api/courses/{id}` | Public |
| POST | `/api/courses` | Bearer token |
| PUT | `/api/courses/{id}` | Bearer token |
| DELETE | `/api/courses/{id}` | Bearer token |
| GET | `/api/assignments` | Public |
| GET | `/api/assignments/{id}` | Public |
| POST | `/api/assignments` | Bearer token |
| PUT | `/api/assignments/{id}` | Bearer token |
| DELETE | `/api/assignments/{id}` | Bearer token |




## Environment and startup

The exact commands depend on the `docker-compose.yml` and service names in this repository. From the project root:

```bash
# Check Docker and Compose
docker --version
docker compose version

# Validate the Compose configuration
docker compose config

# Build and start the services
docker compose up -d --build

# Check running services
docker compose ps

# Inspect logs (replace service names if the Compose file uses different names)
docker compose logs --tail=100
```

Open the local URL and port configured for the NGINX service. Do not assume the port is `80`; check the `ports` section of `docker-compose.yml`.

To stop the services:

```bash
docker compose down
```

Do not use `docker compose down -v` unless you intentionally want to delete the named database volume and its stored data.



The Stage 1 database is expected to include:
- students/users, with unique email and password hash;
- courses, associated with the student who owns them;
- assignments, associated with a course and student;
- token data or another documented token-validation mechanism, including expiration.

The final schema and environment variable names should match the actual implementation.

## Authentication

- Registration stores a password hash, not a plaintext password.
- Login verifies the password and returns a bearer token with an expiration.
- Course and assignment create/update/delete endpoints require `Authorization: Bearer <token>`.
- Tokens must be checked for validity and expiration.
- Protected operations must enforce ownership.
- Use prepared statements for database access.
- Never expose passwords, token secrets, SQL errors, or stack traces in API responses.

## Testing

See [`acceptance.md`](acceptance.md) for endpoint-by-endpoint success and failure cases, including token tests.

At minimum, verify:
1. Docker Compose configuration is valid.
2. NGINX, PHP, and MySQL services start.
3. Requests sent to NGINX reach PHP.
4. PHP connects to MySQL.
5. Registration and login work.
6. Course and assignment CRUD operations persist in MySQL.
7. Missing, invalid, and expired tokens are rejected on protected routes.
8. A student cannot modify another student's records.
