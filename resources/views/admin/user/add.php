<?php
use App\Helpers\View;
use App\Helpers\FlashMessage;
use App\Helpers\CSRFTokenTrait;
View::renderHeader();
?>
<div class="hold-transition sidebar-mini">
  <div class="wrapper">
    <?php
    require_once PATH_VIEWS_ADMIN . '/layout/sidebar.php';
    ?>
    <div class="content-wrapper">

      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">Add user</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                <li class="breadcrumb-item active">Add user</li>
              </ol>
            </div>
          </div>
        </div>
      </div>

      <div class="content">
        <div class="container-fluid">
          <div class="card card-default color-palette-box">

            <div class="card-body">
              <form method="POST" class="mb-3 needs-validation" novalidate>
              <hr>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formUsername">Username (*)</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="user_login" class="form-control" id="formUsername" placeholder="Enter Username" autocomplete="off" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formFullName">Họ và tên (*)</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="user_fullname" class="form-control" id="formFullName" placeholder="Enter Họ và tên" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formEmail">Email (*)</label>
                  </div>
                  <div class="col-md-10">
                    <input type="email" name="user_email" class="form-control" id="formEmail" placeholder="Enter email" required>
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Mật khẩu (*)</label>
                  </div>
                  <div class="col-md-10">
                    <input type="password" name="user_pass" class="form-control" id="formPassword" placeholder="Password" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Loại tài khoản</label>
                  </div>
                  <div class="col-md-10">
                  <select name="role" class="form-select" aria-label="Default select example">
                    <option value="user" selected>User</option>
                    <option value="admin">Admin</option>
                    </select>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <input type="hidden" name="action" value="create">
                <?= CSRFTokenTrait::generateCSRFTokenInput('csrf_user_create_token') ?>
              </form>

              <?php FlashMessage::displayFlashMessage();?>
            </div>

          </div>

        </div>
      </div>

    </div>
  </div>
</div>
<?php
View::renderFooter();
?>