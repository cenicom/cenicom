<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Academic;

use App\Modules\Institution\Domain\Contracts\InstitutionRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\Support\AcademicYear02TestCase;

final class InstitutionRepositoryFixtureSmokeTest extends AcademicYear02TestCase
{
    public function test_institution_repository_can_lock_and_hydrate_isolated_fixture(): void
    {
        $connection = DB::connection('mysql');

        $this->assertSame(
            'cenicom_aca_year02_isolated_test',
            $connection->getDatabaseName(),
            'La prueba solo puede escribir en la base ACA-YEAR-02 aislada.',
        );

        $id = (string) Str::ulid();
        $code = 'ACA-TEST-' . substr($id, 0, 10);
        $shortCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 8);
        $now = now();

        DB::table('institutions')->insert([
            'id' => $id,
            'code' => $code,
            'short_code' => $shortCode,
            'name' => 'Institución fixture ACA-YEAR-02',
            'official_registration_country' => null,
            'official_registration_authority' => null,
            'official_registration_value' => null,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        try {
            $institution = DB::transaction(
                function () use ($id): ?\App\Modules\Institution\Domain\Entity\Institution {
                    return app(InstitutionRepositoryInterface::class)
                        ->lockForUpdate($id);
                },
                3,
            );

            $this->assertNotNull($institution);
            $this->assertSame($id, $institution->id());
            $this->assertSame('Institución fixture ACA-YEAR-02', $institution->name());
            $this->assertSame($code, $institution->code());
            $this->assertSame($shortCode, $institution->shortCode());
            $this->assertSame('active', $institution->status());
            $this->assertNull($institution->officialRegistration());
        } finally {
            DB::table('institutions')->where('id', $id)->delete();
        }
    }
}
