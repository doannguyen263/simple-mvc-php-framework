<?php
use App\Helpers\View;
use App\Helpers\FlashMessage;
use App\Middleware\CSRFTokenTrait;
View::renderHeader();

$user_id = $_SESSION['user']['ID'];
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
              <h1 class="m-0">Add backlink</h1>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                <li class="breadcrumb-item active">Add backlink</li>
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
                    <label for="formUsername">Link</label>
                  </div>
                  <div class="col-md-10">
                    <input type="url" name="link" class="form-control" placeholder="Enter Link" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formFullName">ID</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="content_id" class="form-control" placeholder="Enter ID Ví dụ: #content">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formEmail">Class</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="content_class" class="form-control"  placeholder="Enter Class Ví dụ .classA hoặc .classA.classB">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Tên đường dẫn</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="name_link" class="form-control" placeholder="Tên đường dẫn">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Danh mục</label>
                  </div>
                  <div class="col-md-10">
                  <select name="type" class="form-select" aria-label="Default select example">
                    <option value="onpage" selected>Onpage</option>
                    <option value="offpage">Offpage</option>
                    </select>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">Thêm mới</button>
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                <?= CSRFTokenTrait::generateCSRFTokenInput('csrf_banklink_create_token') ?>
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