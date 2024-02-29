<?php

namespace Sdkconsultoria\WhatsappCloudApi\Models;

use Illuminate\Database\Eloquent\Model;
use Sdkconsultoria\WhatsappCloudApi\Events\NewWhatsappMessageHook;

class Message extends Model
{
    public const STATUS_SENDED = 0;

    public $timestamps = false;

    public static function processMessage($messageEvent): void
    {
        // \Log::debug('Message received', $messageEvent);

        if (isset($messageEvent['messages'])) {
            self::processConversation($messageEvent);
        }

        if (isset($messageEvent['statuses'])) {
        }
    }

    private static function processConversation(array $messageEvent)
    {
        $content = $messageEvent['messages'][0];
        $chat = Chat::findOrCreateChat($content['from'], $messageEvent['metadata']['display_phone_number']);

        switch ($content['type']) {
            case 'text':
            case 'audio':
            case 'sticker':
            case 'image':
                self::processTextMessage($chat, $content);
                break;
            case 'reaction':
                break;
        }

        NewWhatsappMessageHook::dispatch(['chat_id' => $chat->id]);
    }

    private static function processTextMessage(Chat $chat, array $content): void
    {
        $messageModel = new Message();
        $messageModel->chat_id = $chat->id;
        $messageModel->message_id = $content['id'];
        $messageModel->timestamp = $content['timestamp'];
        $messageModel->status = self::STATUS_SENDED;
        $messageModel->type = $content['type'];
        $messageModel->body = json_encode($content);
        $messageModel->direction = 'toApp';
        $messageModel->save();
    }
}
