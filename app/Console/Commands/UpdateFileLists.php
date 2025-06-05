<?php

namespace App\Console\Commands;

use App\Models\FileList;
use App\Models\FileListsTemp;
use App\Models\Record;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class UpdateFileLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-file-lists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '对file_lists表制作索引';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(0);

        // 2.2.10 修复文件列表表 fs_id 索引
        $indexes = DB::select('SHOW INDEXES FROM file_lists WHERE Column_name = ?', ['fs_id']);
        if (!empty($indexes)) {
            $this->info("索引已经完成,无需重复执行");
            return;
        }

        Cache::set("fs_id_indexing", true);

        $total = FileList::query()->count();
        $bar = $this->output->createProgressBar($total);
        $bar->setBarCharacter('<comment>=</comment>');
        $bar->setEmptyBarCharacter(' ');
        $bar->setProgressCharacter('|');
        $bar->setBarWidth(50);

        try {
            Schema::dropIfExists("file_lists_temp");
            Schema::create("file_lists_temp", function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('table_id');
                $table->string('fs_id', 255)->unique()->index();
            });

            $page = 1;
            $perPage = 1000;
            $needDelete = [];
            $allRecordsToUpdate = [];

            while (true) {
                $files = FileList::query()->paginate($perPage, ['*'], 'page', $page)->getCollection();
                if ($files->count() === 0) break;
                $page++;

                $fsIds = $files->pluck('fs_id');
                $tempFiles = FileListsTemp::query()->whereIn("fs_id", $fsIds)->get()->keyBy("fs_id");

                foreach ($files as $file) {
                    $tempFile = $tempFiles[$file["fs_id"]] ?? null;
                    $bar->advance();

                    // 如果不存在,插入数据
                    if (!$tempFile) {
                        $tempFiles[$file["fs_id"]] = [
                            "table_id" => $file["id"],
                            "fs_id" => $file["fs_id"]
                        ];
                        FileListsTemp::query()->create([
                            "table_id" => $file["id"],
                            "fs_id" => $file["fs_id"]
                        ]);
                        continue;
                    }

                    $ids = FileList::query()->where("fs_id", $file["fs_id"])->get()->pluck("id");

                    $recordIds = Record::query()
                        ->whereIn("fs_id", $ids)
                        ->get(['id', 'fs_id']);

                    // 批量更新 fs_id
                    foreach ($recordIds as $record) {
                        $allRecordsToUpdate[] = [
                            'id' => $record["id"],
                            'fs_id' => $tempFile["table_id"]
                        ];
                    }

                    $needDelete = array_merge($needDelete, $ids->filter(fn($id) => $id !== $tempFile["table_id"])->toArray());
                }
            }

            foreach (array_chunk($allRecordsToUpdate, 50000) as $chunk) {
                $caseStatements = '';
                $ids = [];
                foreach ($chunk as $update) {
                    $ids[] = $update["id"];
                    $caseStatements .= "WHEN {$update['id']} THEN '{$update['fs_id']}' ";
                }

                $sql = "
        UPDATE records
        SET fs_id = CASE id
            {$caseStatements}
            ELSE fs_id END
        WHERE id IN (" . implode(',', $ids) . ")
    ";

                DB::statement($sql);
            }

            foreach (array_chunk($needDelete, 50000) as $chunk) {
                FileList::query()->whereIn('id', $chunk)->delete();
            }

            Schema::dropIfExists("file_lists_temp");

            Schema::table('file_lists', function (Blueprint $table) {
                $table->string('fs_id', 255)->unique()->index()->change();
            });

            Cache::set("fs_id_indexing", false);

            $bar->finish();
        } catch (Exception $e) {
            Cache::set("fs_id_indexing", false);
            throw $e;
        }

    }
}
