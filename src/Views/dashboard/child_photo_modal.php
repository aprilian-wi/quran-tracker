
<!-- Child Photo Modal -->
<div class="modal fade" id="childPhotoModal" tabindex="-1" aria-labelledby="childPhotoModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="childPhotoModalLabel">Upload Child Photo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="childPhotoForm" action="<?= BASE_URL ?>public/index.php?page=parent/upload_photo" method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" name="child_id" id="modalChildId">
                    <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label">Select photo</label>
                        <input class="form-control" type="file" id="photo" name="photo" accept="image/*" required>
                        <div class="form-text">Allowed file types: JPG, PNG, GIF. Max size: 5MB.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
