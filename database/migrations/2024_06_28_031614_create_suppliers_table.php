    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->id();
                $table->string('supplier_name');
                $table->unsignedBigInteger('class_id');
                $table->foreign('class_id')->references('id')->on('class_suppliers');
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->string('email')->nullable();
                $table->string('pic')->nullable();

                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('suppliers');
        }
    };
