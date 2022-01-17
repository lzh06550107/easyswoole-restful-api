<?php

declare(strict_types=1);

namespace App\HttpController;

use EasySwoole\Http\AbstractInterface\Controller;
use EasySwoole\HttpAnnotation\Utility\AnnotationDoc;

/**
 * doc文档控制器
 */
class Index extends Controller
{
    /**
     * api文档主页
     */
    public function index()
    {
        $doc = new AnnotationDoc();
        $string = $doc->scan2Html(EASYSWOOLE_ROOT.'/App/HttpController');
        $this->response()->withAddedHeader('Content-type', "text/html;charset=utf-8");
        $this->response()->write($string);
    }
}
