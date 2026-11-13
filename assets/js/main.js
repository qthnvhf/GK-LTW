// Xem truoc anh nguoi dung truoc khi tai len server.
document.querySelectorAll('[data-image-input]').forEach(function (input) {
    input.addEventListener('change', function () {
        var preview = document.querySelector('[data-image-preview]');
        if (!preview || !input.files || !input.files[0]) return;
        var file = input.files[0];
        if (!file.type.startsWith('image/')) return;
        var objectUrl = URL.createObjectURL(file);
        preview.onload = function () { URL.revokeObjectURL(objectUrl); };
        preview.src = objectUrl;
    });
});

// Hoi xac nhan truoc khi gui form xoa.
document.querySelectorAll('form[data-confirm]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
        if (!window.confirm(form.dataset.confirm)) event.preventDefault();
    });
});
