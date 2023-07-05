<?php
require_once URL_VIEWS_ADMIN . '/layout/header.php';
?>
<div class="hold-transition sidebar-mini">
  <div class="wrapper">
    <?php
    require_once URL_VIEWS_ADMIN . '/layout/sidebar.php';
    ?>
    <div class="content-wrapper">

      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Danh sách</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                <li class="breadcrumb-item active">Danh sách</li>
              </ol>
            </div>
          </div>
        </div>
      </div>


      <div class="content">
        <div class="card">
          <div class="card-header">
            <h3 class="card-title">Danh sách</h3>
          </div>
          <div class="card-body p-0">
            <table class="table table-striped projects">
              <thead>
                <tr>
                  <th style="width: 1%">
                    #
                  </th>
                  <th style="width: 20%">
                    Username
                  </th>
                  <th style="width: 30%">
                    Tên
                  </th>
                  <th>
                    Email
                  </th>
                  <th style="width: 8%" class="text-center">
                    Vai trò
                  </th>
                  <th style="width: 20%">
                  </th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td>
                    #
                  </td>
                  <td>
                    <?= $user['user_login'] ?>
                  </td>
                  <td>
                  <?= $user['user_fullname'] ?>
                  </td>
                  <td>
                    <?= $user['user_email'] ?>
                  </td>
                  <td class="project-state">
                    <?= $user['role'] ?>
                  </td>
                  <td class="project-actions text-right">
                    
                    <a class="btn btn-info btn-sm" href="<?= SITE_URL ?>/edit-user?user_id=<?= $user['ID'] ?>">
                      <i class="fas fa-pencil-alt">
                      </i>
                      Edit
                    </a>
                    <a class="btn btn-danger btn-sm" href="<?= SITE_URL ?>/delete-user?user_id=<?= $user['ID'] ?>">
                      <i class="fas fa-trash">
                      </i>
                      Delete
                    </a>
                  </td>
                </tr>
                <?php endforeach; ?>
                
              </tbody>
            </table>
          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<?php
require_once URL_VIEWS_ADMIN . '/layout/footer.php';
?>