document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.querySelector('#comment-form');
    const commentList = document.querySelector('.comment-list');

    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault(); // Ngăn trang web load lại

            const formData = new FormData(this);

            fetch('process-comment.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Tạo phần tử bình luận mới để chèn vào danh sách
                    const newComment = document.createElement('div');
                    newComment.className = 'comment-item';
                    newComment.innerHTML = `
                        <strong>Bạn:</strong>
                        <span>${data.content}</span>
                        <small>(Vừa xong)</small>
                    `;
                    commentList.prepend(newComment); // Thêm lên đầu danh sách
                    commentForm.reset(); // Xóa nội dung trong ô nhập
                } else {
                    alert('Có lỗi xảy ra: ' + data.message);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    }
});