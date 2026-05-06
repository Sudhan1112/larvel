# AI Usage Documentation

During the development of this Laravel Quiz System, AI tools (specifically Claude/Gemini model integrations) were utilized to rapidly scaffold, implement, and refine the application.

## Prompts & Tasks

1. **Initial Project Setup & Scaffolding**
   - **Goal:** Install a fresh Laravel application and configure SQLite.
   - **Usage:** The AI agent automatically executed composer commands and handled environment setup.

2. **Database Modeling**
   - **Prompt/Logic:** "Create migrations and models for Quizzes, Questions, Options, Attempts, and Answers, ensuring support for multiple question types and media."
   - **Usage:** AI generated standard Laravel migration files with foreign key constraints, `cascadeOnDelete` rules, and appropriate column types (`enum`/`string` for types, `json` flexibility for answers).

3. **Controller Logic & Evaluation Algorithm**
   - **Prompt/Logic:** "Implement a QuizController that handles quiz creation, question addition, taking the quiz, and specifically calculating the total score based on the question type."
   - **Usage:** AI wrote the complex evaluation logic inside `QuizController@attempt`. It specifically handled the edge cases between `single_choice` (checking ID), `multiple_choice` (comparing sorted arrays of IDs), `number_input` (float comparison), and `text_input` (case-insensitive string comparison).

4. **Frontend Implementation**
   - **Prompt/Logic:** "Create Blade views using Tailwind CSS and AlpineJS to dynamically render forms based on the question type."
   - **Usage:** AI implemented the dynamic UI in `admin.quizzes.edit` using AlpineJS. It successfully bound the `type` select dropdown to conditionally show/hide radio buttons, checkboxes, or plain text inputs, significantly reducing the complexity of the admin form.

## Corrections & Refinements

- **Dependency Issues:** During initial installation, zip extension missing issues caused Composer to download from source. The AI intelligently monitored the command status and waited for the source compilation to complete without aborting.
- **Directory Structure:** The assignment required pushing the application to a specific repository. The AI correctly detected the nested folder structure and moved the Laravel application files to the root directory before committing to Git.
- **Evaluation Logic Correction:** Ensured that `multiple_choice` array comparisons were sorted (`sort()`) before equality checks to prevent false negatives when users select options in a different order.
