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
              <h1 class="m-0">Edit user</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                <li class="breadcrumb-item active">Edit user</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="content">
        <div class="container-fluid">
          <div class="card card-default color-palette-box">

            <div class="card-body">
              <form method="POST" class="mb-3">
              <p class="text-uppercase"><strong>Cập nhật thông tin</strong></p>
              <hr>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formUsername">Username</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="user_login" value="<?= $user['user_login'] ?>" class="form-control" id="formUsername" placeholder="Enter Username" disabled>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formFullName">Họ và tên</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="user_fullname" value="<?= $user['user_fullname'] ?>" class="form-control" id="formFullName" placeholder="Enter email">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formEmail">Email</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="user_email" value="<?= $user['user_email'] ?>" class="form-control" id="formEmail" placeholder="Enter email">
                  </div>
                </div>
                <hr>
                <p class="text-uppercase"><strong>Cập nhật mật khẩu</strong></p>
                <hr>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Mật khẩu hiện tại</label>
                  </div>
                  <div class="col-md-10">
                    <input type="password" name="old_password" class="form-control" id="formPassword" placeholder="Password">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Mật khẩu mới</label>
                  </div>
                  <div class="col-md-10">
                    <input type="password" name="new_password" class="form-control" id="formPassword" placeholder="Password">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Nhập lại Mật khẩu mới</label>
                  </div>
                  <div class="col-md-10">
                    <input type="password" name="user_pass_confirm" class="form-control" id="formPassword" placeholder="Password">
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="user_id" id="user_id" value="<?= $userId ?>">
                <?= $this->generateCSRFTokenInput('csrf_user_edit_token') ?>
              </form>

              <?php $this->displayFlashMessage();?>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<?php
require_once URL_VIEWS_ADMIN . '/layout/footer.php';
?>