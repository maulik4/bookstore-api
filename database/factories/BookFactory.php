<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
 */
class BookFactory extends Factory
{
    protected $model = Book::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'author' => $this->faker->name,
            'description' => $this->faker->paragraph,
            'published' => $this->faker->date,
            'publisher' => $this->faker->company,
            'isbn' => $this->faker->isbn13,
            'genre' => $this->faker->word,
            'image' => [
                'thumbnail' => $this->faker->imageUrl(150, 150),
                'medium' => $this->faker->imageUrl(300, 300),
                'large' => $this->faker->imageUrl(600, 600),
            ]
        ];
    }
}
