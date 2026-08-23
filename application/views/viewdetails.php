<?php
/**
 * Created by PhpStorm.
 * User: bagus
 * Date: 23/8/26
 * Time: 8:23
 */
switch ($mode) {
    default:
    case "index":
        $p = New Layout("details", "{subTitle}", "application/template/viewdetails.html");
        $p->addTags(array('content'=>$content));
        $p->render();
        break;
}