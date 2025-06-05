<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UtilsController;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Log;

class BDWPApiController extends Controller
{
    /**
     * {
     *     "avatar_url":"https://dss0.bdstatic.com/xxx",
     *     "baidu_name":"xxx",
     *     "netdisk_name":"xxxx",
     *     "uk":0,
     *     "vip_type":0
     * }
     */
    public static function getAccountInfo($accountType, $cookieOrAccessToken)
    {
        $req = [
            "headers" => ["User-Agent" => config("hklist.fake_user_agent")],
            "query" => ["method" => "uinfo"]
        ];

        if ($accountType === "cookie") {
            $req["headers"]["Cookie"] = $cookieOrAccessToken;
        } else if ($accountType === "open_platform") {
            $req["query"]["access_token"] = $cookieOrAccessToken;
        } else {
            return ResponseController::paramsError([
                "errors" => [
                    "accountType" => [
                        "The accountType is invalid."
                    ]
                ]
            ]);
        }

        $res = UtilsController::sendRequest(
            "BDWPApiController::getAccountInfo",
            "get",
            "https://pan.baidu.com/rest/2.0/xpan/nas",
            $req
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $userInfo = $data["data"];
        if (
            !isset($userInfo["errno"]) ||
            $userInfo["errno"] !== 0
        ) {
            return ResponseController::getAccountInfoFailed($userInfo["errno"] ?? "未知", $userInfo["errmsg"] ?? "未知");
        }

        return ResponseController::success([
            "avatar_url" => $userInfo["avatar_url"],
            "baidu_name" => $userInfo["baidu_name"],
            "netdisk_name" => $userInfo["netdisk_name"],
            "uk" => $userInfo["uk"],
            "vip_type" => $userInfo["vip_type"],
        ]);
    }

    /**
     * {
     *      "vip_type": "超级会员",
     *      "expires_at": 100
     * }
     */
    public static function getSvipEndAt($accountType, $cookieOrAccessToken)
    {
        $req = [
            "headers" => ["User-Agent" => config("hklist.fake_user_agent")],
            "query" => ["method" => "query"]
        ];

        if ($accountType === "cookie") {
            $req["headers"]["Cookie"] = $cookieOrAccessToken;
        } else if ($accountType === "open_platform") {
            $req["query"]["access_token"] = $cookieOrAccessToken;
        } else {
            return ResponseController::paramsError([
                "errors" => [
                    "accountType" => [
                        "The accountType is invalid."
                    ]
                ]
            ]);
        }

        $res = UtilsController::sendRequest(
            "BDWPApiController::getSvipEndAt",
            "get",
            "https://pan.baidu.com/rest/2.0/membership/user",
            $req
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $userInfo = $data["data"];
        if (
            !isset($userInfo["error_code"]) ||
            $userInfo["error_code"] !== 0
        ) {
            return ResponseController::getSvipAtFailed($userInfo["error_code"] ?? "未知", $userInfo["error_msg"] ?? "未知");
        }

        $current_product = $userInfo["current_product_v2"];
        $reminder = $userInfo["reminder"];
        if (!isset($current_product["detail_cluster"])) {
            $vip_type = "普通用户";
            $expires_at = 0;
        } else if ($current_product["detail_cluster"] == "svip") {
            $vip_type = "超级会员";
            $expires_at = $reminder["serverTime"] + $reminder["svip"]["leftseconds"];
        } else {
            $vip_type = "普通会员";
            $expires_at = $reminder["serverTime"] + $reminder["vip"]["leftseconds"];
        }

        return ResponseController::success([
            "vip_type" => $vip_type,
            "expires_at" => $expires_at
        ]);
    }

    /**
     * {
     *      "expires_at": 2592000,
     *      "refresh_token": "xxxx",
     *      "access_token": "xxx",
     * }
     */
    public static function getAccessToken($refreshToken)
    {
        $res = UtilsController::sendRequest(
            "BDWPApiController::getAccessToken",
            "get",
            "https://openapi.baidu.com/oauth/2.0/token",
            [
                "query" => [
                    "grant_type" => "refresh_token",
                    "refresh_token" => $refreshToken,
                    // alist
                    "client_id" => "iYCeC9g08h5vuP9UqvPHKKSVrKFXGa1v",
                    "client_secret" => "jXiFMOPVPCWlO2M5CwWQzffpNPaGTRBG"
                ]
            ]
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $accessToken = $data["data"];
        if (
            isset($accessToken["error"]) ||
            isset($accessToken["error_description"])
        ) {
            return ResponseController::getAccessTokenFailed($accessToken["error"] ?? "未知", $accessToken["error_description"] ?? "未知");
        }

        return ResponseController::success([
            "expires_at" => now()->addSeconds($accessToken["expires_in"])->getTimestamp(),
            "access_token" => $accessToken["access_token"],
            "refresh_token" => $accessToken["refresh_token"],
        ]);
    }

    public static function getEnterpriseInfo($cookie)
    {
        $res = UtilsController::sendRequest(
            "BDWPApiController::getEnterpriseInfo",
            "get",
            "https://pan.baidu.com/mid_enterprise_v2/api/enterprise/organization/allorganizationinfo",
            [
                "query" => [
                    "clienttype" => 0,
                    "app_id" => 24029990
                ],
                "headers" => [
                    "User-Agent" => config("hklist.fake_user_agent"),
                    "Cookie" => $cookie
                ]
            ]
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $enterpriseInfo = $data["data"];
        if (
            !isset($enterpriseInfo["errno"]) ||
            $enterpriseInfo["errno"] !== 0
        ) {
            return ResponseController::getEnterpriseInfoFailed($enterpriseInfo["errno"] ?? "未知", $enterpriseInfo["show_msg"] ?? "未知");
        }

        if (!isset($enterpriseInfo["data"][0])) {
            return ResponseController::getEnterpriseInfoFailed(999, "账号可能不是企业账号,获取cid失败");
        }

        $orgInfo = collect($enterpriseInfo["data"])->map(fn($item) => [
            "cid" => $item["cid"],
            "org_name" => $item["orgInfo"]["name"],
            "product_name" => $item["orgInfo"]["product_name"],
            "product_starttime" => $item["orgInfo"]["product_starttime"],
            "product_endtime" => $item["orgInfo"]["product_endtime"],
            "brief" => $item["orgInfo"]["brief"],
            "cert_etime" => $item["orgInfo"]["cert_etime"],
            "cert_status" => $item["orgInfo"]["cert_status"],
            "cert_stime" => $item["orgInfo"]["cert_stime"],
        ]);

        return ResponseController::success($orgInfo);
    }

    /**
     * {
     *      "start_time": 0,
     *      "end_time": 0,
     *      "ban_status": false,
     *      "ban_reason": "",
     *      "ban_times": 0,
     *      "ban_msg": "",
     *      "user_operate_type": 0
     * }
     */
    public static function getAccountAPL($accountType, $cookieOrAccessToken, $cid = null)
    {
        $req = [
            "headers" => ["User-Agent" => config("hklist.fake_user_agent")]
        ];

        if ($accountType === "cookie") {
            $req["headers"]["Cookie"] = $cookieOrAccessToken;
        } else if ($accountType === "open_platform") {
            $req["query"]["access_token"] = $cookieOrAccessToken;
        } else {
            return ResponseController::paramsError([
                "errors" => [
                    "accountType" => [
                        "The accountType is invalid."
                    ]
                ]
            ]);
        }
        if ($cid) $req["query"]["cid"] = $cid;

        $res = UtilsController::sendRequest(
            "BDWPApiController::getAccountAPL",
            "get",
            "https://pan.baidu.com/api/checkapl/download",
            $req
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $antiData = $data["data"];

        if (
            !isset($antiData["errno"]) ||
            $antiData["errno"] !== 0
        ) {
            return ResponseController::getAccountAPLFailed($antiData["errno"] ?? "未知", $antiData["errmsg"] ?? "未知");
        }

        $anti = $antiData["anti"];

        return ResponseController::success([
            "start_time" => $anti["start_time"],
            "end_time" => $anti["end_time"],
            "ban_status" => $anti["ban_status"],
            "ban_reason" => $anti["ban_reason"],
            "ban_times" => $anti["ban_times"],
            "ban_msg" => $anti["ban_msg"],
            "user_operate_type" => $anti["user_operate_type"]
        ]);
    }

    public static function decodeSecKey($seckey)
    {
        $seckey = str_replace("-", "+", $seckey);
        $seckey = str_replace("~", "=", $seckey);
        $seckey = str_replace("_", "/", $seckey);
        return ResponseController::success(["seckey" => $seckey]);
    }

    /**
     * {
     *     "uk": 114514,
     *     "shareid": 1919810,
     *     "randsk": "xxxxx",
     *     "list": {
     *         "category": 0,
     *         "fs_id": 0,
     *         "is_dir": true,
     *         "local_ctime": 0,
     *         "local_mtime": 0,
     *         "md5": "xxx",
     *         "path": "xxxx",
     *         "server_ctime": 0,
     *         "server_mtime": 0,
     *         "server_filename": "xxxxx",
     *         "size": 0,
     *         "dlink": "aaaa"
     *     }[]
     * }
     */
    public static function getFileList($surl, $pwd = "", $dir = "/", $page = 1, $num = 1000, $order = "filename")
    {
        if (is_numeric(substr($surl, 0, 1))) $surl = substr($surl, 1);
        $surl = "1" . $surl;

        $res = UtilsController::sendRequest(
            "BDWPApiController::getFileList",
            "post",
            "https://pan.baidu.com/share/wxlist",
            [
                "headers" => [
                    "User-Agent" => config("hklist.fake_wx_user_agent"),
                    "Cookie" => config("hklist.fake_cookie")
                ],
                "query" => [
                    "channel" => "weixin",
                    "version" => "2.9.6",
                    "clienttype" => 25,
                    "web" => 1,
                    "qq-pf-to" => "pcqq.c2c"
                ],
                "form_params" => [
                    "shorturl" => $surl,
                    "pwd" => $pwd,
                    "dir" => $dir,
                    "root" => $dir === "/" ? 1 : 0,
                    "page" => $page,
                    "num" => min($num, 100),
                    "order" => $order
                ]
            ]
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $response = $data["data"];
        if (
            !isset($response["errno"]) ||
            !isset($response["errtype"])
        ) {
            return ResponseController::getFileListFailed($response["errno"] ?? "未知", $response["errtype"] ?? "未知");
        }

        $errno = $response["errno"];
        $errtype = $response["errtype"];

        if ($errno !== 0) {
            if ($errno === -130) {
                $info = match ($errtype) {
                    0 => "啊哦，你来晚了，分享的文件已经被删除了，下次要早点哟。",
                    1 => "啊哦，你来晚了，分享的文件已经被取消了，下次要早点哟。",
                    2 => "此链接分享内容暂时不可访问",
                    3 => "此链接分享内容可能因为涉及侵权、色情、反动、低俗等信息，无法访问！",
                    5 => "啊哦！链接错误没找到文件，请打开正确的分享链接!",
                    10 => "啊哦，来晚了，该分享文件已过期",
                    11 => "由于访问次数过多，该分享链接已失效",
                    12 => "因该分享含有自动备份文件夹，暂无法查看",
                    15 => "系统升级，链接暂时无法查看，升级完成后恢复正常。",
                    17 => "该链接访问范围受限，请使用正常的访问方式",
                    123 => "该链接已超过访问人数上限，可联系分享者重新分享",
                    124 => "您访问的链接已被冻结，可联系分享者进行激活",
                    "mis_105" => "surl 错误",
                    "mispw_9", "mispwd-9" => "提取码错误",
                    "mis_2", "mis_4" => "路径错误",
                    default => null
                };
                return ResponseController::getFileListFailed($errno, $info ?? $errtype);
            } else if ($errno === 2) {
                return ResponseController::getFileListFailed(999, "surl错误");
            } else if ($errno === -6) {
                return ResponseController::getFileListFailed($errno, "请不要使用私密链接");
            }

            return ResponseController::getFileListFailed($errno, $errtype);
        }

        $seckey = self::decodeSecKey($response["data"]["seckey"]);
        $seckeyData = $seckey->getData(true);
        if ($seckeyData["code"] !== 200) return $seckey;

        return ResponseController::success([
            "uk" => $response["data"]["uk"],
            "shareid" => $response["data"]["shareid"],
            "randsk" => $seckeyData["data"]["seckey"],
            "uname" => $response["data"]["uname"],
            "list" => collect($response["data"]["list"])->map(function ($item) {
                return [
                    "category" => (int)$item["category"],
                    "fs_id" => (double)$item["fs_id"],
                    "is_dir" => ((int)$item["isdir"]) === 1,
                    "local_ctime" => (double)$item["local_ctime"],
                    "local_mtime" => (double)$item["local_mtime"],
                    "md5" => $item["md5"] ?? "",
                    "path" => $item["path"],
                    "server_ctime" => (double)$item["server_ctime"],
                    "server_mtime" => (double)$item["server_mtime"],
                    "server_filename" => $item["server_filename"],
                    "size" => (double)$item["size"],
                    "dlink" => $item["dlink"] ?? ""
                ];
            }),
        ]);
    }

    /**
     * {
     *     "vcode_str": "xxx",
     *     "vcode_img": "aaa"
     * }
     */
    public static function getVcode()
    {
        $res = UtilsController::sendRequest(
            "BDWPApiController::getVcode",
            "post",
            "https://pan.baidu.com/api/getvcode",
            [
                "headers" => [
                    "User-Agent" => config("hklist.fake_user_agent"),
                    "Cookie" => config("hklist.fake_cookie")
                ],
                "query" => [
                    "prod" => "pan"
                ]
            ]
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $response = $data["data"];
        if (
            !isset($response["errno"]) ||
            $response["errno"] !== 0
        ) {
            return ResponseController::getVcodeFailed($response["errno"] ?? "未知");
        }

        return ResponseController::success([
            "vcode_str" => $response["vcode"],
            "vcode_img" => $response["img"],
        ]);
    }

    public static function getVcodeDDDDCOR()
    {
        $res = UtilsController::sendRequest(
            "BDWPApiController::getVcodeDDDDCOR",
            "get",
            config("hklist.parse.ddddocr_server")
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $response = $data["data"];
        if (
            !isset($response["code"]) ||
            $response["code"] !== 200
        ) {
            return ResponseController::getVcodeFailed(($response["code"] ?? "未知") . ($response["message"] ?? "未知"));
        }

        return ResponseController::success([
            "vcode_str" => $response["data"]["vcode_str"],
            "vcode_input" => $response["data"]["vcode_input"],
        ]);
    }

    /**
     * errno: 9013/12 风控
     * errno: 2 参数过期
     * {
     *     "from": "aa",
     *     "from_fs_id": 123,
     *     "to": "abc",
     *     "to_fs_id": 123
     * }[]
     */
    public static function saveToDiskWeb($cookie, $shareid, $fs_id, $uk, $sekey, $url)
    {
        $res = UtilsController::sendRequest(
            "BDWPApiController::saveToDiskWeb",
            "post",
            "https://pan.baidu.com/share/transfer",
            [
                "headers" => [
                    "User-Agent" => config("hklist.fake_user_agent"),
                    "Cookie" => $cookie,
                    "Referer" => $url
                ],
                "query" => [
                    "shareid" => $shareid,
                    "from" => $uk,
                    "sekey" => $sekey,
                    "ondup" => "newcopy",
                    "async" => 0,
                    "channel" => "chunlei",
                    "web" => 1,
                    "app_id" => 250528,
                    "clienttype" => 0
                ],
                "form_params" => [
                    "fsidlist" => JSON::encode($fs_id),
                    "path" => "/我的资源"
                ]
            ]
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $response = $data["data"];
        if (
            !isset($response["errno"]) ||
            $response["errno"] !== 0
        ) {
            return ResponseController::saveToDiskFailed($response["errno"] ?? "未知", $response["show_msg"] ?? "未知");
        }

        if (!isset($response["extra"]) || !isset($response["extra"]["list"])) {
            Log::error("why without extra??", $response);
            return ResponseController::transferFileFailed($response["errno"], ($response["show_msg"] ?? "未知") . "but without extra??");
        }

        return ResponseController::success($response["extra"]["list"]);
    }

    /**
     * errno: 31045 31326 风控
     * {
     *     urls: string[]
     * }
     */
    public static function downloadByDisk($cookie, $path, $ua)
    {
        $res = UtilsController::sendRequest(
            "BDWPApiController::downloadByDisk",
            "post",
            "https://pcs.baidu.com/rest/2.0/pcs/file",
            [
                "headers" => [
                    "User-Agent" => $ua,
                    "Cookie" => $cookie
                ],
                "query" => [
                    "method" => "locatedownload",
                    "app_id" => 250528,
                    "path" => $path,
                    "ver" => 2,
                    "time" => 1732106564,
                    "rand" => "9b696564418ae5ab758d60cbc96c199c4679f772",
                    "rand2" => "d76e889b6aafd3087ac3bd56f4d4053a",
                    "devuid" => "BDIMXV2-O_0B5DF5389B5D4775946635E4E18D8314-C_0-D_5CDF_B803_70B0_42BC.-M_BCECA007D4F6-V_0D274B62",
                    "version" => "7.44.6.1",
                ]
            ]
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $response = $data["data"];
        if (
            isset($response["error_code"]) ||
            isset($response["error_msg"]) ||
            !isset($response["urls"])
        ) {
            return ResponseController::downloadByDiskFailed($response["error_code"] ?? "未知", $response["error_msg"] ?? "未知");
        }

        return ResponseController::success([
            "urls" => array_reverse(array_map(fn($v) => $v["url"], $response["urls"]))
        ]);
    }

    /**
     * {
     *     bdstoken: "xxx",
     *     sign1: "xxx",
     *     sign2: "xxx",
     *     sign3: "xxxx",
     * }
     */
    public static function getTemplateVariable($cookie)
    {
        $res = UtilsController::sendRequest(
            "BDWPApiController::getTemplateVariable",
            "get",
            "https://pan.baidu.com/api/gettemplatevariable",
            [
                "headers" => [
                    "User-Agent" => config("hklist.fake_user_agent"),
                    "Cookie" => $cookie
                ],
                "query" => [
                    "fields" => JSON::encode([
                        "bdstoken",
                        "sign1",
                        "sign2",
                        "sign3"
                    ])
                ]
            ]
        );

        $data = $res->getData(true);
        if ($data["code"] !== 200) return $res;

        $response = $data["data"];
        if (
            !isset($response["errno"]) ||
            !isset($response["result"]) ||
            $response["errno"] !== 0
        ) {
            return ResponseController::getTemplateVariableFailed($response["errno"] ?? "未知");
        }

        return ResponseController::success([
            "bdstoken" => $response["result"]["bdstoken"],
            "sign1" => $response["result"]["sign1"],
            "sign2" => $response["result"]["sign2"],
            "sign3" => $response["result"]["sign3"],
        ]);
    }
}
