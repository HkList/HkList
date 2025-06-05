<?php

namespace App\Http\Controllers;

use App\Models\Proxy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ProxyController extends Controller
{
    public function select(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "column" => ["nullable", "string", Rule::in(Proxy::$attrs)],
            "direction" => ["nullable", "string", Rule::in(["asc", "desc"])],
            "keyword" => "nullable|string"
        ]);
        if ($validator->fails()) return ResponseController::paramsError($validator->errors());

        $data = Proxy::query()
            ->whereLike("proxy", "%$request[keyword]%")
            ->orderBy($request["column"] ?? "id", $request["direction"] ?? "asc")
            ->with(["account"])
            ->paginate($request["size"]);

        return ResponseController::success($data);
    }

    public function insert(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "type" => ["required", Rule::in(['http', 'api', "proxy"])],
            "proxy" => "required|string",
            "account_id" => "required|numeric"
        ]);
        if ($validator->fails()) return ResponseController::paramsError($validator->errors());

        Proxy::query()->create([
            "type" => $request["type"],
            "proxy" => $request["proxy"],
            "enable" => true,
            "reason" => null,
            "account_id" => $request["account_id"]
        ]);

        return ResponseController::success();
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|array",
            "id.*" => "required|numeric",
            "type" => ["required", Rule::in(['http', 'api', "proxy"])],
            "proxy" => "required|string",
            "enable" => "nullable|boolean",
            "reason" => "nullable|string",
            "account_id" => "required|numeric",
        ]);
        if ($validator->fails()) return ResponseController::paramsError($validator->errors());

        $count = Proxy::query()
            ->whereIn("id", $request["id"])
            ->update([
                "type" => $request["type"],
                "proxy" => $request["proxy"],
                "enable" => $request["enable"] ?? true,
                "reason" => $request["reason"] ?? null,
                "account_id" => $request["account_id"]
            ]);

        if ($count === 0) {
            return ResponseController::updateFailed();
        } else {
            return ResponseController::success();
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "id" => "required|array",
            "id.*" => "required|numeric",
        ]);
        if ($validator->fails()) return ResponseController::paramsError($validator->errors());

        $count = Proxy::query()
            ->whereIn("id", $request["id"])
            ->delete();

        if ($count === 0) {
            return ResponseController::deleteFailed();
        } else {
            return ResponseController::success();
        }
    }
}
