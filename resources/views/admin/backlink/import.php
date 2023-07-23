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
              <h1 class="m-0">Import backlink</h1>
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
              <form method="POST" enctype="multipart/form-data" class="mb-3 needs-validation" novalidate>
                <div class="row mb-3">
                  <div class="col-md-2">
                    <label for="formUsername">CSV file</label>
                  </div>
                  <div class="col-md-10">
                    <input type="file" name="file" id="file">
                  </div>
                </div>


                <button type="submit" class="btn btn-primary">Import</button>
                <input type="hidden" name="action" value="import">
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