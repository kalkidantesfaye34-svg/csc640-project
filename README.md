# CSC640 Student Course Management REST API

## Project Overview

The Student Course Management REST API is a PHP and MySQL-based application developed for CSC640. It provides REST API functionality for student registration, authentication, course management, and assignment management.

The project uses PHP, MySQL, NGINX, and Docker Compose to provide a containerized API environment.

Stage 1 has been completed. Stage 2 will extend the project using Laravel, Eloquent ORM, database migrations, Docker, automation and DevOps practices, Hugo, and GitHub.io deployment.

## Project Technologies

- PHP 8.3
- MySQL 8.0
- NGINX
- Docker
- Docker Compose
- HTML and JavaScript
- Laravel (Stage 2)
- Eloquent ORM (Stage 2)
- Hugo (Stage 2)
- GitHub and GitHub.io (Stage 2)

## Project Features — Stage 1

### Student Authentication

- Student registration
- Student login
- Password hashing and verification
- Bearer-token authentication
- Token expiration and validation

### Course Management

- Retrieve all courses
- Retrieve a course by ID
- Create a course
- Update a course
- Delete a course

### Assignment Management

- Retrieve all assignments
- Retrieve an assignment by ID
- Create an assignment
- Update an assignment
- Delete an assignment

### Security

- Passwords are stored using secure password hashing.
- Protected API endpoints require bearer-token authentication.
- Missing and invalid tokens are rejected.
- Expired tokens are rejected.
- Resource ownership is checked where applicable.

## API Endpoints

| Method | Endpoint | Description |
|---|---|---|
| POST | `/api/register` | Register a student |
| POST | `/api/login` | Log in a student |
| GET | `/api/courses` | Retrieve courses |
| GET | `/api/courses/{id}` | Retrieve a course |
| POST | `/api/courses` | Create a course |
| PUT | `/api/courses/{id}` | Update a course |
| DELETE | `/api/courses/{id}` | Delete a course |
| GET | `/api/assignments` | Retrieve assignments |
| GET | `/api/assignments/{id}` | Retrieve an assignment |
| POST | `/api/assignments` | Create an assignment |
| PUT | `/api/assignments/{id}` | Update an assignment |
| DELETE | `/api/assignments/{id}` | Delete an assignment |

## Project Architecture

The Stage 1 application uses:

- **PHP:** Handles API requests and application logic.
- **MySQL:** Stores student, course, assignment, and authentication data.
- **NGINX:** Serves API requests.
- **Docker Compose:** Runs the PHP, MySQL, and NGINX services.

### Database

The database is named `student_management`.

Main database tables include:

- `users`
- `courses`
- `assignments`
- `api_tokens`

The database schema is available in the project database files.

## Repository Structure

The repository includes the following organization:

```text
csc640-project/
│
├── code/
│   ├── src/
│   ├── config/
│   ├── database/
│   ├── nginx/
│   ├── Dockerfile
│   ├── docker-compose.yml
│   └── .dockerignore
│
├── presentation/
│   ├── stage1-api-tutorial.md
│   └── Stage1-api-tutorial.pdf
│
├── plan/
│   ├── Master_Project_Plan.md
│   ├── HW4_plan_stage1.md
│   ├── HW4_plan_stage2.md
│   └── stage1_evaluation.md
│
├── tests/
│   ├── acceptance.php
│   └── stage1-test-results.md
│
├── acceptance.md
├── README.md
└── .gitignore
```

Note: The `code/` folder is reserved for the organized project code and supporting files. The existing root-level Docker configuration and application folders are retained for the current working Docker setup.

## Installation and Setup

### Requirements

Install the following tools:

- Docker Desktop
- Git
- Visual Studio Code (recommended)

### Clone the Repository

```bash
git clone https://github.com/kalkidantesfaye34-svg/csc640-project.git

cd csc640-project
```

### Build and Start the Application

From the project root directory, run:

```bash
docker compose up -d --build
```

This starts the PHP, MySQL, and NGINX containers.

### Access the Application

Open the following address in your browser:

```text
http://localhost:8080/test.html
```

This opens the HTML/JavaScript API testing page.

### Stop the Application

```bash
docker compose down
```

## Testing

Run the automated acceptance tests using:

```bash
docker compose exec php php ../tests/acceptance.php
```

The Stage 1 acceptance test results reported:

- **35 checks passed**
- **0 checks failed**

Tests covered successful API requests, missing tokens, invalid tokens, expired tokens, and nonexistent resources.

The browser-based test page can also be used to test API functionality.

## Documentation

The project documentation includes:

- API tutorial in Markdown
- API tutorial in PDF
- Acceptance criteria
- Automated acceptance test results
- Master project plan
- Stage 1 evaluation
- Stage 2 development plan

See the `presentation/`, `plan/`, and `tests/` folders.

## Project Milestones

### Stage 1 — Completed

- Set up PHP, MySQL, NGINX, and Docker Compose.
- Implemented student registration and login.
- Implemented bearer-token authentication.
- Implemented course management.
- Implemented assignment management.
- Created the database schema.
- Created the browser-based API testing page.
- Prepared automated acceptance tests.
- Created API tutorial documentation.

### Stage 2 — Planned

Stage 2 will focus on:

1. Laravel framework and MVC architecture.
2. Laravel routes and controllers.
3. Models and Eloquent ORM.
4. Database migrations.
5. Docker containerization with PHP and NGINX.
6. Automation and DevOps practices.
7. Hugo static documentation website.
8. GitHub.io deployment.

## Project Repositories

- **Source Code:** https://github.com/kalkidantesfaye34-svg/csc640-project
- **Documentation:** https://github.com/kalkidantesfaye34-svg/csc640-documentation

## References and Acknowledgments

### ASE230 Course Lecture References

The project uses concepts and learning materials from ASE230 course documentation, including:

- Module 1 — First PHP Server.
- Module 1 — Connect PHP with MySQL.
- Module 1 — Request and Response with PHP.
- Module 1 — Webserver Using NGINX.
- Module 1 — REST API.
- Module 1 — Security.

### AI Assistance

I used ChatGPT (OpenAI) for assistance with portions of the PHP code, code examples, debugging, and understanding implementation concepts. I reviewed, modified, and tested the code integrated into my project and can explain its functionality and test results.

## Current Project Status

- **Stage 1:** Completed.
- **Stage 2:** Planned / Not yet completed.

The project will continue with Laravel, ORM, Docker, automation and DevOps, Hugo, and GitHub.io deployment.