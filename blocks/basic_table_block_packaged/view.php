<?php

use Concrete\Core\Page\Page;
use Concrete\Core\User\User;

defined('C5_EXECUTE') or die("Access Denied.");
$u = new User();
$uID = $u->getUserID();
$c = Page::getCurrentPage();

$canAccessComposer = false;
if (is_object($composer)) {
    $ccp = new Permissions($composer);
    if ($ccp->canAccessComposer()) {
        $canAccessComposer = true;
    }
}




