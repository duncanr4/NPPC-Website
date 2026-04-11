<?php

use App\Models\Quote;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {
        $quotes = [
            [
                'text'        => 'I have been in jail four times. I am not afraid of being in jail again.',
                'author_name' => 'John Lewis, Civil Rights Leader & Congressman',
            ],
            [
                'text'        => 'The history of American politics is littered with instances of government officials abusing their power to imprison individuals for their beliefs or associations.',
                'author_name' => 'Edward Snowden',
            ],
            [
                'text'        => 'When a man is denied the right to live the life he believes in, he has no choice but to become an outlaw.',
                'author_name' => 'Nelson Mandela',
            ],
            [
                'text'        => 'Prison bars cannot contain the truth; they only serve to amplify it.',
                'author_name' => 'Martin Luther King Jr.',
            ],
            [
                'text'        => 'We still have hundreds of people that I would categorize as political prisoners. Maybe even thousands, depending on how you categorize them.',
                'author_name' => 'Andrew Young, Former U.S. Ambassador to the United Nations',
            ],
            [
                'text'        => 'Political prisoners are not forgotten heroes; they are our conscience in chains.',
                'author_name' => 'Václav Havel',
            ],
            [
                'text'        => "The struggle for justice doesn't end with the imprisonment of those who fight for it; it only intensifies.",
                'author_name' => 'Angela Davis',
            ],
            [
                'text'        => 'The most certain way to ensure that you are doing nothing to help a political prisoner is to stay silent about their plight.',
                'author_name' => 'Desmond Tutu',
            ],
        ];

        foreach ($quotes as $quote) {
            // Only create if not already present
            if (! Quote::where('text', $quote['text'])->exists()) {
                Quote::create($quote);
            }
        }
    }

    public function down(): void {
        // Don't delete — quotes may have been edited or had images added via admin
    }
};
