# TODO: Implement Progress Tracker for Teaching Books (Jilid & Halaman)

## Database Changes
- [x] Update `database/quran_tracker.sql` to add new tables:
  - `teaching_books` (id, volume_number, title, total_pages, created_at)
  - `progress_books` (id, child_id, book_id, page, status, note, updated_by, updated_at)
  - Update enum status in `progress_status` to include 'fluent', 'repeating'

## Model Updates
- [x] Update `src/Models/Progress.php` to handle progress based on books

## Controller Updates
- [x] Add methods in `src/Controllers/AdminController.php` for managing teaching books

## Views
- [x] Create `src/Views/admin/teaching_books.php` (list all books)
- [x] Create `src/Views/admin/create_teaching_book.php` (form to add new book)
- [x] Create `src/Views/admin/edit_teaching_book.php` (form to edit book)
- [x] Create `src/Views/parent/update_progress_books.php` (update progress for books)
- [x] Create `src/Views/teacher/update_progress_books.php` (update progress for books)
- [x] Update `src/Views/dashboard/superadmin.php` to add link to manage books

## Actions
- [x] Create `src/Actions/update_progress_books_action.php` for handling progress updates

## Routing
- [x] Update routing in `public/index.php` for new pages

## Testing
- [ ] Test database migration and seeding sample data
- [ ] Test CRUD operations for teaching books
- [ ] Test update progress based on books
- [x] Update navigation menu if needed
