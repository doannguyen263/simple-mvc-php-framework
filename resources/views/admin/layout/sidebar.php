<nav class="main-header navbar navbar-expand navbar-white navbar-light">

  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="index3.html" class="nav-link">Home</a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      <a href="#" class="nav-link">Contact</a>
    </li>
  </ul>

  <ul class="navbar-nav ml-auto">



    <div class="nav-item dropdown user-menu">
      <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="public/images/user.png" class="user-image img-circle elevation-2" alt="User Image">
        <span class="d-none d-md-inline">Alexander Pierce</span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user-gear"></i> Hồ sơ</a></li>
        <li><a class="dropdown-item" href="<?= SITE_URL . '/login?action=logout' ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a></li>
      </ul>
    </div>

  </ul>
</nav>
<aside class="main-sidebar sidebar-dark-primary elevation-4">

  <a href="index3.html" class="brand-link">
    <img src="public/images/settings.png" alt="Admin Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">AdminPage</span>
  </a>

  <div class="sidebar">

    <nav class="mt-3 pb-3 mb-3">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item">
          <a href="../gallery.html" class="nav-link">
            <i class="nav-icon far fa-image"></i>
            <p>
              Example
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../gallery.html" class="nav-link">
            <i class="nav-icon far fa-image"></i>
            <p>
              Example
            </p>
          </a>
        </li>
        <li class="nav-item">
          <a href="../gallery.html" class="nav-link">
            <i class="nav-icon far fa-image"></i>
            <p>
              Example
            </p>
          </a>
        </li>

        <li class="nav-item">
          <a href="#" class="nav-link">
            <i class="nav-icon fa-solid fa-users"></i>
            <p>
              User
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="../../index.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Danh sách</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../../index2.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Thêm mới</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="../../index3.html" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Hồ sơ</p>
              </a>
            </li>
          </ul>
        </li>


      </ul>
    </nav>

  </div>

</aside>