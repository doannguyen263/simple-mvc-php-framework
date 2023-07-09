<?php

use Symfony\Component\HttpFoundation\Request;

$user_name = $_SESSION['user']['user_login'] ?? '';

$Request = Request::createFromGlobals();
$currentRoute = $Request->query->get('route');
$menus = array(
  array(
    'name' => 'User',
    'link' => SITE_URL . '/user-index',
    'route' => 'user-index',
    'icon' => 'fa-solid fa-users',
    'submenu' => array(
      array(
        'name' => 'Danh sách',
        'link' => SITE_URL . '/user-index',
        'route' => 'user-index'
      ),
      array(
        'name' => 'Thêm mới',
        'link' => SITE_URL . '/user-create',
        'route' => 'user-create'
      ),
      array(
        'name' => 'Hồ sơ',
        'link' => SITE_URL . '/user-edit',
        'route' => 'user-edit'
      )
    )
  ),
  array(
    'name' => 'User',
    'link' => SITE_URL . '/user-index',
    'route' => 'user2-index',
    'icon' => 'fa-solid fa-book',
    'submenu' => array(
      array(
        'name' => 'Danh sách',
        'link' => SITE_URL . '/user-index',
        'route' => 'user2-index'
      ),
      array(
        'name' => 'Thêm mới',
        'link' => SITE_URL . '/user-create',
        'route' => 'user2-create'
      ),
      array(
        'name' => 'Hồ sơ',
        'link' => SITE_URL . '/user-edit',
        'route' => 'user2-edit'
      )
    )
  )
);

?>

<?php
$activeMenuIndex = null;
$activeSubmenuIndex = null;

$currentRoute = $Request->query->get('route');

foreach ($menus as $menuIndex => $menu) {

  if ($menu['route'] == $currentRoute) {
    $activeMenuIndex = $menuIndex;
    break;
  }


  if (isset($menu['submenu'])) {
    foreach ($menu['submenu'] as $submenuIndex => $subItem) {
      if ($subItem['route'] === $currentRoute) {
        $activeMenuIndex = $menuIndex;
        $activeSubmenuIndex = $submenuIndex;
        break 2;
      }
    }
  }
}
?>

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul>
  <ul class="navbar-nav ml-auto">
    <div class="nav-item dropdown user-menu">
      <button class="nav-link dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        <img src="public/images/user.png" class="user-image img-circle elevation-2" alt="User Image">
        <span class="d-none d-md-inline"><?= $user_name ?></span>
      </button>
      <ul class="dropdown-menu dropdown-menu-end">
        <li><a class="dropdown-item" href="<?= SITE_URL . '/user-edit' ?>"><i class="fa-solid fa-user-gear"></i> Hồ sơ</a></li>
        <li><a class="dropdown-item" href="<?= SITE_URL . '/login?action=logout' ?>"><i class="fa-solid fa-arrow-right-from-bracket"></i> Đăng xuất</a></li>
      </ul>
    </div>

  </ul>
</nav>
<aside class="main-sidebar sidebar-dark-primary elevation-4">

  <a href="<?= SITE_URL ?>" class="brand-link">
    <img src="public/images/settings.png" alt="Admin Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">AdminPage</span>
  </a>

  <div class="sidebar">

    <nav class="mt-3 pb-3 mb-3">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <?php foreach ($menus as $menuIndex => $menu) : ?>
          <?php
          $isActiveMenu = $menuIndex === $activeMenuIndex;
          $isActiveSubmenu = $isActiveMenu && $activeSubmenuIndex !== null;
          ?>
          <li class="nav-item <?= $isActiveMenu ? 'menu-open' : '' ?>">
            <a href="#" class="nav-link">
              <i class="nav-icon <?= $menu['icon'] ?>"></i>
              <p>
                <?= $menu['name'] ?>
                <i class="right fas fa-angle-left"></i>
              </p>
            </a>

            <?php if (isset($menu['submenu'])) : ?>
              <ul class="nav nav-treeview">
                <?php foreach ($menu['submenu'] as $submenuIndex => $subItem) : ?>
                  <li class="nav-item">
                    <a href="<?= $subItem['link'] ?>" class="nav-link <?= ($submenuIndex === $activeSubmenuIndex) ? 'active' : '' ?>">
                      <i class="far fa-circle nav-icon"></i>
                      <p><?= $subItem['name'] ?></p>
                    </a>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>

  </div>

</aside>