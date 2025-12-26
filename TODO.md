# Add Hadist Feature (Duplicate Short Prayer Functionality)

## Database Changes
- [ ] Add hadiths table to database/quran_tracker.sql
- [ ] Add progress_hadiths table to database/quran_tracker.sql

## Controller Updates
- [ ] Add hadith management methods to src/Controllers/AdminController.php

## Model Updates
- [ ] Add hadith progress methods to src/Models/Progress.php

## View Files
- [ ] Create src/Views/admin/manage_hadiths.php
- [ ] Create src/Views/admin/create_hadith.php
- [ ] Create src/Views/admin/edit_hadith.php
- [ ] Create src/Views/shared/list_hadiths.php

## Action Files
- [ ] Create src/Actions/store_hadith_action.php
- [ ] Create src/Actions/delete_hadith_action.php

## Routing Updates
- [ ] Update public/index.php for hadith routes

## Navigation Updates
- [ ] Update src/Views/layouts/main.php for hadith navigation
- [ ] Update src/Views/dashboard/superadmin.php for hadith menu

## Progress Update Views
- [ ] Update src/Views/admin/update_progress.php for hadith progress
- [ ] Update src/Views/teacher/update_progress.php for hadith progress
- [ ] Update src/Views/parent/update_progress.php for hadith progress

## Notification Updates
- [ ] Update notifications to include hadith type in Progress model
