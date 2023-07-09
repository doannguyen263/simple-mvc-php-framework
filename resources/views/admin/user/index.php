<?php

use App\Helpers\View;
use App\Helpers\FlashMessage;
use App\Helpers\Pagination;

$currentUser = $_SESSION['user'];
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
            <div class="col-sm-6 d-flex align-items-center">
              <h1 class="me-3">Danh sách</h1>
              <a class="btn btn-info btn-sm" href="<?= SITE_URL ?>/user-create">
                <i class="fa-solid fa-user-plus"></i>
                </i>
                Thêm User
              </a>
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
        <?php
        if (FlashMessage::hasFlashMessage()) {
          echo '<div class="mb-3">';
          FlashMessage::displayFlashMessage();
          echo '</div>';
        }
        ?>
        <div class="card">
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
                <?php foreach ($data['users'] as $user) :
                ?>
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

                      <a class="btn btn-info btn-sm" href="<?= SITE_URL ?>/user-edit?user_id=<?= $user['ID'] ?>">
                        <i class="fas fa-pencil-alt">
                        </i>
                        Sửa
                      </a>

                      <?php if ($currentUser['ID'] == $user['ID']) : ?>
                        <button class="btn btn-danger btn-sm" disabled>
                          <i class="fas fa-trash">
                          </i>
                          Xóa
                        </button>
                      <?php else : ?>
                        <button class="btn btn-danger btn-sm js-delete" data-link="<?= SITE_URL ?>/user-delete?user_id=<?= $user['ID'] ?>">
                          <i class="fas fa-trash">
                          </i>
                          Xóa
                        </button>
                      <?php endif; ?>
                    </td>
                  </tr>
                <?php endforeach; ?>

              </tbody>
            </table>

          </div>

        </div>
        <div class="pagination mt-3">
          <?= Pagination::render($totalPages) ?>
        </div>
      </div>

    </div>
  </div>
</div>

<?php ob_start(); ?>
<script>
  $('.js-delete').on("click", function(e) {
    var deleteLink = $(this).data('link');
    Swal.fire({
      title: 'Bạn có chắc là muốn xóa?',
      text: "Bạn sẽ không thể hoàn nguyên điều này!",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#3085d6',
      cancelButtonColor: '#d33',
      confirmButtonText: 'Vâng, xóa nó!',
      cancelButtonText: 'Không xóa'
    }).then((result) => {
      if (result.isConfirmed) {
        window.location.href = deleteLink;
      }
    })
  })
</script>
<?php
$addAfterFooter = ob_get_clean();
View::addAfterFooter($addAfterFooter);
View::renderFooter();
?>