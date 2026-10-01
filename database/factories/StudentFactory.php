<?php

namespace Database\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $kelasOptions = Student::AVAILABLE_CLASSES;
        $kelas = fake()->randomElement($kelasOptions);

        return [
            'nis' => fake()->unique()->numerify('10##'),
            'nama' => fake()->name(),
            'kelas' => $kelas,
            'osis_mpk' => fake()->randomElement(['Bukan', 'OSIS', 'MPK']),
            'class_sort_order' => Student::getClassSortOrder($kelas),
            'is_active' => true,
        ];
    }
}
