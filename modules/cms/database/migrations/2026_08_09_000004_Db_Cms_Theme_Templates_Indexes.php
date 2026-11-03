<?php

return new class extends \Winter\Storm\Database\Updates\Migration
{
    public function up()
    {
        Schema::table('cms_theme_templates', function ($table) {
            // Every datasource query filters on source and path together, so a composite
            // index serves them all. This makes the standalone source index redundant.
            $table->index(['source', 'path', 'deleted_at']);
            $table->dropIndex(['source']);
        });
    }

    public function down()
    {
        Schema::table('cms_theme_templates', function ($table) {
            $table->index(['source']);
            $table->dropIndex(['source', 'path', 'deleted_at']);
        });
    }
};
