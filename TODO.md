# Task: Replace progress circle with photo and photo upload feature for Parent role

## Steps to complete

- [ ] Update database:
  - Add `photo` column (VARCHAR) to `children` table to store photo filename/path.
  
- [ ] Backend:
  - Update model (Child.php) to handle photo path.
  - Create an action to handle photo upload and replacement, including deleting old photo file.
  
- [ ] Frontend (src/Views/dashboard/parent.php):
  - Replace progress circle HTML for child photo container.
  - Add photo display with ratio 1:1.
  - Add a small pencil icon button in bottom-right corner of photo for editing.
  - Create popup/modal for photo upload triggered by pencil icon.
  - Add JS to handle popup and AJAX upload or form submission.
  
- [ ] File storage:
  - Save uploaded photos to `public/uploads/children_photos/`.
  - Delete old photo file when new one is uploaded.
  
- [ ] Validation and security:
  - Accept only image file types.
  - Limit file size.
  - Properly sanitize uploaded files to avoid injection.

## Follow-up steps after implementation:
- Test UI for photo display and update flow.
- Test file replacement and deletion.
- Verify old photos do not accumulate in storage.
- Ensure only Parent role can upload/change photos.
