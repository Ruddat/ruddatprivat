<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DefStudio\Telegraph\Models\TelegraphBot;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TelegraphBotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Telegram Bot erstellen
        TelegraphBot::create([
            'name' => 'RuddatBot', // Name deines Bots
            'token' => '7858334051:AAEjhcTmVqTabzqh0UXhIxt5YLXtvX-Mosk', // Token aus der .env-Datei
        ]);

        $this->command->info('TelegramBot erfolgreich erstellt!');
    }
}
