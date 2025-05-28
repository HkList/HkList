<?php

use App\Models\FileList;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// 2.2.10 修复文件列表表 fs_id 索引
$indexes = DB::select('SHOW INDEXES FROM file_lists WHERE Column_name = ?', ['fs_id']);
if (!empty($indexes)) return;

// 判断表是否为空
if (FileList::query()->count() !== 0) throw new Exception("索引更新需要手动执行,更新方法:前往站点目录执行命令 php artisan app:update-file-lists");

Schema::table('file_lists', function (Blueprint $table) {
    $table->string('fs_id', 255)->unique()->index()->change();
});