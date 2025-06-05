<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Api\ParserApiController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ResponseController;
use App\Http\Controllers\UtilsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ParseConfigController extends Controller
{
    public function getConfig()
    {
        return ResponseController::success(config("hklist.parse"));
    }

    public function updateConfig(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "parser_server" => "nullable|string",
            "parser_password" => "nullable|string",
            "allow_folder" => "required|boolean",
            "ddddocr_server" => "nullable|string",

            "token_parse_mode" => "required|numeric",
            "token_user_agent" => "required|string",
            "guest_parse_mode" => "required|numeric",
            "guest_user_agent" => "required|string",

            "token_proxy_host" => "nullable|string",
            "token_proxy_password" => "nullable|string",
            "guest_proxy_host" => "nullable|string",
            "guest_proxy_password" => "nullable|string",

            "moiu_token" => "nullable|string"
        ]);

        if ($validator->fails()) return ResponseController::paramsError($validator->errors());

        if (str_ends_with($request["parser_server"] ?? "", "/")) $request["parser_server"] = substr($request["parser_server"], 0, -1);
        if (str_ends_with($request["ddddocr_server"] ?? "", "/")) $request["ddddocr_server"] = substr($request["ddddocr_server"], 0, -1);
        if (str_ends_with($request["guest_proxy_host"] ?? "", "/")) $request["guest_proxy_host"] = substr($request["guest_proxy_host"], 0, -1);
        if (str_ends_with($request["token_proxy_host"] ?? "", "/")) $request["token_proxy_host"] = substr($request["token_proxy_host"], 0, -1);

        UtilsController::updateEnv([
            "HKLIST_PARSER_SERVER" => $request["parser_server"] ?? "",
            "HKLIST_PARSER_PASSWORD" => $request["parser_password"] ?? "",
            "HKLIST_ALLOW_FOLDER" => $request["allow_folder"],
            "HKLIST_DDDDOCR_SERVER" => $request["ddddocr_server"] ?? "",

            "HKLIST_TOKEN_PARSE_MODE" => $request["token_parse_mode"],
            "HKLIST_TOKEN_USER_AGENT" => $request["token_user_agent"],
            "HKLIST_GUEST_PARSE_MODE" => $request["guest_parse_mode"],
            "HKLIST_GUEST_USER_AGENT" => $request["guest_user_agent"],

            "HKLIST_TOKEN_PROXY_HOST" => $request["token_proxy_host"] ?? "",
            "HKLIST_TOKEN_PROXY_PASSWORD" => $request["token_proxy_password"] ?? "",
            "HKLIST_GUEST_PROXY_HOST" => $request["guest_proxy_host"] ?? "",
            "HKLIST_GUEST_PROXY_PASSWORD" => $request["guest_proxy_password"] ?? "",

            "HKLIST_MOIU_TOKEN" => $request["moiu_token"] ?? "",
        ]);

        return ResponseController::success();
    }

    public function testAuth()
    {
        $parse = config("hklist.parse");
        if ($parse["parser_server"] === "" || $parse["parser_password"] === "") return ResponseController::parserServerNotDefined();
        $res = ParserApiController::getAuthInfo($parse["parser_server"], $parse["parser_password"]);
        $resData = $res->getData(true);
        if ($resData["code"] !== 200) return $res;
        return ResponseController::success($resData["data"]);
    }
}
