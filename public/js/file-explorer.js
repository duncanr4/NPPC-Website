document.addEventListener('DOMContentLoaded', function() {
    // === DRAG & DROP UPLOAD ===
    var page = document.querySelector('.fi-page');
    var dropzone = document.getElementById('fe-dropzone');
    var dragCounter = 0;

    if (page && dropzone) {
        page.addEventListener('dragenter', function(e) {
            e.preventDefault();
            dragCounter++;
            if (e.dataTransfer.types.includes('Files')) {
                dropzone.style.display = 'block';
            }
        });

        page.addEventListener('dragleave', function(e) {
            e.preventDefault();
            dragCounter--;
            if (dragCounter <= 0) {
                dropzone.style.display = 'none';
                dragCounter = 0;
            }
        });

        page.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        page.addEventListener('drop', function(e) {
            e.preventDefault();
            dragCounter = 0;
            dropzone.style.display = 'none';

            if (e.dataTransfer.files.length > 0) {
                var fileInput = page.querySelector('input[type="file"]');
                if (fileInput) {
                    var dt = new DataTransfer();
                    for (var i = 0; i < e.dataTransfer.files.length; i++) {
                        dt.items.add(e.dataTransfer.files[i]);
                    }
                    fileInput.files = dt.files;
                    fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                }
            }
        });
    }
});
