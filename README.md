# Project Management System & Secure Collaborative API

A robust, secure, and scalable Laravel-based RESTful API for advanced project management and secure team collaboration.

## Table of Contents
- [Features](#features)
- [Architecture](#architecture)
- [Security](#security)
- [Setup & Installation](#setup--installation)
- [Testing](#testing)
- [API Overview](#api-overview)

---

## Features
- **Teams & Projects:**
  - Create, update, and manage teams and projects
  - Assign users to teams and projects with roles (owner, admin, manager, member)
- **Tasks:**
  - Assign, update, and track tasks with priorities, statuses, and due dates
  - Advanced Eloquent accessors, mutators, and query scopes
- **Comments & Attachments:**
  - Polymorphic comments and file attachments for tasks, projects, and comments
  - Secure file upload (private disk, MIME/type/size checks)
- **Notifications:**
  - Real-time and email notifications for assignments and updates
- **Authentication & Authorization:**
  - Laravel Sanctum for API authentication
  - Spatie Permissions (with team support) for roles and permissions
  - Strict policies for all resources
- **Validation & Security:**
  - Form Requests for strict validation and authorization
  - Input sanitization and XSS protection
  - Rate limiting and CSRF protection
- **Performance:**
  - Caching for popular projects, active teams, and completed task counts
  - Queues for heavy tasks (emails, notifications, file processing)
- **Testing:**
  - Comprehensive unit and feature tests
  - Factories for all models
- **Console Commands:**
  - Artisan commands for cleaning old attachments and updating overdue tasks

---

## Architecture
- **RESTful API** with resource controllers and strict validation
- **Eloquent ORM** with advanced relationships, accessors, mutators, and observers
- **Event-driven**: Events, listeners, and observers automate notifications and model logic
- **Modular Services** for business logic and caching
- **Custom Policies** for granular authorization

---

## Security
- Input sanitization (mutators, validation)
- XSS and CSRF protection
- Secure file storage (private disk)
- Password hashing (Laravel default)
- Rate limiting for sensitive endpoints

---

## Setup & Installation

1. **Clone the repository:**
   ```sh
   git clone <your-repo-url>
   cd <project-directory>
   ```
2. **Install dependencies:**
   ```sh
   composer install
   npm install && npm run build
   ```
3. **Copy and configure environment:**
   ```sh
   cp .env.example .env
   php artisan key:generate
   # Set your DB and mail credentials in .env
   ```
4. **Run migrations and seeders:**
   ```sh
   php artisan migrate --seed
   ```
5. **(Optional) Run queue worker:**
   ```sh
   php artisan queue:work
   ```
6. **Run the development server:**
   ```sh
   php artisan serve
   ```

---

## Testing
- Run all tests:
  ```sh
  php artisan test
  ```
- Feature and unit tests cover models, services, observers, API endpoints, authentication, permissions, file uploads, queues, and events.

---

## API Overview
- RESTful endpoints for teams, projects, tasks, comments, attachments, and notifications
- Authentication via Laravel Sanctum
- See `routes/api.php` for all available endpoints

---
