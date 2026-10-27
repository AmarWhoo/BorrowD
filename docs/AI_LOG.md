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
