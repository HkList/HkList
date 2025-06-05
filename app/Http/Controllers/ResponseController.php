<?php

namespace App\Http\Controllers;

use Exception;

class ResponseController extends Controller
{
    public static function response($code, $statusCode, $message, $data = null)
    {
        return response()->json(
            [
                "code" => $code,
                "message" => $message,
                "data" => $data
            ],
            $statusCode
        );
    }

    public static function success($data = null)
    {
        return self::response(200, 200, "请求成功", $data);
    }

    public static function networkError($actionName)
    {
        return self::response(20001, 500, "在执行 $actionName 时遇到服务器网络异常");
    }

    public static function requestError($actionName, $data = null)
    {
        return self::response(20002, 500, "在执行 $actionName 时服务器提示参数有问题", $data);
    }

    public static function requestServerError($actionName, $data = null)
    {
        return self::response(20003, 500, "在执行 $actionName 时服务器侧出现错误", $data);
    }

    public static function unknownError($actionName, Exception $exception)
    {
        return self::response(20004, 500, "在执行 $actionName 时服务器侧出现错误,错误信息: " . $exception->getMessage(), config("app.debug") ? $exception : null);
    }

    public static function paramsError($errors)
    {
        return self::response(20005, 500, "参数错误", config("app.debug") ? ["errors" => $errors] : null);
    }

    public static function getAccountInfoFailed($errno, $errmsg)
    {
        return self::response(20006, 500, "获取账户信息失败,errno: $errno, errmsg: $errmsg");
    }

    public static function getAccessTokenFailed($error, $errmsg)
    {
        return self::response(20007, 500, "换取AccessToken失败,error: $error, errmsg: $errmsg");
    }

    public static function getSvipAtFailed($errno, $errmsg)
    {
        return self::response(20008, 500, "获取svip到期时间失败,errno: $errno, errmsg: $errmsg");
    }

    public static function getEnterpriseInfoFailed($errno, $errmsg)
    {
        return self::response(20009, 500, "获取企业账号信息失败,errno: $errno, errmsg: $errmsg");
    }

    public static function getAccountAPLFailed($errno, $errmsg)
    {
        return self::response(20010, 500, "获取账号封禁状态失败,errno: $errno, errmsg: $errmsg");
    }

    public const getFileListMap = [
        "-1" => "由于您分享了违反相关法律法规的文件，分享功能已被禁用，之前分享出去的文件不受影响。",
        "-2" => "用户不存在,请刷新页面后重试",
        "-3" => "文件不存在,请刷新页面后重试",
        "-4" => "登录信息有误，请重新登录试试",
        "-5" => "host_key和user_key无效",
        "-6" => "请重新登录",
        "-7" => "该分享已删除或已取消",
        "-8" => "该分享已经过期",
        "-9" => "访问密码错误",
        "-10" => "分享外链已经达到最大上限100000条，不能再次分享",
        "-11" => "验证cookie无效",
        "-14" => "对不起，短信分享每天限制20条，你今天已经分享完，请明天再来分享吧！",
        "-15" => "对不起，邮件分享每天限制20封，你今天已经分享完，请明天再来分享吧！",
        "-16" => "对不起，该文件已经限制分享！",
        "-17" => "文件分享超过限制",
        "-19" => "请求频率过高,请使用代理请求",
        "-30" => "文件已存在",
        "-31" => "文件保存失败",
        "-33" => "一次支持操作999个，减点试试吧",
        "-32" => "未知",
        "-70" => "你分享的文件中包含病毒或疑似病毒，为了你和他人的数据安全，换个文件分享吧",
        2 => "参数错误",
        3 => "未登录或帐号无效",
        4 => "存储好像出问题了，请稍后再试",
        108 => "文件名有敏感词，优化一下吧",
        110 => "分享次数超出限制，可以到“我的分享”中查看已分享的文件链接",
        114 => "当前任务不存在，保存失败",
        115 => "该文件禁止分享",
        112 => '页面已过期'
    ];

    public static function getFileListFailed($errno, $errtype)
    {
        $message = self::getFileListMap[$errno] ?? "未知";
        return self::response(20011, 500, "获取分享链接信息失败,errno: $errno, errtype: $errtype,message: $message");
    }

//    public static function getFileListMsgFailed($errmsg)
//    {
//        return self::response(20012, 500, "获取分享链接信息失败,errmsg: $errmsg");
//    }

    public static function getVcodeFailed($errno)
    {
        return self::response(20013, 500, "获取验证码图片信息失败,errno: $errno");
    }

    public static function inBlackList($reason)
    {
        return self::response(20014, 400, "处于黑名单中,原因: $reason");
    }

    public static function installFailed($message)
    {
        return self::response(20015, 500, "安装失败,message: $message");
    }

    public static function wrongPass($role)
    {
        return self::response(20016, 400, "请检查 $role 密码是否正确");
    }

    public static function deleteFailed()
    {
        return self::response(20017, 400, "删除成功,但部分可能删除失败");
    }

    public static function updateFailed()
    {
        return self::response(20017, 400, "更新失败,请检查参数");
    }

    public static function getProvFailed($ip)
    {
        return self::response(20018, 500, "获取 $ip 所在的归属地失败");
    }

    public static function TokenNotExists()
    {
        return self::response(20019, 400, "卡密不存在");
    }

    public static function TokenIpHitMax()
    {
        return self::response(20020, 400, "卡密的可用ip数已到达限制");
    }

    public static function TokenExpired()
    {
        return self::response(20021, 400, "卡密已到期");
    }

    public static function TokenQuotaHasBeenUsedUp()
    {
        return self::response(20022, 400, "卡密可用容量已用完");
    }

    public static function parserServerNotDefined()
    {
        return self::response(20023, 400, "未完整配置解析服务器");
    }

    public static function mailServiceIsNotEnable()
    {
        return self::response(20024, 400, "未开启邮件服务");
    }

    public static function blackListExists()
    {
        return self::response(20025, 400, "该识别码已存在");
    }

    public static function filesOverLoaded()
    {
        return self::response(20026, 400, "单次请求的文件过多");
    }

    public static function tokenQuotaCountIsNotEnough()
    {
        return self::response(20027, 400, "卡密剩余下载文件数不足");
    }

    public static function tokenQuotaSizeIsNotEnough()
    {
        return self::response(20028, 400, "卡密剩余下载文件大小不足");
    }

    public static function unknownFsId()
    {
        return self::response(20029, 400, "未记录的fs_id");
    }

    public static function fileIsTooSmall()
    {
        return self::response(20030, 400, "选择解析的文件过小");
    }

    public static function fileIsTooBig()
    {
        return self::response(20031, 400, "选择解析的文件过大");
    }

    public static function unknownParseMode()
    {
        return self::response(20031, 500, "未知解析模式");
    }

    public static function unsupportedCountry()
    {
        return self::response(20032, 400, "不支持非中国国家");
    }

    public static function accountIsNotEnough()
    {
        return self::response(20033, 400, "账号不足");
    }

    public static function unknownAccountType()
    {
        return self::response(20034, 400, "未知账号类型");
    }

    public static function saveToDiskFailed($errno, $errmsg)
    {
        return self::response(20035, 500, "保存到网盘失败,errno: $errno, errmsg: $errmsg");
    }

    public static function downloadByDiskFailed($errno, $errmsg)
    {
        return self::response(20036, 500, "下载盘内文件失败,errno: $errno, errmsg: $errmsg");
    }

    public static function getTemplateVariableFailed($errno)
    {
        return self::response(20037, 500, "获取模板变量失败,errno: $errno");
    }

    public static function canNotChangeGuestToken($action)
    {
        return self::response(20038, 400, "请不要 $action 游客卡密");
    }

    public static function tokenHasBeenBaned($reason)
    {
        return self::response(20039, 400, "卡密已被封禁, 原因: $reason");
    }

    public static function TokenExists()
    {
        return self::response(20040, 400, "卡密已存在");
    }

    public static function domainError()
    {
        return self::response(20041, 400, "域名校验不通过");
    }

    public static function authExpired()
    {
        return self::response(20042, 400, "授权已过期");
    }

    public static function getUinfoFailed($errno)
    {
        return self::response(20043, 500, "获取Uinfo失败, errno: $errno");
    }

    public static function getQuoteEnterpriseFailed($errno)
    {
        return self::response(20044, 500, "获取企业配额失败, errno: $errno");
    }

    public static function deleteFolderFailed($errno, $errmsg)
    {
        return self::response(20045, 500, "删除文件夹失败, errno: $errno, errmsg: $errmsg");
    }

    public static function createFolderFailed($errno, $errmsg)
    {
        return self::response(20046, 500, "创建文件夹失败, errno: $errno, errmsg: $errmsg");
    }

    public static function getFolderFailed($errno, $errmsg)
    {
        return self::response(20047, 500, "获取文件夹失败, errno: $errno, errmsg: $errmsg");
    }

    public static function transferFileFailed($errno, $errmsg)
    {
        return self::response(20048, 500, "转存文件失败, errno: $errno, errmsg: $errmsg 请重试");
    }

    public static function getDlinkFailed($errno)
    {
        return self::response(20049, 500, "获取Dlink失败, errno: $errno");
    }

    public static function getTplConfigFailed($errno, $errmsg)
    {
        return self::response(20050, 500, "获取tplconfig失败, errno: $errno, errmsg: $errmsg");
    }

    public static function getRealLinkFailed($errno, $errmsg)
    {
        return self::response(20051, 500, "获取下载链接失败, errno: $errno, errmsg: $errmsg");
    }

    public static function proxyIsNotEnable()
    {
        return self::response(20052, 400, "代理未启用");
    }

    public static function filesIsTooBig()
    {
        return self::response(20053, 400, "选择解析的文件总量过大");
    }

    public static function getQuoteFailed($errno, $errmsg)
    {
        return self::response(20054, 500, "获取配额失败, errno: $errno, errmsg: $errmsg");
    }

    public static function getDlinkOpenPlatformFailed($errno, $errmsg)
    {
        return self::response(20055, 500, "开放平台获取Dlink失败, errno: $errno, errmsg: $errmsg");
    }

    public static function getReallinkOpenPlatformFailed($errno, $errmsg)
    {
        return self::response(20056, 500, "开放平台获取Reallink失败, errno: $errno, errmsg: $errmsg");
    }

    public static function canNotDownloadFolder()
    {
        return self::response(20057, 400, "服务器禁止解析文件夹");
    }

    public static function importFileToEnterpriseError($errno, $errmsg)
    {
        return self::response(20058, 500, "导入文件失败, errno: $errno, errmsg: $errmsg");
    }

    public static function ocrNotFound()
    {
        return self::response(20059, 500, "未找到ocr服务");
    }

    public static function cidWrong()
    {
        return self::response(20060, 400, "cid错误");
    }

    public static function unUniqueFsId()
    {
        return self::response(20061, 500, "出现意外的重复fs_id,请联系管理员");
    }

    public static function autoUpdateError($message)
    {
        return self::response(20062, 500, "自动更新失败:{$message}");
    }

    public static function unknownTokenType()
    {
        return self::response(20063, 500, "未知卡密类型");
    }

    public static function needMoiuToken()
    {
        return self::response(20064, 500, "此模式需要使用MoiuToken");
    }

    public static function unavaliable()
    {
        return self::response(20065, 500, "当前解析模式不可用");
    }
}
