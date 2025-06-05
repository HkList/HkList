<?php

namespace App\Http\Controllers\FreeParsers;

use App\Http\Controllers\Api\BDWPApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ParseController;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UtilsController;
use Illuminate\Http\Request;

class V0Controller extends Controller
{
    public static function request(Request $request)
    {
        $cookie = ParseController::getRandomCookie($request);
        $cookieData = $cookie->getData(true);
        if ($cookieData["code"] !== 200) return $cookie;
        $account = $cookieData["data"];
        $cookie = $account["account_data"]["cookie"];

        $saveToDisk = BDWPApiController::saveToDiskWeb(
            $cookie,
            $request["shareid"],
            $request["fs_id"],
            $request["uk"],
            $request["randsk"],
            "https://pan.baidu.com/s/$request[surl]"
        );
        $saveToDiskData = $saveToDisk->getData(true);
        if ($saveToDiskData["code"] !== 200) {
            if (!str_contains($saveToDiskData["message"], "errno: 2")) {
                UtilsController::banAccount(
                    "V0Controller::saveToDisk",
                    $saveToDiskData["message"],
                    $account["id"]
                );
            }
            return $saveToDisk;
        }

        $ua = $request["token"] === "guest" ? config("hklist.parse.guest_user_agent") : config("hklist.parse.token_user_agent");
        $list = $saveToDiskData["data"];
        $urls = [];
        foreach ($list as $item) {
            $dlink = BDWPApiController::downloadByDisk($cookie, $item["to"], $ua);
            $dlinkData = $dlink->getData(true);

            $filename = str_replace("/我的资源/", "", $item["to"]);
            $fs_id = $item["from_fs_id"];
            $message = $dlinkData["message"];
            $url = [
                "message" => $message,
                "filename" => $filename,
                "fs_id" => $fs_id,
                "ua" => $ua,
                "account_id" => $account["id"],
                "urls" => []
            ];

            if ($dlinkData["code"] !== 200) {
                UtilsController::banAccount(
                    "V0Controller::downloadByDisk",
                    $dlinkData["message"],
                    $account["id"]
                );
            } else {
                // 请求成功 则 赋值 urls
                $url["urls"] = array_map(fn($url) => $url . "&origin=dlna", $dlinkData["data"]["urls"]);
            }

            $urls[] = $url;
        }

        return ResponseController::success($urls);
    }
}
