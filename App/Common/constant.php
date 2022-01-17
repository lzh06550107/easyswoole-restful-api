<?php

declare(strict_types=1);

/**
 * 常量定义文件
 * Created on 2021/8/10 15:50
 * Create by LZH
 */

// 数据库前缀
!defined('DB_PREFIX') && define('DB_PREFIX', 'sc_');

!defined('IMG_URL') && define('IMG_URL', 'http://images.smart_campus.cn');

// 文件上传路径
!defined('ATTACHMENT_PATH') && define('ATTACHMENT_PATH', EASYSWOOLE_ROOT.'/uploads');
!defined('IMG_PATH') && define('IMG_PATH', ATTACHMENT_PATH . "/images");
!defined('UPLOAD_TEMP_PATH') && define('UPLOAD_TEMP_PATH', ATTACHMENT_PATH . '/temp');
