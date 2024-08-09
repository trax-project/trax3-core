<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * The database connection that should be used by the migration.
     *
     * @var string
     */
    protected $connection = 'agents';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xapi_agents', function (Blueprint $table) {
            $table->string('id', 32);
            $table->string('sid_field_1');
            $table->string('sid_field_2')->nullable();
            $table->string('sid_type', 12)->index();
            $table->string('name')->nullable();
            $table->boolean('is_group')->default(false)->index();
            $table->jsonb('members');
            $table->integer('members_count')->default(0);
            $table->boolean('pseudonymized')->default(false)->index();
            $table->uuid('person_id', 36)->index();
            $table->string('store', 36)->index();

            // MySQL does not support ISO 8601 timestamp in DataTime column.
            // PostgreSQL does and we need it for TimescaleDB hypertable.
            if (config('database.connections.agents.driver') == 'mysql') {
                $table->string('stored', 32)->index();
                $table->string('updated', 32)->index();
            } else {
                $table->dateTimeTz('stored', 6)->index();
                $table->dateTimeTz('updated', 6)->index();
            }

            $table->primary(['id', 'store']);
        });

        /**
         * We create a multi-valued index on the `members` column.
         * MySQL 8.0.17+.
         */
        if (config('database.connections.agents.driver') == 'mysql') {
            DB::connection($this->connection)->statement("CREATE INDEX xapi_agents_members_index ON xapi_agents ((CAST(members->'$[*]' as CHAR(32) ARRAY)));");
        }

        /**
         * We create a multi-valued index on the `members` column.
         * PostgreSQL 13+.
         */
        if (config('database.connections.agents.driver') == 'pgsql') {
            DB::connection($this->connection)->statement('CREATE INDEX xapi_agents_members_index ON xapi_agents USING GIN (members jsonb_path_ops);');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xapi_agents');
    }
};
