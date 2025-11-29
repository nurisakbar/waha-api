<?php

namespace Database\Seeders;

use App\Models\Message;
use App\Models\WhatsAppSession;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class MessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sessions = WhatsAppSession::where('status', 'connected')->get();

        if ($sessions->isEmpty()) {
            $this->command->warn('No connected sessions found. Please run WhatsAppSessionSeeder first.');
            return;
        }

        $messages = [];
        $messageTypes = ['text', 'image', 'video', 'audio', 'document'];
        $statuses = ['sent', 'delivered', 'read', 'pending'];
        $directions = ['incoming', 'outgoing'];

        // Sample phone numbers
        $phoneNumbers = [
            '+6281234567890',
            '+6281234567891',
            '+6281234567892',
            '+6281234567893',
            '+6281234567894',
        ];

        // Sample message contents
        $textMessages = [
            'Hello, how are you?',
            'Thank you for your message!',
            'I will get back to you soon.',
            'Have a great day!',
            'Can we schedule a meeting?',
            'The project is progressing well.',
            'Please confirm your attendance.',
            'Looking forward to hearing from you.',
            'Thank you for your patience.',
            'Let me know if you need anything.',
        ];

        foreach ($sessions as $session) {
            $user = $session->user;
            $messageCount = rand(10, 50); // 10-50 messages per session

            for ($i = 0; $i < $messageCount; $i++) {
                $direction = $directions[array_rand($directions)];
                $messageType = $messageTypes[array_rand($messageTypes)];
                $status = $statuses[array_rand($statuses)];

                // Determine from/to numbers based on direction
                if ($direction === 'incoming') {
                    $fromNumber = $phoneNumbers[array_rand($phoneNumbers)];
                    $toNumber = $user->phone ?? '+6281234567890';
                } else {
                    $fromNumber = $user->phone ?? '+6281234567890';
                    $toNumber = $phoneNumbers[array_rand($phoneNumbers)];
                }

                $createdAt = now()->subDays(rand(0, 30))->subHours(rand(0, 23))->subMinutes(rand(0, 59));

                // Initialize message with all possible columns
                $message = [
                    'id' => Str::uuid()->toString(),
                    'user_id' => $user->id,
                    'session_id' => $session->id,
                    'whatsapp_message_id' => 'WA' . Str::random(16),
                    'from_number' => $fromNumber,
                    'to_number' => $toNumber,
                    'message_type' => $messageType,
                    'direction' => $direction,
                    'status' => $status,
                    'content' => null,
                    'media_url' => null,
                    'media_mime_type' => null,
                    'media_size' => null,
                    'caption' => null,
                    'error_message' => null,
                    'sent_at' => null,
                    'delivered_at' => null,
                    'read_at' => null,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ];

                // Add content based on message type
                if ($messageType === 'text') {
                    $message['content'] = $textMessages[array_rand($textMessages)];
                } else {
                    $message['media_url'] = 'https://example.com/media/' . Str::random(10) . '.' . ($messageType === 'image' ? 'jpg' : ($messageType === 'video' ? 'mp4' : 'pdf'));
                    $message['media_mime_type'] = $messageType === 'image' ? 'image/jpeg' : ($messageType === 'video' ? 'video/mp4' : 'application/pdf');
                    $message['media_size'] = rand(1000, 5000000);
                    $message['caption'] = $textMessages[array_rand($textMessages)];
                }

                // Add status timestamps
                if ($status === 'sent' || $status === 'delivered' || $status === 'read') {
                    $message['sent_at'] = $createdAt;
                }
                if ($status === 'delivered' || $status === 'read') {
                    $message['delivered_at'] = $createdAt->copy()->addMinutes(rand(1, 5));
                }
                if ($status === 'read') {
                    $message['read_at'] = $createdAt->copy()->addMinutes(rand(5, 30));
                }

                $messages[] = $message;
            }
        }

        // Insert in chunks for better performance
        foreach (array_chunk($messages, 100) as $chunk) {
            Message::insert($chunk);
        }

        $this->command->info('Created ' . count($messages) . ' messages.');
    }
}

