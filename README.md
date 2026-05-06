# Dynamic Quiz System - Laravel Assignment

A flexible, dynamic Quiz System built with Laravel, featuring support for multiple question types, media integration, and robust evaluation logic.

## Prerequisites

- PHP >= 8.2
- Composer
- Node.js & npm (optional, but recommended)

## Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/Sudhan1112/larvel.git
   cd larvel
   ```

2. **Install dependencies:**
   ```bash
   composer install
   ```

3. **Environment Setup:**
   - Copy the environment file:
     ```bash
     cp .env.example .env
     ```
   - The application uses **SQLite** by default to make setup seamless. A `database.sqlite` file is created automatically when running migrations.

4. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

5. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

6. **Link Storage (For Media Uploads):**
   ```bash
   php artisan storage:link
   ```

7. **Start the Development Server:**
   ```bash
   php artisan serve
   ```
   Access the application at `http://localhost:8000`.

## Features

- **Quiz Creation**: Admin interface to create and manage quizzes.
- **Dynamic Question Types**: Support for Single Choice, Multiple Choice, True/False, Text Input, and Number Input.
- **Media Support**: Easily upload images or embed video URLs for each question.
- **Dynamic Frontend**: Alpine.js and Tailwind CSS are used to provide a responsive and clean UI without heavy asset compilation.
- **Instant Evaluation**: Fully automated scoring mechanism immediately after submission.
