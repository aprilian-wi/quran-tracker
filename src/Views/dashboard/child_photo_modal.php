<div class="modal fade" id="childPhotoModal" tabindex="-1" aria-labelledby="childPhotoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form id="childPhotoForm" method="POST" enctype="multipart/form-data" action="<?= BASE_URL ?>src/Actions/upload_child_photo_action.php">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="childPhotoModalLabel">Upload Child Photo</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="child_id" id="modalChildId" value="">
          <div class="mb-3">
            <label for="photoInput" class="form-label">Select photo</label>
            <input class="form-control" type="file" id="photoInput" name="photo" accept="image/*" required>
            <div class="form-text">Allowed file types: JPG, PNG, GIF. Max size: 5MB.</div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Upload</button>
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        </div>
      </div>
    </form>
  </div>
</div>
