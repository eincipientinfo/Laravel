<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class TaskRelatedTableMigration extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tasks', function(Blueprint $table){
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->uuid('uuid')->nullable()->unique('unique_uuid_index');
            $table->string('name', 255)->nullable();
            $table->float('lng', 11, 2)->nullable();
            $table->float('lat', 11, 2)->nullable();
            $table->point('position')->spatialIndex('spacial_position_index');
            $table->integer('type_id')->index('type_id_index');
            $table->integer('status_id')->index('type_id_index');
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('task_status', function(Blueprint $table){
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->uuid('uuid')->nullable()->unique('unique_uuid_index');
            $table->string('name', 100)->nullable()->change();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });

        Schema::create('task_types', function(Blueprint $table){
            $table->engine = 'InnoDB';
            $table->charset = 'utf8';
            $table->collation = 'utf8_unicode_ci';
            $table->increments('id');
            $table->uuid('uuid')->nullable()->unique('unique_uuid_index');
            $table->string('name', 100)->nullable()->change();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::dropIfExists('tasks');
//        Schema::dropIfExists('task_status');
//        Schema::dropIfExists('task_types');
    }
}
