<?php

namespace Database\Seeders;

use App\Models\Person;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->people() as $person) {
            $this->seedPerson($person);
        }
    }

    protected function seedPerson(array $attributes): void
    {
        Person::query()->updateOrCreate(
            ['name' => $attributes['name']],
            $attributes,
        );
    }

    protected function people(): array
    {
        return [
            $this->person(
                name: 'Ariana Grande',
                fullName: 'Ariana Grande-Butera',
                birthDate: '1993-06-26',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Boca Raton',
            ),
            $this->person(
                name: 'Zendaya',
                fullName: 'Zendaya Maree Stoermer Coleman',
                birthDate: '1996-09-01',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Oakland',
            ),
            $this->person(
                name: 'Margot Robbie',
                fullName: 'Margot Elise Robbie',
                birthDate: '1990-07-02',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'Australia',
                birthCity: 'Dalby',
            ),
            $this->person(
                name: 'Sadie Sink',
                fullName: 'Sadie Elizabeth Sink',
                birthDate: '2002-04-16',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Brenham',
            ),
            $this->person(
                name: 'Taylor Swift',
                fullName: 'Taylor Alison Swift',
                birthDate: '1989-12-13',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Reading',
            ),
            $this->person(
                name: 'Millie Bobby Brown',
                fullName: 'Millie Bonnie Brown',
                birthDate: '2004-02-19',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'Spain',
                birthCity: 'Marbella',
            ),
            $this->person(
                name: 'Rachel Zegler',
                fullName: 'Rachel Anne Zegler',
                birthDate: '2001-05-03',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Hackensack',
            ),
            $this->person(
                name: 'Brooke Monk',
                fullName: 'Brooke Monk',
                birthDate: '2003-01-31',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Jacksonville',
            ),
            $this->person(
                name: 'Pokimane',
                fullName: 'Imane Anys',
                birthDate: '1996-05-14',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'Morocco',
                birthCity: 'Marrakesh',
            ),
            $this->person(
                name: 'Valkyrae',
                fullName: 'Rachell Hofstetter',
                birthDate: '1992-01-08',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Moses Lake',
            ),
            $this->person(
                name: 'QT Cinderella',
                fullName: 'Blaire',
                birthDate: '1994-12-06',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Los Angeles',
            ),
            $this->person(
                name: 'Jenna Ortega',
                fullName: 'Jenna Marie Ortega',
                birthDate: '2002-09-27',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Palm Desert',
            ),
            $this->person(
                name: 'Emma Myers',
                fullName: 'Emma Elizabeth Myers',
                birthDate: '2002-04-02',
                deathDate: null,
                gender: 'female',
                sexuality: 'straight',
                birthCountry: 'United States of America',
                birthCity: 'Orlando',
            ),
            $this->person(
                name: 'Robert Pattinson',
                fullName: 'Robert Douglas Thomas Pattinson',
                birthDate: '1987-02-09',
                deathDate: null,
                gender: 'male',
                sexuality: 'straight',
                birthCountry: 'United Kingdom',
                birthCity: 'London',
            ),
        ];
    }

    protected function person(
        string $name,
        string $fullName,
        string $birthDate,
        ?string $deathDate,
        string $gender,
        string $sexuality,
        string $birthCountry,
        string $birthCity,
        bool $isPublished = true,
        bool $isHidden = false,
        ?string $picture = null,
    ): array {
        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'full_name' => $fullName,
            'birth_date' => $birthDate,
            'death_date' => $deathDate,
            'gender' => $gender,
            'sexuality' => $sexuality,
            'birth_country' => $birthCountry,
            'birth_city' => $birthCity,
            'is_published' => $isPublished,
            'is_hidden' => $isHidden,
            'picture' => $picture,
        ];
    }
}
