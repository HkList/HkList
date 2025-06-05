<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// 2.2.26 新增代理表
if (!Schema::hasTable("proxies")) {
    Schema::create("proxies", function (Blueprint $table) {
        $table->increments("id");
        $table->enum("type", ["http", "api", "proxy"])->comment("代理类型");
        $table->string("proxy")->comment("代理地址");
        $table->boolean("enable")->default(true)->comment("是否启用");
        $table->string("reason")->nullable()->comment("禁用原因");
        $table->unsignedBigInteger("account_id")->comment("关联账号ID");
        $table->timestamps();

        $table->foreign("account_id")->on("accounts")->references("id")->cascadeOnDelete();
    });
}