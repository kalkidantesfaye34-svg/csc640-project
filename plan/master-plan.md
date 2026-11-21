# Student Course Management Project – Master Project Plan

## 1. Project Overview

The goal of this project is to develop a Student Course Management REST API. The project is divided into two stages.

- **Stage 1:** Develop a REST API using PHP, MySQL, and NGINX, with authentication and course and assignment management.
- **Stage 2:** Reimplement the Stage 1 API using Laravel, Eloquent ORM, and Laravel migrations. Add automation, Docker deployment, and a Hugo documentation website published through GitHub Pages.

Both implementations will be preserved in the GitHub repository for comparison and evaluation.

## 2. Stage 1 – PHP REST API (Completed)

### Milestone 1 – Project Environment and Authentication

**Planned Date:** November 17, 2026  
**Actual Date:** November 15, 2026  
**Status:** Completed

- Established the PHP, MySQL, NGINX, and Docker environment.
- Implemented student registration and login.
- Implemented password hashing and Bearer token generation.

### Milestone 2 – Authenticated API, Testing, and Documentation

**Planned Date:** November 20, 2026  
**Actual Date:** November 20, 2026  
**Status:** Completed

- Implemented authenticated course retrieval and course management operations.
- Implemented assignment management operations.
- Created the MySQL database schema.
- Created the HTML/JavaScript API test page.
- Created and executed acceptance tests.
- Prepared the Stage 1 API tutorial in Markdown and PDF.
- Updated the project README and documentation.

## 3. Stage 2 – Laravel, ORM, DevOps, and GitHub Pages

### Milestone 3 – Laravel REST API Implementation

**Planned Date:** November 30, 2026  
**Actual Date:** TBD  
**Status:** Planned

- Reimplement the Stage 1 API using Laravel routes and controllers.
- Create Eloquent models and appropriate relationships.
- Use Laravel migrations to manage the database schema.
- Implement Bearer authentication using Sanctum or an equivalent solution.
- Run the same Stage 1 acceptance tests against Laravel.
- Compare Stage 1 PHP and Stage 2 Laravel test results.
- Create and test `run.sh` to start Laravel and run acceptance tests.

### Milestone 4 – Automation, Docker, and Documentation Publication

**Planned Date:** December 8, 2026  
**Actual Date:** TBD  
**Status:** Planned

- Adapt a shell script and explain its purpose with references.
- Configure Docker and Docker Compose for Laravel and its required services.
- Create and test `setup.sh` to build and start the Docker environment and run acceptance tests.
- Adapt the Stage 1 API tutorial into a Hugo documentation website.
- Create `comparison.md` explaining automation benefits, costs, remaining manual work, and links to test results.
- Configure GitHub Actions to build and publish the Hugo website to GitHub Pages whenever changes are pushed.

## 4. Project Deliverables

### Stage 1 Deliverables

- PHP REST API source code.
- MySQL database schema and configuration.
- Student registration and login.
- Bearer token authentication.
- Course and assignment management operations.
- HTML/JavaScript test page.
- Acceptance tests and test results.
- Stage 1 API tutorial in Markdown and PDF.
- Project README.

### Stage 2 Deliverables

- Laravel REST API implementation.
- Laravel routes, controllers, Eloquent models, and relationships.
- Laravel database migrations.
- Bearer authentication.
- Acceptance test results for both Stage 1 and Stage 2.
- `run.sh` and adapted shell script.
- Docker and Docker Compose configuration.
- `setup.sh`.
- Hugo documentation website.
- `comparison.md`.
- GitHub Actions workflow for GitHub Pages publication.

## 5. Repository Organization

The project repository will organize the work into these main folders:

- `code/` – Application code, scripts, Docker configuration, and supporting files.
- `presentation/` – API tutorials, documentation source files, and comparison documentation.
- `plan/` – Master project plan, Stage 1 plan, Stage 2 plan, and evaluations.
- `tests/` – Acceptance tests and saved test results.

The root README will explain how to set up, run, and test the project.

## 6. Project Status

- **Stage 1:** Completed.
- **Stage 2:** Planned and in progress after Stage 1 submission.
- **Final goal:** Preserve both runnable implementations, demonstrate their test results, and publish the project documentation through GitHub Pages.

## 7. Plan Maintenance

The master project plan will be updated throughout the project. Actual completion dates will be recorded after milestones are completed. Any schedule changes or delays will be explained in the plan.