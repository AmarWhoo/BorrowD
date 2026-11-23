# AI Log

## Milestone 1

### Tools and tasks

- **Tool:** Backend agent
- **Model:** gpt 5.6 luna
- **Task:** Add a footer to `frontend/index.html`
- **Main prompt:** "Add a footer to frontend/index.html"
- **What was generated:** No code change. The agent reasoned about modifying frontend code and asked whether it should create the missing file or use an existing file in another location.
- **What the student kept, changed, or rejected:** No generated changes were kept. The clarification request was cancelled, and no fix was made.

### Where the AI was wrong

- **What was wrong:** The Backend agent considered modifying frontend code, even though it was intended only for backend code modification.
- **How it was noticed:** The agent's reasoning indicated that it was attempting to modify frontend code and later asked whether it should create the missing file or use an existing file elsewhere.
- **How it was fixed:** No fix was made.

### Tools and tasks

- **Tool:** Frontend
- **Model:** gpt 5.6 luna
- **Task:** Create an SPA shell with hash-based navigation in the frontend, starting with empty views.
- **Main prompt:** "Set up the SPA shell for BorrowD in the frontend/ folder, using the Bootstrap template that is already there. Include the requested BorrowD navbar, main view container, eight empty view files, jQuery hash-based routing, one-time view loading, view show/hide behavior, and active-link highlighting. Do not add libraries, connect the backend, or create files outside frontend/. First show a short plan and wait for approval."
- **What was generated:** Functioning navigation with a small bug that improperly hid views on initial load.
- **What the student kept, changed, or rejected:** Kept all generated code and modified the view-hiding section in `app.js`.


## Milestone 3

### Tools and tasks

- **Tool:** Claude (claude.ai), model Claude Opus 5.5
- **Task:** Help with the service layer, FlightPHP routes, OpenAPI documentation and the Open Library integration, following Labs 4–6.
- **Main prompts:** "I would like us to go step by step, going over Business Logic, then the Presentation layer, and finally the necessary APIs. Begin with laying out a plan, then we can go slowly thru the steps and finish it" and, for each later part, "review the code, give a plan and then we go".
- **What was generated:** A step-by-step plan for each part based on Labs 4–6. Some steps had mistakes (see below), such as DAO methods given as scattered snippets.
- **What the student kept, changed, or rejected:** Kept the plan and some of the generated code after testing each step with `test.php`, Postman and Swagger UI on XAMPP. Chose the loan rules (14 days, at most 3 active loans, no borrowing with an overdue loan), checked the real Open Library response in the browser, and fixed the setup when `.htaccess` was in the wrong folder.

### Where the AI was wrong

- **What was wrong:** The first plan only blocked deleting users with *active* loans. Because of the foreign keys, deleting a user with a returned loan or a review would have caused a database error (500).
- **How it was noticed:** While going through the user rules, compared with the foreign keys in `library.sql`.
- **How it was fixed:** The rule now blocks deleting a user with any loans or reviews and answers 400 with a clear message.

- **What was wrong:** New DAO methods were given as separate snippets over several steps, so some were missing and `test.php` failed with `Call to undefined method CategoryDao::get_by_name()`.
- **How it was noticed:** Running `test.php`.
- **How it was fixed:** Replaced the changed DAO files with complete versions; all checks passed afterwards.