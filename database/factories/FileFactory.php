<?php

namespace Database\Factories;

use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\File>
 */
class FileFactory extends Factory
{
    protected $model = File::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $extension = $this->faker->randomElement(['jpg', 'png', 'pdf', 'doc', 'txt']);
        $originalName = $this->faker->word() . '.' . $extension;

        return [
            'user_id' => User::factory(),
            'original_name' => $originalName,
            'mime_type' => $this->getMimeType($extension),
            'size' => $this->faker->numberBetween(1024, 10485760), // 1KB to 10MB
            'extension' => $extension,
            'disk' => 'local',
            'path' => 'files/' . $this->faker->uuid() . '.' . $extension,
            'key' => $this->faker->uuid(),
            'is_public' => $this->faker->boolean(30), // 30% chance of being public
            'metadata' => json_encode([
                'uploaded_at' => now()->toISOString(),
                'client_ip' => $this->faker->ipv4(),
            ]),
        ];
    }

    /**
     * Get MIME type based on extension
     */
    private function getMimeType(string $extension): string
    {
        return match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'txt' => 'text/plain',
            default => 'application/octet-stream',
        };
    }

    /**
     * Create an image file
     */
    public function image(): static
    {
        return $this->state(function (array $attributes) {
            $extension = $this->faker->randomElement(['jpg', 'png']);
            $originalName = $this->faker->word() . '.' . $extension;

            return [
                'original_name' => $originalName,
                'mime_type' => $this->getMimeType($extension),
                'extension' => $extension,
                'path' => 'images/' . $this->faker->uuid() . '.' . $extension,
            ];
        });
    }

    /**
     * Create a document file
     */
    public function document(): static
    {
        return $this->state(function (array $attributes) {
            $extension = $this->faker->randomElement(['pdf', 'doc', 'docx', 'txt']);
            $originalName = $this->faker->word() . '.' . $extension;

            return [
                'original_name' => $originalName,
                'mime_type' => $this->getMimeType($extension),
                'extension' => $extension,
                'path' => 'documents/' . $this->faker->uuid() . '.' . $extension,
            ];
        });
    }

    /**
     * Create a public file
     */
    public function public(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => true,
        ]);
    }

    /**
     * Create a private file
     */
    public function private(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_public' => false,
        ]);
    }
}
