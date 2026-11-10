
# Student Course Management API

## Project Overview

The Student Course Management API is a REST API designed to help students organize their academic information.

The system allows students to manage courses, assignments, deadlines, and grades.

## Acceptance Criteria

This document describes the expected success and failure results for the API endpoints.

## 1. Authentication

### POST /api/register

**Success Case:**
- Input: Valid name, email, and password.
- Expected Status: 201 Created.
- Expected Result: Student account is created.

**Failure Case:**
- Input: Missing required information.
- Expected Status: 400 Bad Request.
- Expected Result: Error message is returned.

**Failure Case:**
- Input: Email already registered.
- Expected Status: 409 Conflict.
- Expected Result: Duplicate email error.

### POST /api/login

**Success Case:**
- Input: Correct email and password.
- Expected Status: 200 OK.
- Expected Result: Authentication token is returned.

**Failure Case:**
- Input: Incorrect email or password.
- Expected Status: 401 Unauthorized.
- Expected Result: Invalid credentials error.

## 2. Course Endpoints

### GET /api/courses

**Success Case:**
- Expected Status: 200 OK.
- Expected Result: Returns a list of courses.

### GET /api/courses/{id}

**Success Case:**
- Expected Status: 200 OK.
- Expected Result: Returns the requested course.

**Failure Case:**
- Expected Status: 404 Not Found.
- Expected Result: Course not found.

## Actual Test Results

To be completed after implementing and testing the API.

