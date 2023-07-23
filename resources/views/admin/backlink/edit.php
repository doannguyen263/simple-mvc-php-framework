<?php
use App\Helpers\View;
use App\Helpers\FlashMessage;
use App\Middleware\CSRFTokenTrait;
View::renderHeader();

$user = $_SESSION['user'];
$user_role = $user['role'] ?? '';

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
            <div class="col-sm-6 d-flex align-items-center">
              <h1 class="me-3">Sửa backlink</h1>
              <a class="btn btn-info btn-sm" href="<?= SITE_URL ?>/backlink-create">
                <i class="fa-solid fa-user-plus"></i>
                </i>
                Thêm backlink
              </a>
            </div>
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="<?= SITE_URL ?>">Home</a></li>
                <li class="breadcrumb-item active">Edit backlink</li>
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
                    <label for="formUsername">Link</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="link" class="form-control" value="<?= $post['link'] ?>" placeholder="Enter Link" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formFullName">ID</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="content_id" class="form-control" value="<?= $post['content_id'] ?>" placeholder="Enter ID">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formEmail">Class</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="content_class" class="form-control" value="<?= $post['content_class'] ?>" placeholder="Enter Class">
                  </div>
                </div>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Tên đường dẫn</label>
                  </div>
                  <div class="col-md-10">
                    <input type="text" name="name_link" class="form-control" value="<?= $post['name_link'] ?>" placeholder="Tên đường dẫn">
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formPassword">Danh mục</label>
                  </div>
                  <div class="col-md-10">
                    <select name="type" class="form-select" aria-label="Default select example">
                      <option value="onpage" <?php selected($post['type'],'onpage')?>>Onpage</option>
                      <option value="offpage" <?php selected($post['type'],'offpage')?>>Offpage</option>
                    </select>
                  </div>
                </div>

                <button type="submit" class="btn btn-primary">Cập nhật</button>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="post_id" id="post_id" value="<?= $post['ID'] ?>">
                <input type="hidden" name="user_id" id="user_id" value="<?= $user['ID'] ?>">
                <?= CSRFTokenTrait::generateCSRFTokenInput('csrf_backlink_edit_token') ?>
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