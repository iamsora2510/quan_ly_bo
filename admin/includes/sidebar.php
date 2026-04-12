<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
  <div class="sidebar-brand">
    <a href="index.php" class="brand-link">
      <img src="../assets/img/AdminLTELogo.png" alt="Logo" class="brand-image opacity-75 shadow" />
      <span class="brand-text fw-light">Quản Lý Bò</span>
    </a>
  </div>

  <div class="sidebar-wrapper">
    <nav class="mt-2">
      <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="navigation">

        <li class="nav-item menu-open">
          <a href="index.php" class="nav-link active">
            <i class="nav-icon bi bi-speedometer"></i>
            <p>Bảng điều khiển</p>
          </a>
        </li>

        <li class="nav-header">NGHIỆP VỤ</li>

        <li class="nav-item">
          <a href="danh-sach-bo.php" class="nav-link">
            <i class="nav-icon bi bi-list-columns-reverse"></i>
            <p>Danh sách Bò</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="quan-ly-dat-cho.php" class="nav-link <?= (basename($_SERVER['PHP_SELF']) == 'quan-ly-dat-cho.php') ? 'active' : '' ?>">
            <i class="nav-icon bi bi-telephone-inbound text-warning"></i>
            <p>
              Quản lý Đặt chỗ
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="danh-sach-phieu.php" class="nav-link">
            <i class="nav-icon bi bi-cart-check"></i>
            <p>Danh sách Phiếu Nhập</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="them-phieu-nhap.php" class="nav-link">
            <i class="nav-icon bi bi-cart-plus"></i>
            <p>Lập Phiếu Nhập Mới</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="cong-no.php" class="nav-link">
            <i class="nav-icon bi bi-credit-card-2-front"></i>
            <p>Quản lý Công nợ</p>
          </a>
        </li>

        <li class="nav-item">
          <a href="danh-sach-hoa-don.php" class="nav-link">
            <i class="nav-icon bi bi-cash-stack"></i>
            <p>Hóa đơn Bán</p>
          </a>
        </li>
        <li class="nav-item">
          <a href="quan-ly-doi-tac.php" class="nav-link">
            <i class="nav-icon bi bi-person-rolodex text-warning"></i>
            <p>
              Quản Lý Đối Tác
            </p>
          </a>
        </li>

        <li class="nav-header">BÁO CÁO</li>

        <li class="nav-item">
          <a href="bao-cao-thong-ke.php" class="nav-link">
            <i class="nav-icon bi bi-graph-up-arrow"></i>
            <p>Báo cáo lợi nhuận</p>
          </a>
        </li>

        <li class="nav-item mt-3">
          <a href="../logout.php" class="nav-link text-danger">
            <i class="nav-icon bi bi-box-arrow-right"></i>
            <p>Đăng xuất</p>
          </a>
        </li>

      </ul>
    </nav>
  </div>
</aside>