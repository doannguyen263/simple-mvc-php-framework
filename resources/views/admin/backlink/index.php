<?php

use App\Helpers\View;
use App\Helpers\FlashMessage;
use App\Helpers\Pagination;
use App\Models\BackLink;

$currentUser = $_SESSION['user'];

$backlinkModel = new BackLink;

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
              <h1 class="me-2">Danh sách</h1>
              <a class="btn btn-info btn-sm me-2" href="<?= SITE_URL ?>/backlink-create">
                <i class="fa-solid fa-user-plus"></i>
                </i>
                Thêm Backlink
              </a>

              <button class="btn btn-info btn-sm js-get-link me-2">
                <i class="fa-brands fa-searchengin"></i>
                </i>
                Get link
              </button>
              <button class="btn btn-info btn-sm js-check-link me-2">
                <i class="fa-solid fa-list-check"></i>
                </i>
                Check link
              </button>
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

      <div class="content content-backlink">
        <?php
        if (FlashMessage::hasFlashMessage()) {
          echo '<div class="mb-3">';
          FlashMessage::displayFlashMessage();
          echo '</div>';
        }
        ?>
        <?php if ($data['list']) : ?>
          <div class="card p-2">
            <div class="card-body p-0  card-overflow">
            <div id="toolbar">
              <button id="remove" class="btn btn-danger" disabled="">
                <i class="fa fa-trash" aria-hidden="true"></i> Delete
              </button>
            </div>
              <table id="table-list" class="table projects dntheme-list-table">
                <thead class="text-nowrap">
                  <tr>
                    <th class="text-center" style="width: 44px;">STT</th>
                    <th style="width: 1%" id="cb" class="manage-column column-cb check-column">
                      <label class="custom-checkbox">
                        <input id="cb-select-all-1" type="checkbox">
                        <span class="checkmark"></span>
                      </label>
                    </th>
                    <th style="width:30%">
                      Link
                    </th>
                    <th>
                      ID
                    </th>
                    <th style="width:15%">
                      Class
                    </th>
                    <th style="width:20%">
                      Tên đường dẫn
                    </th>
                    <th style="width: 86px;">
                      Danh mục
                    </th>
                    <th>SL Đi</th>
                    <th>SL Đến</th>
                    <th class="text-center" style="width: 100px">Action</th>
                  </tr>
                </thead>
                <tbody class="text-break">
                  <?php
                  $i = 0;
                  if ($page > 1) {
                    $i = $i + $page * $perPage - $perPage;
                  }
                  foreach ($data['list'] as $item) : $i++;
                  ?>
                    <tr class="cursor-pointer js-tr tr-<?= $item['type'] ?>" data-post_id=<?= $item['ID'] ?>>
                      <td class="text-center"><?= $i; ?></td>
                      <td scope="row" class="check-column">
                        <label class="custom-checkbox">
                          <input id="cb-select-<?= $item['ID'] ?>" type="checkbox" name="post_id" value="<?= $item['ID'] ?>">
                          <span class="checkmark"></span>
                        </label>
                      </td>
                      <td>
                        <?= $item['link'] ?>
                      </td>
                      <td>
                        <?= $item['content_id'] ?>
                      </td>
                      <td>
                        <?= $item['content_class'] ?>
                      </td>
                      <td>
                        <?= $item['name_link'] ?>
                      </td>
                      <td>
                        <?= $item['type'] ?>
                      </td>
                      <td>
                        <?= isset($item['count_more']) ? $item['count_more']  : 0 ?>
                      </td>
                      <td>
                        <?php
                        $count_internal_links = $backlinkModel->count_internal_links($currentUser['ID'], $item['ID'], $item['link']);
                        echo isset($count_internal_links[0]['count_internal_links']) ? $count_internal_links[0]['count_internal_links'] : 0;
                        ?>
                      </td>
                      <td class="project-actions text-right">
                        <a class="btn btn-info btn-sm" href="<?= SITE_URL ?>/backlink-edit?post_id=<?= $item['ID'] ?>">
                          <i class="fas fa-pencil-alt">
                          </i>
                        </a>
                        <button class="btn btn-danger btn-sm js-delete" data-link="<?= SITE_URL ?>/backlink-delete?post_id=<?= $item['ID'] ?>">
                          <i class="fas fa-trash">
                          </i>
                        </button>
                      </td>

                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
            <div class="pagination mt-3">
              <?= Pagination::render($totalPages) ?>
            </div>

          </div>

          <div class="card p-2 js-list-tab">
            <div class="table-list__tab onpage pb-3 card-overflow">
              <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                  <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home-tab-pane" type="button" role="tab" aria-controls="home-tab-pane" aria-selected="true">Danh sách đi</button>
                </li>
                <li class="nav-item" role="presentation">
                  <button class="nav-link js-get-ds-den" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile-tab-pane" type="button" role="tab" aria-controls="profile-tab-pane" aria-selected="false">Danh sách đến</button>
                </li>
              </ul>
              <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="home-tab-pane" role="tabpanel" aria-labelledby="home-tab" tabindex="0">
                  <table id="table-child" class="table table-bordered" data-post_id="">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 25%;">Internal Links</th>
                        <th scope="col" style="width: 25%;">Keyword</th>
                        <th scope="col" style="width: 90px;">CheckLink</th>
                        <th scope="col">Title</th>
                        <th scope="col">Note</th>
                      </tr>
                    </thead>
                    <tbody class="text-break js-dsd-content js-content-autosave" data-func="saveBacklinkMore">
                      <tr>
                        <td colspan="5">No data</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
                <div class="tab-pane fade" id="profile-tab-pane" role="tabpanel" aria-labelledby="profile-tab" tabindex="0">
                  <table id="table-child2" class="table table-bordered" data-post_id="">
                    <thead>
                      <tr>
                        <th scope="col" style="width: 25%;">Link trỏ đến</th>
                        <th scope="col" style="width: 25%;">Keyword</th>
                        <th scope="col" style="width: 90px;">Note</th>
                      </tr>
                    </thead>
                    <tbody class="text-break js-dsden-content js-content-autosave" data-func="saveDsDen">
                      <tr>
                        <td colspan="5">No data</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
            <div class="table-list__tab offpage pb-3 card-overflow">

              <table id="table-child-offpage" class="table table-bordered" data-post_id="">
                <thead>
                  <tr>
                    <th scope="col" style="width: 25%;">Links trỏ đến</th>
                    <th scope="col" style="width: 25%;">Internal Links</th>
                    <th scope="col" style="width: 25%;">Keyword</th>
                    <th scope="col" style="width: 90px;">CheckLink</th>
                    <th scope="col">Note</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody class="text-break js-offpage-content js-content-autosave" data-func="saveAutoDetailOffPage">

                  <tr class="container-item js-row-autosave">
                    <td>
                      <input type="text" name="link_to" class="form-control js-offpage-link_to js-input-autosave" placeholder="Link trỏ đến">
                    </td>
                    <td>
                      <input type="text" name="internal_links" class="form-control js-offpage-internal_links js-input-autosave" placeholder="Internal links">
                    </td>
                    <td>
                      <input type="text" name="keyword" class="form-control js-offpage-keyword js-input-autosave" placeholder="Keyword">
                    </td>
                    <td class="text-center align-middle">
                      <label class="custom-checkbox">
                        <input type="checkbox" name="checkbox" class="js-offpage-checkbox js-input-autosave">
                        <span class="checkmark"></span>
                      </label>
                    </td>
                    <td><input type="text" name="note" class="form-control js-offpage-note js-input-autosave" placeholder="Enter Note"></td>
                    <td>
                      <a href="javascript:void(0)" class="remove-item btn btn-danger btn-sm remove-social-media"><i class="fas fa-trash"></i></a>
                      <input type="hidden" name="ID">
                    </td>
                  </tr>

                </tbody>
              </table>

              <div class="d-flex">
                <a href="javascript:;" class="btn btn-info btn-sm me-2" id="add-more"><i class="fa fa-plus"></i>
                  Thêm hàng</a>
              </div>

            </div>

          </div>
        <?php else : ?>
          <div class="alert alert-info mt-3 mx-3">No data</div>
        <?php endif; ?>
      </div>
    </div>
  </div>



  <?php ob_start(); ?>
  <script src="<?= URL_PUBLIC_ADMIN . '/js/cloneData.js' ?>"></script>
  <script>
    $('a#add-more').cloneData({
      mainContainerId: 'table-child-offpage', // Main container Should be ID
      cloneContainer: 'container-item', // Which you want to clone
      removeButtonClass: 'remove-item', // Remove button for remove cloned HTML
      removeConfirm: true, // default true confirm before delete clone item
      removeConfirmMessage: 'Are you sure want to delete?', // confirm delete message
      //append: '<a href="javascript:void(0)" class="remove-item btn btn-sm btn-danger remove-social-media">Remove</a>', // Set extra HTML append to clone HTML
      minLimit: 0, // Default 1 set minimum clone HTML required
      maxLimit: 100, // Default unlimited or set maximum limit of clone HTML
      defaultRender: 1, // Number of clone items rendered by default 
      init: function() {
        console.info(':: Initialize Plugin ::');
      },
      beforeRender: function() {
        console.info(':: Before rendered callback called');
        return false;
      },
      afterRender: function() {
        console.info(':: After rendered callback called');
      },
      afterRemove: function(event) {
        var backlinkMoreID = $(this).find('input[name="ID"]').val()
        if (backlinkMoreID) {
          $.ajax({
            url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
            method: 'POST',
            dataType: 'html',
            data: {
              action: "deletePostMore",
              post_id: backlinkMoreID
            },
            success: function(response) {},
            error: function(xhr, status, error) {
              // Xử lý lỗi tại đây
              console.log(error);
            },
            complete: function() {
              $('.js-get-link').removeClass("disabled is-loading")
            }
          });
        }
        console.log( backlinkMoreID );
        console.warn(':: After remove callback called');

      },
      beforeRemove: function() {
        console.warn(':: Before remove callback called');
      }
    });
  </script>
  <?php $addBeforeFooter = ob_get_clean();
  View::addBeforeFooter($addBeforeFooter); ?>


  <?php ob_start(); ?>
  <script>
    // Action delete
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

    // Action all delete
    $('#table-list').change(function() {
      var checkedCheckboxes = $('#table-list .js-tr input[name="post_id"]:checked');
        var checkedValues = checkedCheckboxes.map(function() {
          return $(this).val();
        }).get();
        if (checkedValues.length > 0) { 
          $('#remove').attr("disabled",false)
        }else{
          $('#remove').attr("disabled",true)
        }
    })

    $('#remove').on("click", function(e) {
      var checkedCheckboxes = $('.js-tr input[name="post_id"]:checked');
      var checkedValues = checkedCheckboxes.map(function() {
        return $(this).val();
      }).get();
   
      if (checkedValues.length > 0) { 
        let post_ids = checkedValues
        $(this).addClass("disabled is-loading")
        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "destroyMulti",
            post_ids: post_ids
          },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Xóa Thành Công!',
              showConfirmButton: false,
              timer: 1500
            })
            setTimeout(function() {
              location.reload();
            }, 1500)
          },
          error: function(xhr, status, error) {
            // Xử lý lỗi tại đây
            console.log(error);
          },
          complete: function() {
            $('#remove').removeClass("disabled is-loading")
          }
        });
      }
    })
    
    // Action get link
    $('.js-get-link').click(function() {

      var checkedCheckboxes = $('.tr-onpage input[name="post_id"]:checked');
      // Lưu giá trị của các checkbox đã chọn vào một mảng riêng (nếu cần)
      var checkedValues = checkedCheckboxes.map(function() {
        return $(this).val();
      }).get();

      if (checkedValues.length > 0) {
        $(this).addClass("disabled is-loading")
        let post_ids = checkedValues
        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "getLink",
            post_ids: post_ids
          },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'GET LINK Thành Công!',
              showConfirmButton: false,
              timer: 1500
            })

          },
          error: function(xhr, status, error) {
            // Xử lý lỗi tại đây
            console.log(error);
          },
          complete: function() {
            $('.js-get-link').removeClass("disabled is-loading")
            setTimeout(function() {
              location.reload();
            }, 1500)
          }
        });
      } else {
        Swal.fire({
          icon: 'success',
          title: 'Không có bài nào được chọn',
          showConfirmButton: false,
          timer: 1500
        })
      }

    })

    // Action get backlink more detail
    $('.js-tr').click(function(e) {
      $('.js-tr').removeClass('table-active')
      $(this).addClass('table-active')
      var post_id = $(this).data('post_id');
      var type = $(this).hasClass('tr-onpage') ? 'onepage' : 'offpage'
      $('.table-list__tab').data("post_id", post_id)
      $('.js-list-tab .table-list__tab').removeClass('active')

      if (type == 'onepage') {
        $('.js-list-tab .table-list__tab.onpage').addClass('active')
        $('#home-tab').click()
        // Get danh sach di
        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "getLinkCrawlerDetail",
            post_id: post_id
          },
          success: function(response) {
            $('#table-child tbody').html(response)
          },
          error: function(xhr, status, error) {
            // Xử lý lỗi tại đây
            console.log(error);
          }
        });

        // Get danh sach den
        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "getDsDen",
            post_id: post_id
          },
          success: function(response) {
            $('.js-dsden-content').html(response)
          },
          error: function(xhr, status, error) {
            // Xử lý lỗi tại đây
            console.log(error);
          },
          complete: function() {
            $('.js-get-ds-den').removeClass("disabled is-loading")
          }
        });
      } else {
        $('.js-list-tab .table-list__tab.offpage').addClass('active')

        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "getOffpageDetail",
            post_id: post_id
          },
          success: function(response) {
            $('#table-child-offpage tbody').html(response)
          },
          error: function(xhr, status, error) {
            // Xử lý lỗi tại đây
            console.log(error);
          }
        });
      }
    })

    // Lưu backlink more
    $('.js-offpage-save-change').click(function() {
      var post_id = $('.table-list__tab.offpage').data("post_id")

      var data = [];
      if (post_id) {
        // Lặp qua từng hàng trong bảng và thu thập dữ liệu
        $('.js-offpage-content tr').each(function() {
          var row = {};

          row.link_to = $(this).find('.js-offpage-link_to').val();
          row.internal_links = $(this).find('.js-offpage-internal_links').val();
          row.keyword = $(this).find('.js-offpage-keyword').val();
          row.checkbox = $(this).find('.js-offpage-checkbox').prop('checked');
          row.note = $(this).find('.js-offpage-note').val();
          // Check if all values in the row object are null or empty strings
          var allNull = Object.values(row).every(value => value === null || value === '' || value === false);
          // Only push the row if not all values are null or empty strings

          row.post_id = post_id;

          if (!allNull) {
            data.push(row);
          }

        });

        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "saveDetailOffPage",
            post_id: post_id,
            data: data,
          },
          success: function(response) {
            Swal.fire({
              icon: 'success',
              title: 'Lưu Thành Công!',
              showConfirmButton: false,
              timer: 1500
            })
          },
          error: function(xhr, status, error) {
            // Xử lý lỗi tại đây
            console.log(error);
          }
        });
      } else {
        Swal.fire({
          icon: 'success',
          title: 'Vui lòng chọn bài viết',
          showConfirmButton: false,
          timer: 1500
        })
      }

    });
    // Action check link
    $('.js-check-link').click(function() {
      var checkedCheckboxes = $('.tr-onpage input[name="post_id"]:checked');
      // Lưu giá trị của các checkbox đã chọn vào một mảng riêng (nếu cần)
      var checkedValues = checkedCheckboxes.map(function() {
        return $(this).val();
      }).get();

      if (checkedValues.length > 0) {
        $(this).addClass("disabled is-loading")
        let post_ids = checkedValues
        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "checkLink",
            post_ids: post_ids
          },
          success: function(response) {

            Swal.fire({
              icon: 'success',
              title: 'CHECK LINK Thành Công!',
              showConfirmButton: false,
              timer: 1500
            })
            setTimeout(function() {
              location.reload();
            }, 1500)

          },
          error: function(xhr, status, error) {
            // Xử lý lỗi tại đây
            console.log(error);
          },
          complete: function() {
            $('.js-check-link').removeClass("disabled is-loading")
          }
        });
      } else {
        Swal.fire({
          icon: 'success',
          title: 'Không có bài nào được chọn',
          showConfirmButton: false,
          timer: 1500
        })
      }

    })

    // Auto save
    let timeoutIds = new Map();

    function startTyping(event) {
      const parentElement = $(event.target).closest('.js-row-autosave').data("post_id");
      clearTimeout(timeoutIds.get(parentElement));
    }

    function saveData(event) {
      const callFunc = $(event.target).closest('.js-content-autosave').data('func')
      const parentElement = $(event.target).closest('.js-row-autosave');

      if (callFunc == 'saveBacklinkMore') {
        var data = [];
        var row = {};
        row.ID = parentElement.find('input[name="ID"]').val();
        row.internal_links = parentElement.find('input[name="internal_links"]').val();
        row.text = parentElement.find('input[name="text"]').val();
        row.title = parentElement.find('input[name="title"]').val();
        row.note = parentElement.find('input[name="note"]').val();
        data.push(row);

        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "saveBacklinkMore",
            data: data,
          },
          beforeSend: function(xhr) {
            parentElement.addClass('is-sending')
          },
          error: function(xhr, status, error) {
            parentElement.addClass('is-done')
            // Xử lý lỗi tại đây
            console.log(error);
          },
          complete: (function() {
            parentElement.removeClass('is-sending')
            parentElement.addClass('is-done')
            setTimeout(function() {
              parentElement.removeClass('is-done')
            }, 1000)
          })
        });
      }

      if (callFunc == 'saveDsDen') {
        console.log('saveDsDen');

        var data = [];
        var row = {};
        row.ID = parentElement.find('input[name="ID"]').val();
        row.note = parentElement.find('input[name="note"]').val();
        data.push(row);

        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "saveDsDen",
            data: data,
          },
          beforeSend: function(xhr) {
            parentElement.addClass('is-sending')
          },
          error: function(xhr, status, error) {
            parentElement.addClass('is-done')
            // Xử lý lỗi tại đây
            console.log(error);
          },
          complete: (function() {
            parentElement.removeClass('is-sending')
            parentElement.addClass('is-done')
            setTimeout(function() {
              parentElement.removeClass('is-done')
            }, 1000)
          })
        });

      }

      if (callFunc == 'saveAutoDetailOffPage') {
        console.log('saveAutoDetailOffPage');
        console.log(parentElement.html());
        var post_id = $('.table-list__tab.offpage').data("post_id")
        var data = [];
        var row = {};
        row.ID = parentElement.find('input[name="ID"]').val();
        row.link_to = parentElement.find('input[name="link_to"]').val();
        row.post_id = post_id;
        row.internal_links = parentElement.find('input[name="internal_links"]').val();
        row.keyword = parentElement.find('input[name="keyword"]').val();
        row.checkbox = parentElement.find('input[name="checkbox"]').prop('checked');
        row.note = parentElement.find('input[name="note"]').val();
        data.push(row);
        $.ajax({
          url: '<?= SITE_URL . '/backlink-crawler' ?>', // Thay thế bằng URL của endpoint AJAX của bạn
          method: 'POST',
          dataType: 'html',
          data: {
            action: "saveAutoDetailOffPage",
            data: data,
          },
          beforeSend: function(xhr) {
            parentElement.addClass('is-sending')
          },
          error: function(xhr, status, error) {
            parentElement.addClass('is-done')
            // Xử lý lỗi tại đây
            console.log(error);
          },
          complete: (function(post_id) {
            parentElement.find('input[name="ID"]').val(post_id.responseText);
            parentElement.removeClass('is-sending')
            parentElement.addClass('is-done')
            setTimeout(function() {
              parentElement.removeClass('is-done')
            }, 1000)
          })
        });

      }

      console.log(`Data from ${parentElement} saved!`);
    }

    function onInputChange(event) {
      const inputElement = $(event.target);
      if (inputElement.hasClass('js-input-autosave')) {
        const parentElement = inputElement.closest('.js-row-autosave').data("post_id");
        startTyping(event);
        const timeoutId = setTimeout(() => saveData(event), 2000);
        timeoutIds.set(parentElement, timeoutId);
      }
    }

    $(document).ready(function() {
      $('.js-content-autosave').on('keydown', onInputChange);

      $('.js-content-autosave.js-ds-den').on('keydown', onInputChange);
    });
  </script>
  <!-- code -->
  <?php $addAfterFooter = ob_get_clean();
  View::addAfterFooter($addAfterFooter);
  View::renderFooter();
  ?>