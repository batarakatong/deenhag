<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('product_code')->nullable()->after('category_id');
            $table->string('service_type')->default('printing')->after('slug');
            $table->json('sample_images')->nullable()->after('image');
            $table->text('file_guidelines')->nullable()->after('description');
            $table->text('technical_specs')->nullable()->after('file_guidelines');
            $table->string('print_method')->nullable()->after('pricing_type');
            $table->string('default_material')->nullable()->after('print_method');
            $table->decimal('waste_percentage', 5, 2)->default(0)->after('default_material');
        });

        Schema::create('product_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->decimal('usage_per_unit', 12, 4)->default(1);
            $table->string('usage_type')->default('per_item');
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
        });

        Schema::create('production_steps', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('status_key')->unique();
            $table->string('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_steps');
        Schema::dropIfExists('product_materials');
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'product_code',
                'service_type',
                'sample_images',
                'file_guidelines',
                'technical_specs',
                'print_method',
                'default_material',
                'waste_percentage',
            ]);
        });
    }
};
