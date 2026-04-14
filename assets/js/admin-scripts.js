// assets/js/admin-scripts.js

function tinhTongPhieuNhap() {
    // Lấy giá trị từ các ô input
    let tienBo = document.getElementById('tong_tien_bo').value || 0;
    let tienXe = document.getElementById('chi_phi_xe').value || 0;
    
    // Tổng cộng = Tiền bò + Tiền xe
    let tong = parseFloat(tienBo) + parseFloat(tienXe);
    
    // Hiển thị kết quả định dạng VNĐ vào ô input và text hiển thị
    let formatted = tong.toLocaleString('vi-VN') + " đ";
    document.getElementById('hien_thi_tong').innerText = formatted;
    document.getElementById('tong_thanh_toan_hidden').value = tong;
}/**
 * File: admin-scripts.js
 * Chứa toàn bộ logic xử lý giao diện cho trang Quản trị
 */

// 1. Hàm tính tổng tiền cho Phiếu Nhập Bò (Phần 1)
function tinhTongPhieuNhap() {
    // Lấy giá trị từ các ID đã đặt trong HTML
    let tienBo = document.getElementById('tong_tien_bo').value || 0;
    let tienXe = document.getElementById('chi_phi_xe').value || 0;
    
    // Ép kiểu số để tính toán
    let tong = parseFloat(tienBo) + parseFloat(tienXe);
    
    // Hiển thị kết quả định dạng tiền Việt (đ)
    let hienThi = document.getElementById('hien_thi_tong');
    if(hienThi) {
        hienThi.innerText = tong.toLocaleString('vi-VN') + " đ";
    }

    // Gán vào input ẩn để gửi lên server nếu cần
    let inputHidden = document.getElementById('tong_thanh_toan_hidden');
    if(inputHidden) {
        inputHidden.value = tong;
    }
}

// 2. Hàm xác nhận xóa (Sửa lại bản này cho chuẩn)
function confirmDelete(id, type) {
    let message = "Bạn có chắc chắn muốn xóa không?";
    let url = "";

    if(type === 'bo') {
        message = "Bạn có chắc chắn muốn xóa con bò này không? Ảnh bò cũng sẽ bị xóa vĩnh viễn!";
        url = "xoa-bo.php?id=" + id;
    } else if(type === 'phieu_nhap') {
        message = "Xóa phiếu nhập này sẽ gỡ liên kết các con bò trong phiếu. Bạn vẫn muốn xóa?";
        url = "xoa-phieu-nhap.php?id=" + id;
    }
    
    if(confirm(message)) {
        window.location.href = url;
    }
}
/**
 * File: assets/js/admin-scripts.js
 * Chứa toàn bộ logic xử lý giao diện cho trang Quản trị
 */

// 1. Hàm tính tổng tiền cho Phiếu Nhập Bò
function tinhTongPhieuNhap() {
    let tienBo = document.getElementById('tong_tien_bo').value || 0;
    let tienXe = document.getElementById('chi_phi_xe').value || 0;
    
    let tong = parseFloat(tienBo) + parseFloat(tienXe);
    
    let hienThi = document.getElementById('hien_thi_tong');
    if(hienThi) {
        hienThi.innerText = tong.toLocaleString('vi-VN') + " đ";
    }

    let inputHidden = document.getElementById('tong_thanh_toan_hidden');
    if(inputHidden) {
        inputHidden.value = tong;
    }
}

/**
 * 2. Hàm xác nhận xóa (Phiên bản linh hoạt)
 * @param {number} id - ID của đối tượng cần xóa
 * @param {string} actionFile - Tên file xử lý xóa (ví dụ: 'xoa-bo.php')
 */
function confirmDelete(id, actionFile) {
    let message = "Bạn có chắc chắn muốn xóa không?";
    
    // Tùy biến lời nhắn dựa trên tên file để người dùng dễ hiểu
    if(actionFile === 'xoa-bo.php') {
        message = "Bạn có chắc chắn muốn xóa con bò này không?\nDữ liệu và ảnh bò sẽ bị xóa vĩnh viễn!";
    } else if(actionFile === 'xoa-phieu-nhap.php') {
        message = "Xóa phiếu nhập này sẽ gỡ liên kết các con bò trong phiếu.\nBạn vẫn muốn tiếp tục?";
    } else if(actionFile === 'xoa-doi-tac.php') {
        message = "Bạn có chắc chắn muốn xóa đối tác này?";
    }

    // Thực hiện chuyển hướng nếu người dùng nhấn OK
    if(confirm(message)) {
        // Vì file xử lý nằm cùng thư mục với trang danh sách, chỉ cần gọi trực tiếp tên file
        window.location.href = actionFile + "?id=" + id;
    }
}

// 3. Hàm tính tiền bán bò
function tinhTienBan() {
    let giaBan = document.getElementById('gia_ban').value || 0;
    let thue = document.getElementById('thue_vat').value || 0;
    let tongBan = parseFloat(giaBan) + parseFloat(thue);
    
    let el = document.getElementById('tong_hoa_don');
    if(el) el.innerText = tongBan.toLocaleString('vi-VN') + " đ";
}

// 4. Logic cho Checkbox chọn tất cả (Dùng trong danh sách bò)
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAll');
    if(checkAll) {
        checkAll.addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.checkItem');
            checkboxes.forEach(item => {
                item.checked = checkAll.checked;
            });
        });
    }
});
// 3. Hàm tính tiền bán bò (Dự phòng cho Phần 3)
function tinhTienBan() {
    let giaBan = document.getElementById('gia_ban').value || 0;
    let thue = document.getElementById('thue_vat').value || 0;
    let tongBan = parseFloat(giaBan) + parseFloat(thue);
    
    let el = document.getElementById('tong_hoa_don');
    if(el) el.innerText = tongBan.toLocaleString('vi-VN') + " đ";
}