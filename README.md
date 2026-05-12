# EventSpot — Backend

Projet final du parcours 2024

A concise backend for EventSpot. This repository contains the server-side code implemented in PHP and includes Blade templates. It provides APIs and/or server-rendered pages to manage events, users, and related resources for the EventSpot application.

---

## Table of contents
- [About](#about)
- [Key features](#key-features)
- [Tech stack](#tech-stack)
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Environment](#environment)
- [Database](#database)
- [Run locally](#run-locally)
- [Testing](#testing)
- [Deployment](#deployment)
- [Contributing](#contributing)
- [License & Contact](#license--contact)
- [Notes](#notes)

---

## About
EventSpot-back is the backend portion of the EventSpot project (final project for the 2024 learning path). It is primarily written in PHP and contains Blade template files (Blade suggests Laravel usage, but the README keeps instructions flexible).

---

## Key features
- Event CRUD (Create, Read, Update, Delete)
- User authentication & authorization (typical in backend projects)
- API endpoints for frontend consumption
- Blade-based server-rendered views (small proportion of repo)
- Database migrations and seeders (if included)

---

## Tech stack
- PHP (≈98.4% of repository)
- Blade templates (≈1.6% of repository)
- Typical supporting tools: Composer, (optionally) Laravel framework, a SQL database (MySQL / MariaDB / PostgreSQL)

---

## Prerequisites
- PHP (version 8.0+ recommended)
- Composer
- A database server (MySQL, MariaDB, or PostgreSQL)
- Node & npm/yarn (only if frontend build or asset compilation is present)

---

## Installation (general)
1. Clone the repo:
   ```bash
   git clone https://github.com/Sodja1234/eventspot-back.git
   cd eventspot-back
   ```
2. Install PHP dependencies:
   ```bash
   composer install
   ```
3. Copy environment file and update settings:
   ```bash
   cp .env.example .env
   # then edit .env to configure DB credentials, APP_KEY, etc.
   ```
4. Generate application key (if Laravel):
   ```bash
   php artisan key:generate
   ```

---

## Environment (common variables)
Set the following in your `.env`:
- APP_NAME, APP_ENV, APP_KEY, APP_URL
- DB_CONNECTION, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
- MAIL_*, CACHE_DRIVER, QUEUE_CONNECTION (if used)

---

## Database
If migrations and seeders exist:
```bash
php artisan migrate
php artisan db:seed
```
If not using Laravel, run whatever migration/seed commands the project provides.

---

## Run locally
If this is a Laravel project:
```bash
php artisan serve
# Visit: http://127.0.0.1:8000
```
Otherwise use the project's documented server start command or host under your web server (Nginx/Apache + PHP-FPM).

---

## Testing
If PHPUnit is configured:
```bash
vendor/bin/phpunit
```
Or follow the repository's testing instructions (if any).

---

## Deployment
- Prepare `.env` with production values.
- Ensure database migrations are run on deploy.
- Use a process manager (Supervisor) for queue workers if applicable.
- Use appropriate web server configuration (Nginx/Apache + PHP-FPM).
- Consider deployment platforms like Heroku, DigitalOcean, Fly.io, or cloud provider of your choice.

---

## Contributing
- Please open issues to report bugs or request features.
- Create branches from `main` (or repo default) for PRs and use descriptive commit messages.
- Add tests for new features or fixes when possible.

---

## License & Contact
- Add your license file (e.g., MIT) in the repo root if you want an open-source license.
- Contact: @Sodja1234 on GitHub

---

## Notes
- This README intentionally stays generic because the repo structure / framework detection may vary. If this repository is a Laravel project, the commands above apply directly. If you'd like, I can:
  - Inspect the repository to produce a tailored README with exact install/run steps.
  - Create the README.md file in the repo and open a PR for you.
