<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'statements';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xapi_attachments', function (Blueprint $table) {
            $table->string('id', 128);
            $table->longText('content');
            $table->string('content_type', 250)->index();
            $table->integer('length');
            $table->string('store', 36)->index();
            $table->string('client', 36)->nullable()->index();

            // MySQL does not support ISO 8601 timestamp in DataTime column.
            // PostgreSQL does and we need it for TimescaleDB hypertable.
            if (config('database.connections.statements.driver') == 'mysql') {
                $table->string('stored', 32)->index();
            } else {
                $table->dateTimeTz('stored', 6)->index();
            }

            $table->primary(['id', 'store']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xapi_attachments');
    }
};
