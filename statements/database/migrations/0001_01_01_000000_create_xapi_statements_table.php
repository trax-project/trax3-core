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
    protected $connection = 'statements';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('xapi_statements', function (Blueprint $table) {
            $table->uuid('id');
            $table->json('raw');
            $table->boolean('voided')->default(false)->index();
            $table->boolean('voiding')->default(false)->index();
            $table->boolean('validated')->default(false)->index();
            $table->boolean('valid')->default(false)->index();
            $table->boolean('pseudonymized')->default(false)->index();
            $table->string('actor_id', 32)->nullable()->index();
            $table->string('verb_id', 32)->index();
            $table->string('object_id', 36)->nullable()->index();   // 36 is required because it may be an UUID (statement ref).
            $table->string('type_id', 32)->nullable()->index();
            $table->jsonb('agent_ids');
            $table->jsonb('activity_ids');
            $table->uuid('registration')->nullable()->index();
            $table->uuid('statement_ref')->nullable()->index();
            $table->string('store', 36)->index();
            $table->string('client', 36)->nullable()->index();

            // MySQL does not support ISO 8601 timestamp in DataTime column.
            // PostgreSQL does and we need it for TimescaleDB hypertable.
            if (config('database.connections.statements.driver') == 'mysql') {
                $table->string('timestamp', 32);
                $table->string('stored', 32)->index();
            } else {
                $table->dateTimeTz('timestamp', 6);
                $table->dateTimeTz('stored', 6)->index();
            }

            // The stored column must be part of the unique index.
            if (config('database.connections.statements.timeseries')) {
                $table->primary(['id', 'store', 'stored']);
            } else {
                $table->primary(['id', 'store']);
            }
        });

        /**
         * We create a multi-valued index on the `agent_ids` and `activity_ids` columns.
         * MySQL 8.0.17+.
         */
        if (config('database.connections.statements.driver') == 'mysql') {
            DB::connection($this->connection)->statement("CREATE INDEX xapi_statements_agent_ids_index ON xapi_statements ((CAST(agent_ids->'$[*]' as CHAR(32) ARRAY)));");
            DB::connection($this->connection)->statement("CREATE INDEX xapi_statements_activity_ids_index ON xapi_statements ((CAST(activity_ids->'$[*]' as CHAR(32) ARRAY)));");
        }

        /**
         * We create a multi-valued index on the `agent_ids` and `activity_ids` columns.
         * PostgreSQL 13+.
         */
        if (config('database.connections.statements.driver') == 'pgsql') {
            DB::connection($this->connection)->statement('CREATE INDEX xapi_statements_agent_ids_index ON xapi_statements USING GIN (agent_ids jsonb_path_ops);');
            DB::connection($this->connection)->statement('CREATE INDEX xapi_statements_activity_ids_index ON xapi_statements USING GIN (activity_ids jsonb_path_ops);');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('xapi_statements');
    }
};
