<?php

declare(strict_types=1);

namespace App\Models;

use App\Modules\Institution\Models\Institution;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Validation\ValidationException;

final class Student extends Model
{
    use HasUlids;

    protected $table = 'students';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'institution_id',
        'first_name',
        'middle_name',
        'last_name',
        'second_last_name',
        'birth_date',
        'gender_id',
        'document_type_id',
        'document_number_original',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Student $student): void {
            $original = $student->getAttribute(
                'document_number_original'
            );

            if (! is_string($original)) {
                throw ValidationException::withMessages([
                    'document_number_original' =>
                        'El número de documento original es obligatorio.',
                ]);
            }

            if (mb_strlen($original, 'UTF-8') > 100) {
                throw ValidationException::withMessages([
                    'document_number_original' =>
                        'El número de documento no puede superar los 100 caracteres.',
                ]);
            }

            $normalized = mb_strtoupper(
                trim($original),
                'UTF-8'
            );

            if ($normalized === '') {
                throw ValidationException::withMessages([
                    'document_number_original' =>
                        'El número de documento no puede estar vacío.',
                ]);
            }

            if (mb_strlen($normalized, 'UTF-8') > 100) {
                throw ValidationException::withMessages([
                    'document_number_original' =>
                        'El documento normalizado no puede superar los 100 caracteres.',
                ]);
            }

            $student->setAttribute(
                'document_number_normalized',
                $normalized
            );
        });
    }

    public function institution(): BelongsTo
    {
        return $this->belongsTo(
            Institution::class,
            'institution_id',
            'id'
        );
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(
            DocumentType::class,
            'document_type_id',
            'id'
        );
    }

    public function gender(): BelongsTo
    {
        return $this->belongsTo(
            Gender::class,
            'gender_id',
            'id'
        );
    }
}
