// assets/js/ban-hang.js

function formatTien(so) {
    return new Intl.NumberFormat('vi-VN').format(so);
}

function tinhToanLôBò() {
    let tongBill = 0;
    let inputs = document.querySelectorAll('.gia-ban-input');

    inputs.forEach(input => {
        // 1. Lấy ID bò từ name gia_ban[ID]
        let id = input.name.match(/\[(\d+)\]/)[1];
        let giaBan = parseFloat(input.value) || 0;
        let giaVon = parseFloat(input.getAttribute('data-von')) || 0;
        
        // 2. Tính lợi nhuận từng con
        let loiNhuan = giaBan - giaVon;
        let displayLoi = document.getElementById('loi_nhuan_' + id);
        
        if (displayLoi) {
            displayLoi.innerText = formatTien(loiNhuan);
            displayLoi.className = loiNhuan >= 0 ? 'fw-bold text-primary' : 'fw-bold text-danger';
        }
        
        tongBill += giaBan;
    });

    // 3. Hiển thị tổng tiền bill
    document.getElementById('tong_tien_bill').innerText = formatTien(tongBill) + ' đ';

    // 4. Tính nợ tổng đơn hàng
    let traTruoc = parseFloat(document.getElementById('tra_truoc').value) || 0;
    let conNo = tongBill - traTruoc;
    
    let vungNo = document.getElementById('vung_no');
    let hienThiNo = document.getElementById('so_tien_no');

    if (conNo > 0) {
        vungNo.style.display = 'block';
        hienThiNo.innerText = formatTien(conNo);
    } else {
        vungNo.style.display = 'none';
    }
}