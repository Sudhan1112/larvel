# Architecture & Design Decisions

## Data Modeling

The database schema is designed to be highly relational and extensible to support future question types.

1. **`quizzes`**: The main entity containing high-level details (`title`, `description`).
2. **`questions`**: Belongs to a Quiz.
   - Designed with a polymorphic-like `type` column (`binary`, `single_choice`, `multiple_choice`, `text_input`, `number_input`).
   - Uses `content` for rich HTML text.
   - Contains `media_path` and `media_type` to natively support images and video URLs.
3. **`options`**: Belongs to a Question. 
   - Each option represents a possible answer.
   - Contains an `is_correct` boolean. 
   - For text/number input questions, the single correct answer is stored here. This standardizes how evaluation is performed without adding extra columns to the `questions` table.
4. **`attempts`**: Represents a user's session of taking a quiz. Stores the `user_name` and the final `score`.
5. **`answers`**: Stores individual responses.
   - Relates to both the `Attempt` and the `Question`.
   - `answer_value`: A flexible text/JSON column to store the user's input (e.g., option ID, array of selected option IDs, or raw text string).
   - Stores `is_correct` and `marks_awarded` for historical accuracy even if the quiz is later modified.

## Extensibility

### Adding New Question Types
Because of the generic `type` field and the flexible `options` structure, adding a new question type (e.g., "Matching" or "Sorting") involves:
1. Adding the new type to the frontend dropdown.
2. Handling the specific UI rendering in `show.blade.php`.
3. Adding a new `elseif` evaluation block in `QuizController@attempt` logic. 
No database migrations are strictly required for standard new types since `options` and `answers` can store JSON arrays or generic strings.

### Media Handling
By defining `media_type` and `media_path`, the application can easily be extended to support audio files or document attachments in the future without changing the schema.

## Frontend Approach
To maintain simplicity while achieving a modern look:
- **Tailwind CSS (via CDN)**: For rapid, clean styling without requiring a build step.
- **Alpine.js**: For handling dynamic form states in the "Add Question" section. It dynamically manages the UI state based on the selected `type` (e.g., showing/hiding checkboxes, toggling option inputs) without writing heavy vanilla JS or setting up Vue/React.
