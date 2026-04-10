<?php

use App\Models\Prisoner;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void {
        // Ruchell Cinque Magee — died October 17, 2023
        $magee = Prisoner::where('slug', 'ruchell-cinque-magee')
            ->orWhere('name', 'like', '%Ruchell%Magee%')
            ->first();
        if ($magee) {
            $magee->update(['death_date' => '2023-10-17']);
        }

        // Jamil Abdullah al-Amin (H. Rap Brown) — died November 23, 2025
        $alAmin = Prisoner::where('slug', 'jamil-abdullah-al-amin')
            ->orWhere('name', 'like', '%Jamil%al-Amin%')
            ->orWhere('name', 'like', '%Jamil%Al-Amin%')
            ->first();
        if ($alAmin) {
            $alAmin->update(['death_date' => '2025-11-23']);
        }
    }

    public function down(): void {
        Prisoner::where('slug', 'ruchell-cinque-magee')
            ->orWhere('name', 'like', '%Ruchell%Magee%')
            ->update(['death_date' => null]);

        Prisoner::where('slug', 'jamil-abdullah-al-amin')
            ->orWhere('name', 'like', '%Jamil%al-Amin%')
            ->orWhere('name', 'like', '%Jamil%Al-Amin%')
            ->update(['death_date' => null]);
    }
};
