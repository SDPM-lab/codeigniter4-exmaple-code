<?php

namespace App\Entities;

use CodeIgniter\Entity\Entity;

class TodoListsEntity extends Entity
{
    /**
     * 主鍵
     *
     * @var int
     */
    protected $t_key;

    /**
     * 標題
     *
     * @var string
     */
    protected $t_title;

    /**
     * 內容
     *
     * @var string
     */
    protected $t_content;

    /**
     * 成員鍵
     *
     * @var int
     */
    protected $m_key;

    /**
     * 新增時間
     *
     * @var datetime
     */
    protected $created_at;

    /**
     * 更新時間
     *
     * @var datetime
     */
    protected $updated_at;

    /**
     * 刪除時間
     *
     * @var datetime
     */
    protected $deleted_at;

    /**
     * 類別
     *
     * @var string
     */
    protected $t_category;

    /**
     * 開始時間
     *
     * @var datetime
     */
    protected $t_begin_at;

    /**
     * 結束時間
     *
     * @var datetime
     */
    protected $t_end_at;
}
