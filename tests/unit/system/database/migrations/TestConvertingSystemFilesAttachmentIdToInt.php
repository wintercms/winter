<?php

use Illuminate\Support\Facades\DB;
use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Support\Facades\Schema;

class TestConvertingSystemFilesAttachmentIdToInt extends TestCase
{
    public static $tempMigrationsPath = '';

    protected $migrationFileName = '2021_06_25_143510_Db_Attachment_ID_To_int.php';

    protected function setUp(): void
    {
        parent::setUp();

        $this->setupTestFixtures();

        if (Schema::hasTable('migrations')) {
            // Clean database starting point
            $this->artisan('winter:down --force');
        }

        $this->artisan('winter:up');
    }

    protected function tearDown(): void
    {
        $this->tearDownTestFixtures();

        $this->artisan('winter:down --force');

        parent::tearDown();
    }

    public function test_it_can_change_attachment_id_column_to_int()
    {
        $faker = \Faker\Factory::create();

        $data = [];

        foreach (range(1, 100) as $i) {
            $data[] = [
                'disk_name' => str_random().'.jpg',
                'file_name' => str_random().'.jpg',
                'file_size' => mt_rand(),
                'content_type' => 'image/jpeg',
                'title' => $faker->title,
                'description' => $faker->text,
                'field' => 'files',
                'attachment_id' => mt_rand(1, 10000),
                'attachment_type' => 'System\Models\File',
                'sort_order' => mt_rand(0, 100),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        DB::table('system_files')->insert($data);

        $this->enableMigration();

        $this->artisan('winter:up');

        $this->assertEquals('bigint', DB::getSchemaBuilder()->getColumnType('system_files', 'attachment_id'));

        $files = DB::table('system_files')->get();

        foreach ($files as $file) {
            $this->assertIsInt($file->attachment_id);
            $this->assertNotEmpty($file->attachment_id);
            $this->assertTrue($file->attachment_id !== 0);
        }
    }

    public function test_it_will_not_change_attachment_id_column_to_int()
    {
        $faker = \Faker\Factory::create();

        $data = [];

        foreach (range(1, 100) as $i) {
            $data[] = [
                'disk_name' => str_random().'.jpg',
                'file_name' => str_random().'.jpg',
                'file_size' => mt_rand(),
                'content_type' => 'image/jpeg',
                'title' => $faker->title,
                'description' => $faker->text,
                'field' => 'files',
                'attachment_id' => str_random(10),
                'attachment_type' => 'System\Models\File',
                'sort_order' => mt_rand(0, 100),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        DB::table('system_files')->insert($data);

        $this->enableMigration();

        $this->artisan('winter:up');

        $this->assertEquals('string', DB::getSchemaBuilder()->getColumnType('system_files', 'attachment_id'));
    }

    public function test_it_will_not_change_attachment_id_column_to_int_when_column_contains_both_strings_and_integers()
    {
        $faker = \Faker\Factory::create();

        $data = [];

        foreach (range(1, 100) as $i) {
            $data[] = [
                'disk_name' => str_random().'.jpg',
                'file_name' => str_random().'.jpg',
                'file_size' => mt_rand(),
                'content_type' => 'image/jpeg',
                'title' => $faker->title,
                'description' => $faker->text,
                'field' => 'files',
                'attachment_id' => mt_rand(0, 1) ? str_random(10) : mt_rand(),
                'attachment_type' => 'System\Models\File',
                'sort_order' => mt_rand(0, 100),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        DB::table('system_files')->insert($data);

        $this->enableMigration();

        $this->artisan('winter:up');

        $this->assertEquals('string', DB::getSchemaBuilder()->getColumnType('system_files', 'attachment_id'));
    }

    public function test_it_will_not_convert_attachment_id_column_when_it_contains_a_float()
    {
        $faker = \Faker\Factory::create();

        $data = [];

        foreach (range(1, 100) as $i) {
            $data[] = [
                'disk_name' => str_random().'.jpg',
                'file_name' => str_random().'.jpg',
                'file_size' => mt_rand(),
                'content_type' => 'image/jpeg',
                'title' => $faker->title,
                'description' => $faker->text,
                'field' => 'files',
                'attachment_id' => $faker->randomFloat(2, 1),
                'attachment_type' => 'System\Models\File',
                'sort_order' => mt_rand(0, 100),
                'created_at' => now()->toDateTimeString(),
                'updated_at' => now()->toDateTimeString(),
            ];
        }

        DB::table('system_files')->insert($data);

        $this->enableMigration();

        $this->artisan('winter:up');

        $this->assertEquals('string', DB::getSchemaBuilder()->getColumnType('system_files', 'attachment_id'));
    }

    protected function setupTestFixtures()
    {
        static::$tempMigrationsPath = storage_path('temp/tests/database/migrations');

        $this->disableMigration();
    }

    protected function tearDownTestFixtures()
    {
        $this->enableMigration();
    }

    protected function enableMigration()
    {
        if (! is_dir(static::$tempMigrationsPath)) {
            mkdir(static::$tempMigrationsPath, 0777, true);
        }

        if (! file_exists(static::$tempMigrationsPath.'/'.$this->migrationFileName)) {
            return;
        }

        rename(static::$tempMigrationsPath.'/'.$this->migrationFileName, base_path('modules/system/database/migrations/'.$this->migrationFileName));
    }

    protected function disableMigration()
    {
        if (! file_exists(base_path('modules/system/database/migrations/'.$this->migrationFileName))) {
            return;
        }

        rename(base_path('modules/system/database/migrations/'.$this->migrationFileName), static::$tempMigrationsPath.'/'.$this->migrationFileName);
    }
}
