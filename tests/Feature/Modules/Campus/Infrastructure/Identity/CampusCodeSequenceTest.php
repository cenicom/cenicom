<?php

declare(strict_types=1);

namespace Tests\Feature\Modules\Campus\Infrastructure\Identity;

use App\Modules\Campus\Infrastructure\Identity\CampusCodeSequence;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class CampusCodeSequenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_call_creates_row_and_returns_one(): void
    {
        $institutionId = '01JTESTINSTITUTION000000001';

        $this->createInstitution($institutionId);

        $sequence = new CampusCodeSequence();

        $value = $sequence->next($institutionId);

        $this->assertSame(1, $value);

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institutionId,
            'current_value' => 1,
        ]);
    }

    public function test_second_call_for_same_institution_returns_two(): void
    {
        $institutionId = '01JTESTINSTITUTION000000002';

        $this->createInstitution($institutionId);

        $sequence = new CampusCodeSequence();

        $this->assertSame(1, $sequence->next($institutionId));
        $this->assertSame(2, $sequence->next($institutionId));
    }

    public function test_different_institution_starts_at_one(): void
    {
        $firstInstitution = '01JTESTINSTITUTION000000003';
        $secondInstitution = '01JTESTINSTITUTION000000004';

        $this->createInstitution($firstInstitution);
        $this->createInstitution($secondInstitution);

        $sequence = new CampusCodeSequence();

        $this->assertSame(1, $sequence->next($firstInstitution));
        $this->assertSame(2, $sequence->next($firstInstitution));

        $this->assertSame(1, $sequence->next($secondInstitution));
    }

    public function test_sequences_are_isolated_by_institution_id(): void
    {
        $firstInstitution = '01JTESTINSTITUTION000000005';
        $secondInstitution = '01JTESTINSTITUTION000000006';

        $this->createInstitution($firstInstitution);
        $this->createInstitution($secondInstitution);

        $sequence = new CampusCodeSequence();

        $sequence->next($firstInstitution);
        $sequence->next($firstInstitution);
        $sequence->next($firstInstitution);

        $sequence->next($secondInstitution);

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $firstInstitution,
            'current_value' => 3,
        ]);

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $secondInstitution,
            'current_value' => 1,
        ]);
    }

    public function test_row_is_created_when_it_does_not_exist(): void
    {
        $institutionId = '01JTESTINSTITUTION000000007';

        $this->createInstitution($institutionId);

        $this->assertDatabaseMissing('campus_code_sequences', [
            'institution_id' => $institutionId,
        ]);

        $sequence = new CampusCodeSequence();

        $value = $sequence->next($institutionId);

        $this->assertSame(1, $value);

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institutionId,
            'current_value' => 1,
        ]);
    }

    public function test_current_value_is_persisted_after_increment(): void
    {
        $institutionId = '01JTESTINSTITUTION000000008';

        $this->createInstitution($institutionId);

        $sequence = new CampusCodeSequence();

        $sequence->next($institutionId);
        $sequence->next($institutionId);

        $storedValue = DB::table('campus_code_sequences')
            ->where('institution_id', $institutionId)
            ->value('current_value');

        $this->assertSame(2, (int) $storedValue);
    }

    public function test_sequence_can_reach_maximum_value_of_999(): void
    {
        $institutionId = '01JTESTINSTITUTION000000009';

        $this->createInstitution($institutionId);

        DB::table('campus_code_sequences')->insert([
            'institution_id' => $institutionId,
            'current_value' => 998,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = new CampusCodeSequence();

        $this->assertSame(999, $sequence->next($institutionId));

        $this->assertDatabaseHas('campus_code_sequences', [
            'institution_id' => $institutionId,
            'current_value' => 999,
        ]);
    }

    public function test_sequence_cannot_exceed_999_or_increment_counter(): void
    {
        $institutionId = '01JTESTINSTITUTION000000010';

        $this->createInstitution($institutionId);

        DB::table('campus_code_sequences')->insert([
            'institution_id' => $institutionId,
            'current_value' => 999,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $sequence = new CampusCodeSequence();

        try {
            $sequence->next($institutionId);
            $this->fail(
                'La secuencia debe rechazar una asignación posterior a 999.'
            );
        } catch (\RuntimeException $exception) {
            $this->assertSame(
                999,
                (int) DB::table('campus_code_sequences')
                    ->where('institution_id', $institutionId)
                    ->value('current_value'),
                'El contador debe permanecer en 999 tras el rechazo.'
            );
        }
    }


    private function createInstitution(string $institutionId): void
    {
        DB::table('institutions')->insert([
            'id' => $institutionId,
            'code' => 'CEN-' . substr($institutionId, -6),
            'name' => 'Institution Test',
            'short_code' => 'INST' . substr($institutionId, -3),
            'status' => 'draft',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->assertDatabaseHas('institutions', [
            'id' => $institutionId,
            'short_code' => 'INST' . substr($institutionId, -3),
        ]);
    }
}
