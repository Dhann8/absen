<?php

namespace App\Models;

use Database\Factories\AttendanceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    /** @use HasFactory<AttendanceFactory> */
    use HasFactory;

    protected $fillable = [
        'student_id',
        'tanggal',
        'nama',
        'kelas',
        'osis_mpk',
        'class_sort_order',
        'status',
        'kelengkapan',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Get the student associated with the attendance.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Predefined class options sorted according to school hierarchy:
     * X RPL 1 - X RPL 11, X DKV 1 - X DKV 3, XI RPL 1 - XI RPL 7, XI DKV 1 - XI DKV 3
     */
    public const AVAILABLE_CLASSES = [
        'X RPL 1', 'X RPL 2', 'X RPL 3', 'X RPL 4', 'X RPL 5', 'X RPL 6', 'X RPL 7', 'X RPL 8', 'X RPL 9', 'X RPL 10', 'X RPL 11',
        'X DKV 1', 'X DKV 2', 'X DKV 3',
        'XI RPL 1', 'XI RPL 2', 'XI RPL 3', 'XI RPL 4', 'XI RPL 5', 'XI RPL 6', 'XI RPL 7',
        'XI DKV 1', 'XI DKV 2', 'XI DKV 3',
    ];

    /**
     * Compute sort order priority for a given class name.
     */
    public static function getClassSortOrder(string $className): int
    {
        $index = array_search(trim($className), self::AVAILABLE_CLASSES, true);
        if ($index !== false) {
            return $index + 1;
        }

        // Fallback for custom names
        return 999;
    }

    protected static function boot(): void
    {
        parent::boot();

        static::saving(function (Attendance $attendance) {
            if ($attendance->kelas) {
                $attendance->class_sort_order = static::getClassSortOrder($attendance->kelas);
            }
        });
    }
}
