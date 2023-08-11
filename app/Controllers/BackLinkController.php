<?php

namespace App\Controllers;

use MysqliDb;
use App\Models\User;
use App\Helpers\View;
use App\Models\BackLink;
use App\Helpers\FlashMessage;
use App\Helpers\CrawlerHelper;
use App\Middleware\CSRFTokenTrait;
use App\Controllers\AuthController;
use Symfony\Component\HttpFoundation\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helpers\FileHelper;

class BackLinkController
{
    use CSRFTokenTrait;
    use FlashMessage;

    protected $db;
    protected $request;
    protected $authModel;
    protected $userModel;
    protected $backlinkModel;

    public function __construct()
    {
        $this->db = MysqliDb::getInstance();


        $userModel = new User;
        $this->userModel = $userModel;

        $authModel = new AuthController;
        $this->authModel = $authModel;
        $authModel->checkUserLogin();

        $backlinkModel = new BackLink;
        $this->backlinkModel = $backlinkModel;

        $this->request = Request::createFromGlobals();
    }

    public function index()
    {
        $keyword = $this->request->get('keyword');
        $link = $this->request->get('link');
        $name_link = $this->request->get('name_link');
        $type = $this->request->get('type');
        $get_orderby = $this->request->get('order_by');
        $explode_orderby = explode("-", $get_orderby);
        $order_by = isset($explode_orderby[0]) ? $explode_orderby[0] : '';
        $sort = isset($explode_orderby[1]) ? $explode_orderby[1] : '';

        // $sort = $this->request->get('sort');

        $search_params = [
            'keyword' => trim($keyword),
            'link' => trim($link),
            'name_link' => trim($name_link),
            'type' => $type,
            'order_by' => $order_by,
            'sort' => $sort,
        ];
        
        $page = $this->request->get('page') ?? 1;
        $perPage = 1000;

        $data = $this->backlinkModel->getBackLinksWithPagination($page, $perPage,$search_params);

        // Kiểm tra dữ liệu có tồn tại hay không
        if (empty($data['data']) && $page > 1) {
            $totalPages = $data['totalPages'];
            $nextPage = min($totalPages, $page + 1);
            $data = $this->backlinkModel->getBackLinksWithPagination($nextPage, $perPage,$search_params);
            header('Location: backlink-index?page=' . $nextPage);
            exit();
        }

        // Truyền dữ liệu phân trang cho view
        $data = [
            'list' => $data['data'],
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $data['totalPages'],
            'request' => $this->request
        ];
        
        View::render('admin/backlink/index', $data);
    }

    public function create()
    {
        $this->store();
        View::render('admin/backlink/add');
    }

    function store()
    {
        // Kiểm tra xem yêu cầu là phương thức POST hay không
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Lấy dữ liệu từ biểu mẫu
            $link = $this->request->get('link');
            $content_id = $this->request->get('content_id');
            $content_class = $this->request->get('content_class');
            $name_link = $this->request->get('name_link');
            $type = $this->request->get('type');
            $user_id = $this->userModel->getUserCurrentID();

            $data = array(
                'link' => $link,
                'content_id' => $content_id,
                'content_class' => $content_class,
                'name_link' => $name_link,
                'type' => $type,
                'user_id' => $user_id
            );

            $csrf_banklink_create_token = $this->request->get('csrf_banklink_create_token');
            $verifyCSRFToken = CSRFTokenTrait::verifyCSRFToken($csrf_banklink_create_token, 'csrf_banklink_create_token');

            if (!$verifyCSRFToken) {
                FlashMessage::setFlashMessage('error', 'verifyCSRFToken. Cập nhật thất bại.');
            }

            // Thực hiện kiểm tra và thêm người dùng vào cơ sở dữ liệu
            $result = $this->backlinkModel->addBackLink($data);
            if ($result) {
                $this->crawlerGetLink(array($result));
                // Nếu thêm thành công, đặt thông báo flash và chuyển hướng đến trang danh sách người dùng
                FlashMessage::success('Thêm bản ghi thành công.');
                // Thực hiện redirect về trang hiện tại sau khi cập nhật
                header("Location: backlink-index");
                exit;
            } else {
                // Nếu thêm thất bại, đặt thông báo flash và hiển thị lại biểu mẫu
                FlashMessage::error('Thêm bản ghi thất bại.');
            }
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }
    }

    public function edit()
    {
        $post_id = $this->request->get('post_id');

        $checkAuthor = $this->backlinkModel->checkAuthor($post_id);
        // Kiểm tra xem người dùng đã đăng nhập và có vai trò admin hay không
        if (!$checkAuthor && !$this->authModel->isRoleAdmin()) {
            // Xử lý khi người dùng không có quyền xóa
            FlashMessage::setFlashMessage('error', 'Bạn không có quyền thực hiện tác vụ này.');
            header("Location: backlink-index");
            exit;
        }

        // Call update func
        $this->update();

        $post = $this->backlinkModel->getPostByPostID($post_id);

        if (!$post) {
            die('ID không hợp lệ.');
        }

        // Hiển thị form chỉnh sửa thông tin người dùng
        View::render('admin/backlink/edit', ['post' => $post]);
    }

    public function update()
    {
        if ($this->request->isMethod('POST')) {
            $post_id = $this->request->get('post_id');
            $link = $this->request->get('link');
            $content_id = $this->request->get('content_id');
            $content_class = $this->request->get('content_class');
            $name_link = $this->request->get('name_link');
            $type = $this->request->get('type');

            $csrf_backlink_edit_token = $this->request->get('csrf_backlink_edit_token');
            $verifyCSRFToken = CSRFTokenTrait::verifyCSRFToken($csrf_backlink_edit_token, 'csrf_backlink_edit_token');

            if (!$verifyCSRFToken) {
                FlashMessage::setFlashMessage('error', 'verifyCSRFToken. Cập nhật thất bại.');
            }

            $data = array(
                'link' => $link,
                'content_id' => $content_id,
                'content_class' => $content_class,
                'name_link' => $name_link,
                'type' => $type,
            );
            $result = $this->backlinkModel->updatePost($post_id, $data);

            if ($result) {
                $this->crawlerGetLink(array($post_id));
                FlashMessage::setFlashMessage('success', 'Cập nhật bản ghi thành công.');
            } else {
                FlashMessage::setFlashMessage('error', 'Cập nhật bản ghi thất bại.');
            }
            // Thực hiện redirect về trang hiện tại sau khi cập nhật
            header("Location: backlink-index");
            exit;
        }
    }

    public function updatePassword()
    {
    }

    public function destroy()
    {
        $post_id = $this->request->get('post_id');

        $checkAuthor = $this->backlinkModel->checkAuthor($post_id);
        // Kiểm tra xem người dùng đã đăng nhập và có vai trò admin hay không
        if (!$checkAuthor && !$this->authModel->isRoleAdmin()) {
            // Xử lý khi người dùng không có quyền xóa
            FlashMessage::setFlashMessage('error', 'Bạn không có quyền thực hiện tác vụ này.');
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit;
        }

        // Xóa người dùng từ cơ sở dữ liệu
        $result = $this->backlinkModel->deletePost($post_id);

        if ($result) {
            FlashMessage::setFlashMessage('success', 'Xóa bản thành công.');
        } else {
            FlashMessage::setFlashMessage('error', 'Xóa bản thất bại.');
        }

        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }
    public function destroyMulti()
    {
        $post_ids = $this->request->get('post_ids');
        foreach ($post_ids as $post_id) {
            $checkAuthor = $this->backlinkModel->checkAuthor($post_id);
            // Kiểm tra xem người dùng đã đăng nhập và có vai trò admin hay không
            if (!$checkAuthor && !$this->authModel->isRoleAdmin()) {
                // Xử lý khi người dùng không có quyền xóa
                FlashMessage::setFlashMessage('error', 'Bạn không có quyền thực hiện tác vụ này.');
                header("Location: " . $_SERVER['HTTP_REFERER']);
                exit;
            }
            // Xóa người dùng từ cơ sở dữ liệu
            $result = $this->backlinkModel->deletePost($post_id);

            if ($result) {
                FlashMessage::setFlashMessage('success', 'Xóa bản thành công.');
            } else {
                FlashMessage::setFlashMessage('error', 'Xóa bản thất bại.');
            }
        }
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit;
    }

    function crawler()
    {
        $action = $this->request->get('action');
        $post_id = $this->request->get('post_id');
        $post_ids = $this->request->get('post_ids');
        $data = $this->request->get('data');
        $user_id = getUserCurrentID();

        // $checkAuthor = $this->backlinkModel->checkAuthor($post_id);
        // Kiểm tra xem người dùng đã đăng nhập và có vai trò admin hay không
        // if ( !$checkAuthor && !$this->authModel->isRoleAdmin()) {
        //     // Xử lý khi người dùng không có quyền xóa
        //     FlashMessage::setFlashMessage('error', 'Bạn không có quyền thực hiện tác vụ này.');
        //     header("Location: " . $_SERVER['HTTP_REFERER']);
        //     exit;
        // }

        if ($action == 'getLink') {
            $this->crawlerGetLink($post_ids);
        }
        if ($action == 'getLinkCrawlerDetail') {
            $this->getLinkCrawlerDetail($post_id);
        }
        if ($action == 'checkLink') {
            $this->crawlerCheckLink($post_ids);
        }
        if ($action == 'saveBacklinkMore') {
            $this->saveBacklinkMore($data);
        }
        if ($action == 'getDsDen') {
            $this->getDsDen($post_id, $user_id);
        }
        if ($action == 'saveDsDen') {
            $this->saveDsDen($data);
        }

        if ($action == 'getOffpageDetail') {
            $this->getOffpageDetail($post_id, $user_id);
        }
        if ($action == 'saveDetailOffPage') {
            $this->saveDetailOffPage($post_id, $data);
        }
        if ($action == 'exportExel') {
            $this->exportExel($post_id, $data);
        }
        if ($action == 'importExel') {
            $this->importExel();
        }

        if ($action == 'saveAutoDetailOffPage') {
            $this->saveAutoDetailOffPage($data);
        }
        if ($action == 'deletePostMore') {
            $this->deletePostMore($post_id);
        }
        if ($action == 'destroyMulti') {
            $this->destroyMulti();
        }
    }

    function crawlerGetLink($post_ids)
    {
        $posts = $this->backlinkModel->getPostByPostIDs($post_ids);

        $crawlerHelper = new CrawlerHelper();
        $links = [];
        foreach ($posts as $post) {
            $post_type = ($post['type']) ? $post['type'] : $post['type'];
            if ($post_type == 'onpage') {
       
                $filterSection = ($post['content_id']) ? $post['content_id'] : $post['content_class'];
                $filterLink = $post['name_link'];
                if (filter_var($post['link'], FILTER_VALIDATE_URL)) {
                    $get_links = $crawlerHelper->crawlLinks($post['link'], $filterSection, $filterLink, $post['ID']);
                    $links[] = $get_links;
                    $this->backlinkModel->updatePost($post['ID'], array('incoming_links' => count($get_links)) );
                }
            }
            
        }

        if ($links) {
            foreach ($links as $post) {
                if ($post) {
                    try {
                        $this->db->where('post_id', $post[0]['post_id'])->delete('backlinks_more');
                        $this->db->where('post_id', $post[0]['post_id'])->delete('backlinks_more_checklink');
                        $ids = $this->db->insertMulti('backlinks_more', $post);
                        if (!$ids) {
                            echo 'insert failed: ' . $this->db->getLastError();
                        } else {
                            echo 'new users inserted with following id\'s: ' . implode(', ', $ids);
                        }
                    } catch (\Throwable $th) {
                        //throw $th;
                    }
                    
                }
            }
        }
    }

    function checkArray($search_internal_links, $search_keyword, $dataArray)
    {
        $found = false;
        // Duyệt qua từng phần tử trong mảng
        foreach ($dataArray as $item) {
            // Kiểm tra nếu cặp giá trị bằng với giá trị tìm kiếm
            if ($item['internal_links'] === $search_internal_links && $item['keyword'] === $search_keyword) {
                $found = true;
                break; // Khi tìm thấy, dừng vòng lặp
            }
        }
        return $found;
    }

    function getLinkCrawlerDetail($post_id)
    {
        $posts = $this->backlinkModel->getPostMoreByPostID($post_id);
        $posts_check = $this->backlinkModel->getPostMoreCheckLinkByPostID($post_id);

        $i = 0;
        foreach ($posts as $item) {
            $selected = 0;
            if ($posts_check) {
                $selected = 1;
            } else if ($this->checkArray($item['internal_links'], $item['keyword'], $posts_check)) {
                $selected = 2;
            }
        ?>
            <tr class="js-row-autosave" data-post_id="<?= $item['ID'] ?>">
                <td>
                    <?= $item['internal_links'] ?>
                    <input type="hidden" name="ID" value="<?= $item['ID'] ?>">
                    <input type="hidden" name="internal_links" value="<?= $item['internal_links'] ?>">
                </td>
                <td>
                    <?= $item['keyword'] ?>
                    <input type="hidden" name="text" value="<?= $item['keyword'] ?>">
                </td>
                <td class="text-center align-middle pe-none">
                    <?php if ($selected == 1) : ?>
                        <i class="fa-solid fa-circle-check text-success"></i>
                    <?php elseif ($selected == 2) : ?>
                        <i class="fa-solid fa-circle-xmark text-danger"></i>
                    <?php else : ?>
                        -
                    <?php endif; ?>
                </td>
                <td><input type="text" name="title" value="<?= $item['title'] ?>" class="form-control js-input-autosave" placeholder="Enter Title"></td>
                <td><input type="text" name="note" value="<?= $item['note'] ?>" class="form-control js-input-autosave" placeholder="Enter Note"></td>
            </tr>
            <?php
            $i++;
        }
    }

    function saveBacklinkMore($data)
    {
        foreach ($data as $item) {
            $this->backlinkModel->updatePostMore($item);
        }
    }
    function crawlerCheckLink($post_ids)
    {
        $posts = $this->backlinkModel->getPostByPostIDs($post_ids);

        $crawlerHelper = new CrawlerHelper();
        $links = [];
        foreach ($posts as $post) {
            $filterSection = ($post['content_id']) ? $post['content_id'] : $post['content_class'];
            $filterLink = $post['name_link'];
            if (filter_var($post['link'], FILTER_VALIDATE_URL)) {
                $links[] = $crawlerHelper->crawlLinks($post['link'], $filterSection, $filterLink, $post['ID']);
            }
        }
        if ($links) {
            foreach ($links as $post) {
                if ($post) {
                    $this->db->where('post_id', $post[0]['post_id'])->delete('backlinks_more_checklink');
                    $ids = $this->db->insertMulti('backlinks_more_checklink', $post);
                    if (!$ids) {
                        echo 'insert failed: ' . $this->db->getLastError();
                    } else {
                        echo 'new users inserted with following id\'s: ' . implode(', ', $ids);
                    }
                }
            }
        }
    }

    function getDsDen($post_id, $user_id)
    {
        $post = $this->backlinkModel->getPostByPostID($post_id);
        if ($post) {

            $this->db->join("backlinks bl", "blm.post_id=bl.ID", "LEFT");
            $this->db->where("bl.user_id", $user_id);
            $this->db->where("bl.link", $post['link'], "!=");
            $result = $this->db
                ->where('post_id', $post_id, '!=')->where('internal_links', $post['link'])->get("backlinks_more blm", null, "blm.ID, internal_links, keyword, note, bl.link");
                foreach ($result as $item) {
            ?>
                <tr class="js-row-autosave" data-post_id="<?= $item['ID'] ?>">
                    <td>
                        <?= $item['link'] ?>
                        <input type="hidden" name="ID" value="<?= $item['ID'] ?>">
                        <input type="hidden" name="internal_links" value="<?= $item['internal_links'] ?>">
                    </td>
                    <td>
                        <?= $item['keyword'] ?>
                        <input type="hidden" name="text" value="<?= $item['keyword'] ?>">
                    </td>
                    <td><input type="text" name="note" value="<?= $item['note'] ?>" class="form-control js-input-autosave" placeholder="Enter Note"></td>
                </tr>
                <?php
            }
        }
    }

    function getOffpageDetail($post_id, $user_id)
    {
        $post = $this->backlinkModel->getPostByPostID($post_id);
        if ($post) {

            $this->db->join("backlinks bl", "blm.post_id=bl.ID", "RIGHT");
            $this->db->where("bl.user_id", $user_id);
            $result = $this->db
                ->where('post_id', $post_id)->get("backlinks_more blm", NULL,'blm.ID, blm.link_to, blm.internal_links, blm.keyword, blm.checkbox, blm.note');
            if ($result) {
                foreach ($result as $item) {
                ?>
                    <tr class="container-item js-row-autosave">
                        <td>
                            <input type="text" value="<?= $item['link_to'] ?>" name="link_to" class="form-control js-offpage-link_to js-input-autosave" placeholder="Link trỏ đến">
                        </td>
                        <td>
                            <input type="text" value="<?= $item['internal_links'] ?>" name="internal_links" class="form-control js-offpage-internal_links js-input-autosave" placeholder="Internal links">
                        </td>
                        <td>
                            <input type="text" value="<?= $item['keyword'] ?>" name="keyword" class="form-control js-offpage-keyword js-input-autosave" placeholder="Keyword">
                        </td>
                        <td class="text-center align-middle">
                            <label class="custom-checkbox">
                                <input type="checkbox" name="checkbox" <?= ($item['checkbox'] === 'true') ? 'checked' : '' ?> class="js-offpage-checkbox js-input-autosave">
                                <span class="checkmark"></span>
                            </label>
                        </td>
                        <td><input type="text" name="note" value="<?= $item['note'] ?>" class="form-control js-offpage-note js-input-autosave" placeholder="Enter Note"></td>
                        <td>
                            <a href="javascript:void(0)" class="remove-item btn btn-danger btn-sm remove-social-media"><i class="fas fa-trash"></i></a>
                            <input type="hidden" name="ID" value="<?= $item['ID'] ?>">
                        </td>
                    </tr>
                <?php
                }
            }
        }
    }

    function saveDetailOffPage($post_id, $data)
    {
        $get_post = $this->backlinkModel->getPostByPostID($post_id);

        if ($get_post && $data) {
            $this->db->where('post_id', $get_post['ID'])->delete('backlinks_more');
            $ids = $this->db->insertMulti('backlinks_more', $data);
            if (!$ids) {
                echo 'insert failed: ' . $this->db->getLastError();
            } else {
                echo 'new users inserted with following id\'s: ' . implode(', ', $ids);
            }
        }
    }

    function saveAutoDetailOffPage($data)
    {
        foreach ($data as $item) {
   
            if( isset($item['ID']) &&  $item['ID'] > 0){
                // echo 'updatePostMore';
                $this->backlinkModel->updatePostMore($item);
            }else {
                $id = $this->backlinkModel->createPostMore($item);
                // echo 'createPostMore';
                echo $id;
            }
        }

        // if ($get_post && $data) {
        //     $this->db->where('post_id', $get_post['ID'])->delete('backlinks_more');
        //     $ids = $this->db->insertMulti('backlinks_more', $data);
        //     if (!$ids) {
        //         echo 'insert failed: ' . $this->db->getLastError();
        //     } else {
        //         echo 'new users inserted with following id\'s: ' . implode(', ', $ids);
        //     }
        // }
    }

    public function deletePostMore($post_id)
    {
        // Xóa người dùng từ cơ sở dữ liệu
        $result = $this->backlinkModel->deletePostMore($post_id);
    }



    function saveDsDen($data)
    {
        foreach ($data as $item) {
            $this->backlinkModel->updatePostMoreCheckLink($item);
        }
    }

    function exportExel()
    {

        $get_data = $this->backlinkModel->getListBankLink();
        if ($get_data) {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();

            // Set document properties
            $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
                ->setLastModifiedBy('Maarten Balliauw')
                ->setTitle('Danh sách backlink')
                ->setSubject('Office 2007 XLSX Test Document')
                ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Test result file');

            // Bước 6: Tạo tiêu đề cho từng cell excel,
            // Các cell của từng row bắt đầu từ A1 B1 C1 ...
            $spreadsheet->getActiveSheet()
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'link')
                ->setCellValue('C1', 'content_id')
                ->setCellValue('D1', 'content_class')
                ->setCellValue('E1', 'name_link')
                ->setCellValue('F1', 'type');

            $rowNumber = 2;

            foreach ($get_data as $index => $item) {
                $spreadsheet->getActiveSheet()
                    ->setCellValue('A' . $rowNumber, $item['ID'])
                    ->setCellValue('B' . $rowNumber, $item['link'])
                    ->setCellValue('C' . $rowNumber, $item['content_id'])
                    ->setCellValue('D' . $rowNumber, $item['content_class'])
                    ->setCellValue('E' . $rowNumber, $item['name_link'])
                    ->setCellValue('F' . $rowNumber, $item['type']);
                $rowNumber++;
            }

            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(100);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách backlink.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        }
    }
    function importExel()
    {

        $get_data = $this->backlinkModel->getListBankLink();
        if ($get_data) {
            // Create new Spreadsheet object
            $spreadsheet = new Spreadsheet();

            // Set document properties
            $spreadsheet->getProperties()->setCreator('Maarten Balliauw')
                ->setLastModifiedBy('Maarten Balliauw')
                ->setTitle('Danh sách backlink')
                ->setSubject('Office 2007 XLSX Test Document')
                ->setDescription('Test document for Office 2007 XLSX, generated using PHP classes.')
                ->setKeywords('office 2007 openxml php')
                ->setCategory('Test result file');

            // Bước 6: Tạo tiêu đề cho từng cell excel,
            // Các cell của từng row bắt đầu từ A1 B1 C1 ...
            $spreadsheet->getActiveSheet()
                ->setCellValue('A1', 'ID')
                ->setCellValue('B1', 'link')
                ->setCellValue('C1', 'content_id')
                ->setCellValue('D1', 'content_class')
                ->setCellValue('E1', 'name_link')
                ->setCellValue('F1', 'type');

            $rowNumber = 2;

            foreach ($get_data as $index => $item) {
                $spreadsheet->getActiveSheet()
                    ->setCellValue('A' . $rowNumber, $item['ID'])
                    ->setCellValue('B' . $rowNumber, $item['link'])
                    ->setCellValue('C' . $rowNumber, $item['content_id'])
                    ->setCellValue('D' . $rowNumber, $item['content_class'])
                    ->setCellValue('E' . $rowNumber, $item['name_link'])
                    ->setCellValue('F' . $rowNumber, $item['type']);
                $rowNumber++;
            }

            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(100);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(8);

            // Set active sheet index to the first sheet, so Excel opens this as the first sheet
            $spreadsheet->setActiveSheetIndex(0);

            // Redirect output to a client’s web browser (Xlsx)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Danh sách backlink.xlsx"');
            header('Cache-Control: max-age=0');
            // If you're serving to IE 9, then the following may be needed
            header('Cache-Control: max-age=1');

            // If you're serving to IE over SSL, then the following may be needed
            header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
            header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
            header('Pragma: public'); // HTTP/1.0

            $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
            $writer->save('php://output');
            exit;
        }
    }
    function importFromCSV()
    {

        if ($this->request->isMethod('POST')) {
            if (isset($_FILES['file'])) {
                $result = FileHelper::uploadFile($this->request, 'file');
                if ($result['success']) {
                    FlashMessage::setFlashMessage('success', 'Tải file lên thành công. Đường dẫn: ' . $result['filePath']);
                    $this->importDataFromExcel($result['filePath']);

                    if (file_exists($result['filePath'])) {
                        unlink($result['filePath']);
                    }

                    header("Location: backlink-import");
                    exit;

                } else {
                    FlashMessage::setFlashMessage('error', 'Lỗi: ' . $result['message']);
                }
            }
        }
        View::render('admin/backlink/import');
    }

    function importDataFromExcel($inputFileName)
    {
        $getUserCurrentID = $this->userModel->getUserCurrentID();
        try {

            // Khởi tạo đối tượng PhpSpreadsheet để đọc dữ liệu từ tệp Excel
            $spreadsheet = IOFactory::load($inputFileName);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            // Chắc chắn rằng tệp Excel có dữ liệu để chèn
            if (count($rows) <= 1) {
                echo "Tệp Excel không có dữ liệu để chèn.";
                return;
            }

            // Loại bỏ tiêu đề (dòng đầu tiên) trong tệp Excel
            array_shift($rows);

            // Chèn dữ liệu vào cơ sở dữ liệu bằng phương thức insertMulti
            $data = array();
            foreach ($rows as $row) {
                $data[] = array(
                    'link' => $row[1],
                    'content_id' => $row[2],
                    'content_class' => $row[3],
                    'name_link' => $row[4],
                    'type' => $row[5],
                    'user_id' => $getUserCurrentID,
                    'created_at' => $this->db->now()
                );
            }
            // Thực hiện chèn dữ liệu hàng loạt (Batch Insertion) bằng phương thức insertMulti
            $ids = $this->db->insertMulti('backlinks', $data);
            if ($ids) {
                FlashMessage::setFlashMessage('success', "Chèn thành công " . count($ids) . " dòng dữ liệu từ tệp Excel vào cơ sở dữ liệu.");
            } else {
                FlashMessage::setFlashMessage('error', "Lỗi khi chèn dữ liệu.");
            }
            //code...
        } catch (\Throwable $e) {
            FlashMessage::setFlashMessage('error', "Catch Lỗi khi chèn dữ liệu.");
        }
    }
}
