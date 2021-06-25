<?php

use Illuminate\Support\Facades\DB;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

class DbAttachmentIdToInt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_files', function (Blueprint $table) {
            // Drop old indexes
            $table->dropIndex(['attachment_id']);
            $table->dropIndex(['attachment_type']);
            $table->dropIndex(['field']);

            $table->index([
                'field',
                'attachment_type',
                'attachment_id',
            ]);
        });

        // Convert attachment_id to bigint if the attachment_id column only contains integer values.
        $convert = true;
        DB::table('system_files')
            ->select(['attachment_id'])
            ->chunkById(1000, function ($attachmentIds) use (&$convert) {
                $files = $attachmentIds->filter(function ($file) {
                    return ! is_numeric($file->attachment_id);
                });

                if ($files->isNotEmpty()) {
                    return $convert = false;
                }
            });

        if ($convert) {
            Schema::table('system_files', function (Blueprint $table) {
                $table->unsignedBigInteger('attachment_id')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_files', function (Blueprint $table) {
            $table->index('attachment_id');
            $table->index('attachment_type');
            $table->index('field');
        });
    }
}
