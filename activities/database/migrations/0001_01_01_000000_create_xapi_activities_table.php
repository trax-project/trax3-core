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
    protected $connection = 'activities';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xapi_activities', function (Blueprint $table) {
            $table->string('id', 32);
            $table->string('iri');
            $table->json('definition');
            $table->string('type_id', 32)->nullable()->index();
            $table->boolean('is_category')->default(false)->index();
            $table->boolean('is_profile')->default(false)->index();
            $table->string('store', 36)->index();

            // MySQL does not support ISO 8601 timestamp in DataTime column.
            // PostgreSQL does and we need it for TimescaleDB hypertable.
            if (config('database.connections.activities.driver') == 'mysql') {
                $table->string('stored', 32)->index();
                $table->string('updated', 32)->index();
            } else {
                $table->dateTimeTz('stored', 6)->index();
                $table->dateTimeTz('updated', 6)->index();
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
        Schema::dropIfExists('xapi_activities');
    }
};
