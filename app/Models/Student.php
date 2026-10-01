<?php

namespace App\Models;

use Database\Factories\StudentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Student extends Model
{
    /** @use HasFactory<StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'nis',
        'nama',
        'kelas',
        'osis_mpk',
        'class_sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Predefined class options sorted according to school hierarchy.
     */
    public const AVAILABLE_CLASSES = Attendance::AVAILABLE_CLASSES;

    /**
     * Compute sort order priority for a given class name.
     */
    public static function getClassSortOrder(string $className): int
    {
        return Attendance::getClassSortOrder($className);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Student $student) {
            if ($student->kelas) {
                $student->class_sort_order = static::getClassSortOrder($student->kelas);
            }
        });
    }

    /**
     * Attendance history records for this student.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}
