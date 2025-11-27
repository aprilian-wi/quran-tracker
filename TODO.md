# TODO: Transform "Latest Update" Section into Notification System

## Database Changes
- [x] Add `notifications` table to `database/quran_tracker.sql`

## Model Updates
- [x] Add notification methods to `src/Models/Progress.php` (insert and fetch notifications)

## Controller Updates
- [x] Modify `src/Controllers/DashboardController.php` to fetch unread notifications instead of latest updates

## View Updates
- [x] Update `src/Views/dashboard/parent.php` to display notifications as dismissible alerts

## Action Updates
- [x] Modify `src/Actions/update_progress_action.php` to insert notification on teacher update
- [x] Modify `src/Actions/update_progress_books_action.php` to insert notification on teacher update
- [x] Modify `src/Actions/update_progress_prayers_action.php` to insert notification on teacher update

## JavaScript Enhancements
- [x] Add AJAX functionality to mark notifications as viewed when dismissed

## Followup Steps
- [ ] Run database migration to create notifications table (user will do manually)
- [ ] Test notification creation and viewing
- [ ] Ensure notifications only for teacher updates and disappear after viewing
