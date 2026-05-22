// Hiệu ứng thông báo khi nhấn "Đặt mượn"
function confirmRent(bookTitle) {
    return confirm(`Bạn có chắc chắn muốn đặt mượn cuốn "${bookTitle}" không?`);
}

// Xử lý hiển thị ảnh xem trước khi Admin chọn file (Preview Image)
const uploadInput = document.querySelector('input[name="cover_image"]');
if (uploadInput) {
    uploadInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Giả sử bạn có một thẻ img để preview
                const preview = document.querySelector('#preview-img');
                if (preview) preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });
}