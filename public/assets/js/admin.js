/**
 * admin.js — prévisualisation d'upload d'images dans le back-office.
 */

document.addEventListener('DOMContentLoaded', function () {
    initImagePreview();
});

function initImagePreview() {
    document.querySelectorAll('[data-image-input]').forEach(function (input) {
        var previewId = input.getAttribute('data-image-input');
        var preview = document.getElementById(previewId);
        if (!preview) return;

        input.addEventListener('change', function () {
            preview.innerHTML = '';

            Array.prototype.forEach.call(input.files, function (file) {
                if (!file.type.startsWith('image/')) return;

                var reader = new FileReader();
                reader.onload = function (e) {
                    var img = document.createElement('img');
                    img.src = e.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        });
    });
}
