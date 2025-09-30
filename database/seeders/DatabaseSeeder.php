<?php

namespace Database\Seeders;

use App\Models\User;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Beispielbenutzer erstellen
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // TelegraphBot erstellen
        $bot = TelegraphBot::firstOrCreate(
            ['token' => '7858334051:AAEjhcTmVqTabzqh0UXhIxt5YLXtvX-Mosk'], // Ersetze durch den tatsächlichen Bot-Token
            ['name' => 'RuddatBot'],    // Bot-Name
        );

        $this->command->info("Bot '{$bot->name}' wurde erfolgreich erstellt oder existierte bereits.");

        // TelegraphChat erstellen
        if ($bot) {
            $chat = TelegraphChat::firstOrCreate(
                ['chat_id' => '6508551813'], // Telegram Chat-ID
                [
                    'name' => 'Chat #1',      // Name des Chats
                    'telegraph_bot_id' => $bot->id,
                ],
            );

            if ($chat->wasRecentlyCreated) {
                $this->command->info("Chat '{$chat->name}' wurde erfolgreich erstellt.");
            } else {
                $this->command->warn("Chat '{$chat->name}' existiert bereits.");
            }
        } else {
            $this->command->error('Es konnte kein Bot erstellt oder gefunden werden.');
        }

        // Weitere Seeder aufrufen
        $this->call([
            LandingPageSeeder::class,
            AccountSeeder::class,
            FiscalYearSeeder::class,
          //  UtilityCostsUserSeeder::class,
          //  UtilityCostsSeeder::class,
            UserTestSeeder::class,
            BwaGroupSeeder::class,
            BkBalanceGroup::class,
            BkBookingTemplateSeeder::class,
        ]);

    }
}
