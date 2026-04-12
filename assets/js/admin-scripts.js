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

// 2. Hàm xác nhận xóa (Dùng chung cho nhiều trang)
function confirmDelete(id, type) {
    let message = "Bạn có chắc chắn muốn xóa không?";
    if(type === 'phieu_nhap') message = "Xóa phiếu nhập này sẽ ảnh hưởng đến thống kê kho. Bạn vẫn muốn xóa?";
    
    if(confirm(message)) {
        // Tùy vào loại mà chuyển hướng link xóa
        window.location.href = `xoa-${type}.php?id=${id}`;
    }
}

// 3. Hàm tính tiền bán bò (Dự phòng cho Phần 3)
function tinhTienBan() {
    let giaBan = document.getElementById('gia_ban').value || 0;
    let thue = document.getElementById('thue_vat').value || 0;
    let tongBan = parseFloat(giaBan) + parseFloat(thue);
    
    let el = document.getElementById('tong_hoa_don');
    if(el) el.innerText = tongBan.toLocaleString('vi-VN') + " đ";
}