<?php

namespace Sdkconsultoria\WhatsappCloudApi\Tests\Feature\Conversation;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Sdkconsultoria\WhatsappCloudApi\Models\Campaign;
use Sdkconsultoria\WhatsappCloudApi\Tests\Fake\Message\FakeMessageCreteResponse;
use Sdkconsultoria\WhatsappCloudApi\Tests\TestCase;

class CreateCampaignTest extends TestCase
{
    use WithFaker;

    public function test_create_campaign_without_vars()
    {
        $messageId = 'wamid.'.$this->faker->numberBetween(111, 450);
        $campaign = Campaign::factory()->make();
        $phones = [
            $this->faker->e164PhoneNumber(),
            $this->faker->e164PhoneNumber(),
            $this->faker->e164PhoneNumber(),
        ];

        Http::fake([
            '*/messages' => Http::response(FakeMessageCreteResponse::getFakeMessageCreateResponse($messageId)),
        ]);

        $this->post(route('campaign.store'), [
            'name' => $campaign->name,
            'waba_phone_id' => $campaign->waba_phone_id,
            'template_id' => $campaign->template_id,
            'phones' => $phones,
        ])->assertStatus(200);

        $this->assertDatabaseHas('campaigns', [
            'name' => $campaign->name,
            'waba_phone_id' => $campaign->waba_phone_id,
            'template_id' => $campaign->template_id,
            'total_messages' => count($phones),
        ]);

        foreach ($phones as $phone) {
            $this->assertDatabaseHas('chats', [
                'client_phone' => $phone,
            ]);
        }
    }
}
