function openModal(id, giong, can, loai) {
    const modalElement = document.getElementById('modalDuyet');
    const header = document.getElementById('modalHeader');
    const title = document.getElementById('modalTitle');
    const boxGia = document.getElementById('box_gia_mua');
    const btnSubmit = document.getElementById('btnSubmit');
    const inputGia = document.getElementById('input_gia_mua');
    
    // Gán dữ liệu ẩn
    document.getElementById('id_ky_gui').value = id;
    document.getElementById('hinh_thuc_tiep_nhan').value = loai;
    document.getElementById('info_bo').innerText = `Bò ${giong} (${can} kg)`;

    if (loai === 'mua_dut') {
        header.className = "modal-header bg-warning text-dark py-3";
        title.innerText = "MUA ĐỨT & LẬP PHIẾU THU";
        boxGia.style.display = "block";
        btnSubmit.className = "btn btn-warning px-5 fw-bold rounded-pill text-dark";
        inputGia.required = true;
    } else {
        header.className = "modal-header bg-success text-white py-3";
        title.innerText = "TIẾP NHẬN KÝ GỬI";
        boxGia.style.display = "none";
        btnSubmit.className = "btn btn-success px-5 fw-bold rounded-pill";
        inputGia.required = false;
        inputGia.value = 0;
    }

    const myModal = new bootstrap.Modal(modalElement);
    myModal.show();
}