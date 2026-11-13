
<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use RuntimeException;

return new class extends Migration
{
    public function up(): void
    {
        /*
         * Recuperar los dos géneros permitidos
         * desde el catálogo existente.
         */
        $allowedGenders = DB::table('genders')
            ->whereIn('name', [
                'M (Masculino)',
                'F (Femenino)',
            ])
            ->get(['id', 'name']);

        $genderNames = $allowedGenders
            ->pluck('name')
            ->sort()
            ->values()
            ->all();

        if ($genderNames !== [
            'F (Femenino)',
            'M (Masculino)',
        ]) {
            throw new RuntimeException(
                'No se encontraron exactamente los géneros permitidos M y F.'
            );
        }
        /*
         * Tabla auxiliar de géneros permitidos.
         */
        Schema::create('student_allowed_genders', function (Blueprint $table) {
            $table->unsignedBigInteger('gender_id');

            $table->primary('gender_id');

            $table->foreign('gender_id', 'fk_sag_gender')
                ->references('id')
                ->on('genders')
                ->restrictOnUpdate()
                ->restrictOnDelete();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });

        DB::table('student_allowed_genders')->insert(
            $allowedGenders->map(fn($gender) => [
                'gender_id' => $gender->id,
            ])->all()
        );

        /*
         * Tabla principal de estudiantes.
         */
        Schema::create('students', function (Blueprint $table) {
            $table->char('id', 26)->primary();

            $table->char('institution_id', 26);

            $table->string('first_name', 100);
            $table->string('middle_name', 100)->nullable();
            $table->string('last_name', 100);
            $table->string('second_last_name', 100)->nullable();

            $table->date('birth_date')->nullable();

            $table->unsignedBigInteger('gender_id')->nullable();

            $table->unsignedBigInteger('document_type_id');

            $table->string('document_number_original', 100);

            $table->string('document_number_normalized', 100)
                ->collation('utf8mb4_nopad_bin');

            $table->string('status', 20)->default('active');

            $table->timestamps();

            /*
             * Claves candidatas e índices únicos.
             */
            $table->unique(
                ['institution_id', 'id'],
                'uq_students_institution_id'
            );

            $table->unique(
                [
                    'institution_id',
                    'document_type_id',
                    'document_number_normalized',
                ],
                'uq_students_institution_document'
            );

            /*
             * Relaciones institucionales y catálogos.
             */
            $table->foreign('institution_id', 'fk_students_institution')
                ->references('id')
                ->on('institutions')
                ->restrictOnUpdate()
                ->restrictOnDelete();

            $table->foreign('document_type_id', 'fk_students_document_type')
                ->references('id')
                ->on('typedocuments')
                ->restrictOnUpdate()
                ->restrictOnDelete();

            $table->foreign('gender_id', 'fk_students_allowed_gender')
                ->references('gender_id')
                ->on('student_allowed_genders')
                ->restrictOnUpdate()
                ->restrictOnDelete();

            $table->engine = 'InnoDB';
            $table->charset = 'utf8mb4';
            $table->collation = 'utf8mb4_unicode_ci';
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');

        Schema::dropIfExists('student_allowed_genders');
    }
};
