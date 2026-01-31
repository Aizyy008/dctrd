<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnitPreferencesToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Unit preferences
            $table->string('preferred_length_unit', 10)->default('km')->after('timezone'); // km, mi, m, ft, yd
            $table->string('preferred_mass_unit', 10)->default('kg')->after('preferred_length_unit'); // kg, lb, g, oz, ton
            $table->string('preferred_area_unit', 10)->default('sqm')->after('preferred_mass_unit'); // sqm, sqft, acre, hectare
            $table->string('preferred_temperature_unit', 10)->default('C')->after('preferred_area_unit'); // C, F, K
            
            // Currency preference (if not already exists)
            if (!Schema::hasColumn('users', 'preferred_currency')) {
                $table->string('preferred_currency', 3)->nullable()->after('preferred_temperature_unit');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'preferred_length_unit',
                'preferred_mass_unit',
                'preferred_area_unit',
                'preferred_temperature_unit',
            ]);
            
            // Only drop preferred_currency if we added it
            if (Schema::hasColumn('users', 'preferred_currency')) {
                $table->dropColumn('preferred_currency');
            }
        });
    }
}
