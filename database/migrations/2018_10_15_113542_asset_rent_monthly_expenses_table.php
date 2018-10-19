<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AssetRentMonthlyExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asset_rent_monthly_expenses', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('project_site_id');
            $table->foreign('project_site_id')->references('id')->on('project_sites')->onUpdate('cascade')->onDelete('cascade');
            $table->unsignedInteger('asset_id');
            $table->foreign('asset_id')->references('id')->on('assets')->onDelete('cascade')->onUpdate('cascade');
            $table->unsignedInteger('year_id');
            $table->foreign('year_id')->references('id')->on('years')->onUpdate('cascade')->onDelete('cascade');
            $table->text('january')->nullable();
            $table->text('february')->nullable();
            $table->text('march')->nullable();
            $table->text('april')->nullable();
            $table->text('may')->nullable();
            $table->text('june')->nullable();
            $table->text('july')->nullable();
            $table->text('august')->nullable();
            $table->text('september')->nullable();
            $table->text('october')->nullable();
            $table->text('november')->nullable();
            $table->text('december')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('asset_rent_monthly_expenses');
    }
}
