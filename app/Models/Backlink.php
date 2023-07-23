<?php

namespace App\Models;
use App\Models\User;

use MysqliDb;

class BackLink
{

    protected $db;
    protected $tableName = 'backlinks';
    protected $tableNameMore = 'backlinks_more';
    protected $tableNameMoreCheckLink = 'backlinks_more_checklink';
    protected $role;
    protected $userModel;

    public function __construct()
    {
        $this->db = MysqliDb::getInstance();
        $userModel = new User;
        $this->userModel = $userModel;
    }
    public function getPostByPostID($post_id)
    {
        return $this->db->where('ID', $post_id)->getOne($this->tableName);
    }
    public function getPostMoreByPostID($post_id)
    {
        return $this->db->where('post_id', $post_id)->orderBy('ID', 'DESC')->get($this->tableNameMore);
    }
    public function getPostMoreCheckLinkByPostID($post_id)
    {
        return $this->db->where('post_id', $post_id)->orderBy('ID', 'DESC')->get($this->tableNameMoreCheckLink);
    }

    public function getPostByPostIDs($post_id)
    {
        return $this->db->where('ID', $post_id, 'IN')->orderBy('ID', 'DESC')->get($this->tableName);
    }
    public function getListBankLink()
    {
        $getUserCurrentID = $this->userModel->getUserCurrentID();
        return $this->db->where ('user_id', $getUserCurrentID)->orderBy('ID', 'DESC')->get($this->tableName);
    }
    public function getBackLinksWithPaginationRemove($currentPage, $perPage = 10)
    {
        $getUserCurrentID = $this->userModel->getUserCurrentID();
        $this->db->pageLimit = $perPage;
        $result = $this->db
        ->where ('user_id', $getUserCurrentID)->orderBy('ID', 'DESC')
        ->arraybuilder()->paginate($this->tableName, $currentPage);

        $totalPages = $this->db->totalPages;
        $totalRecords = $this->db->totalCount;
        return [
            'data' => $result,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords
        ];
    }

    public function getBackLinksWithPagination($currentPage, $perPage = 10)
    {
        $getUserCurrentID = $this->userModel->getUserCurrentID();
        $this->db->pageLimit = $perPage;

        $this->db->join("backlinks_more bm", "bm.post_id=bl.ID", "LEFT");
        $this->db->where("bl.user_id", $getUserCurrentID)->orderBy('bl.ID', 'DESC');
        $this->db->groupBy ("bl.ID");
        $result = 
        $this->db->arraybuilder()->paginate("backlinks bl", $currentPage, " bl.ID, bl.link, bl.content_id, bl.content_class, bl.name_link, bl.type, bl.user_id, COUNT(CASE WHEN bm.post_id IS NOT NULL THEN bl.ID END) AS count_more");
    
        $totalPages = $this->db->totalPages;
        $totalRecords = $this->db->totalCount;

        return [
            'data' => $result,
            'totalPages' => $totalPages,
            'totalRecords' => $totalRecords
        ];
    }

    function count_internal_links($user_id, $post_id,$post_link) {
        $this->db->join("backlinks bl", "blm.post_id=bl.ID", "LEFT");
        $this->db->where("bl.user_id", $user_id);
        $result = $this->db
            ->where('post_id', $post_id, '!=')->where('internal_links', $post_link)->get("backlinks_more blm", null, " COUNT(blm.ID) AS count_internal_links");
            return $result;
    }

    public function addBackLink($dataArray)
    {
        $data = array_intersect_key(
            $dataArray,
            [
                'link' => '',
                'content_id' => '',
                'content_class' => '',
                'name_link' => '',
                'type' => '',
                'user_id' => '',
            ]
        );
        if (empty($data)) {
            return false; // Hoặc xử lý theo nhu cầu của bạn
        }
        $data['created_at'] = $this->db->now();

        $result = $this->db->insert($this->tableName, $data);
        return $result;
    }

    function checkAuthor($post_id) {
        $getUserCurrentID = $this->userModel->getUserCurrentID();

        $this->db
        ->where('ID', $post_id)
        ->where('user_id', $getUserCurrentID);
        
        return $this->db->has($this->tableName);
    }
    public function deletePost($post_id)
    {
        $this->db->where('post_id', $post_id)->delete($this->tableNameMore);
        $this->db->where('post_id', $post_id)->delete($this->tableNameMoreCheckLink);

        // Xóa người dùng từ cơ sở dữ liệu
        $result = $this->db->where('ID', $post_id)->delete($this->tableName);
        
        return $result;
    }
    
    public function updatePost($post_id, $dataArray)
    {
        $data = array_intersect_key(
            $dataArray,
            [
                'link' => '',
                'content_id' => '',
                'content_class' => '',
                'name_link' => '',
                'type' => '',
            ]
        );

        if (empty($data)) {
            return false; // Hoặc xử lý theo nhu cầu của bạn
        }
        $data['updated_at'] = $this->db->now();
        $this->db->where('ID', $post_id);
        $this->db->update($this->tableName, $data);
        return $this->db->count;
    }
    public function updatePostMore($dataArray)
    {
        $data = array_intersect_key(
            $dataArray,
            [
                'title' => '',
                'note' => '',
                'link_to' => '',
                'internal_links' => '',
                'keyword' => '',
                'checkbox' => '',
            ]
        );
        if (empty($data)) {
            return false; // Hoặc xử lý theo nhu cầu của bạn
        }
        $data['updated_at'] = $this->db->now();
        $this->db->where('ID', $dataArray['ID']);
        $this->db->update($this->tableNameMore, $data);
        return $this->db->count;
    }
    public function createPostMore($dataArray)
    {
        $data = array_intersect_key(
            $dataArray,
            [
                'title' => '',
                'note' => '',
                'post_id' => '',
                'link_to' => '',
                'internal_links' => '',
                'keyword' => '',
                'checkbox' => '',
            ]
        );
        if (empty($data)) {
            return false; // Hoặc xử lý theo nhu cầu của bạn
        }
        $data['created_at'] = $this->db->now();
        $result = $this->db->insert($this->tableNameMore, $data);
        return $result;
    }
    public function deletePostMore($post_id)
    {
        $result = $this->db->where('ID', $post_id)->delete($this->tableNameMore);
        return $result;
    }

    public function updatePostMoreCheckLink($dataArray)
    {
        $data = array_intersect_key(
            $dataArray,
            [
                'note' => '',
            ]
        );

        if (empty($data)) {
            return false; // Hoặc xử lý theo nhu cầu của bạn
        }
        $data['updated_at'] = $this->db->now();
        $this->db->where('ID', $dataArray['ID']);
        $this->db->update($this->tableNameMore, $data);
        return $this->db->count;
    }

}
