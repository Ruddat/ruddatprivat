<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TelegraphChatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Bot-ID oder Bot auswählen
        $bot = TelegraphBot::first();

        if (!$bot) {
            $this->command->error('Es wurde kein TelegraphBot gefunden. Bitte zuerst einen Bot erstellen.');
            return;
        }

        // Chat-Daten definieren
        $chatData = [
            'chat_id' => '6508551813', // Telegram Chat-ID
            'name' => 'Chat #1',      // Name des Chats
            'telegraph_bot_id' => $bot->id,
        ];

        // Prüfen, ob der Chat bereits existiert
        $chat = TelegraphChat::where('chat_id', $chatData['chat_id'])->first();

        if (!$chat) {
            // Chat erstellen
            TelegraphChat::create($chatData);

            $this->command->info("Chat '{$chatData['name']}' wurde erfolgreich erstellt.");
        } else {
            $this->command->warn("Chat mit der ID '{$chatData['chat_id']}' existiert bereits.");
        }
    }
}
