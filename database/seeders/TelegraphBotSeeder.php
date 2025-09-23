<?php

namespace Database\Seeders;

use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Database\Seeder;

class TelegraphBotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Bot-Daten definieren
        $botData = [
            'token' => '7858334051:AAEjhcTmVqTabzqh0UXhIxt5YLXtvX-Mosk', // Ersetze durch den tatsächlichen Bot-Token
            'name' => 'RuddatBot',   // Name des Bots
        ];

        // Prüfen, ob der Bot bereits existiert
        $bot = TelegraphBot::where('token', $botData['token'])->first();

        if (! $bot) {
            // Bot erstellen
            TelegraphBot::create($botData);

            $this->command->info("Bot '{$botData['name']}' wurde erfolgreich erstellt.");
        } else {
            $this->command->warn("Bot mit dem Token '{$botData['token']}' existiert bereits.");
        }
    }
}
