<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Core\Audit\DTO\AuditSubject;
use PHPUnit\Framework\TestCase;

final class AuditSubjectTest extends TestCase
{
    public function test_creates_subject_with_integer_identifier(): void
    {
        $subject = new AuditSubject(
            type: 'user',
            id: 25,
        );

        $this->assertSame('user', $subject->type);
        $this->assertSame(25, $subject->id);
    }

    public function test_creates_subject_with_string_identifier(): void
    {
        $subject = new AuditSubject(
            type: 'role',
            id: 'teacher',
        );

        $this->assertSame('role', $subject->type);
        $this->assertSame('teacher', $subject->id);
    }

    public function test_preserves_subject_type(): void
    {
        $subject = new AuditSubject(
            type: 'permission',
            id: 17,
        );

        $this->assertSame('permission', $subject->type);
    }

    public function test_allows_null_identifier(): void
    {
        $subject = new AuditSubject(
            type: 'system',
            id: null,
        );

        $this->assertSame('system', $subject->type);
        $this->assertNull($subject->id);
    }
}
