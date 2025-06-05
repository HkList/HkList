<?php

use App\Http\Controllers\UtilsController;

// 2.2.27 更新新的UA
$ua = config("hklist.parse.user_agent");
if ($ua === "netdisk;P2SP;3.0.20.88") {
    UtilsController::updateEnv([
        "HKLIST_USER_AGENT" => "netdisk;P2SP;3.0.20.138"
    ]);
}