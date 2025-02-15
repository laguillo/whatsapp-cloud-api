<?php

namespace Sdkconsultoria\WhatsappCloudApi\Tests\Feature\Waba;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Http;
use Sdkconsultoria\WhatsappCloudApi\Models\Waba;
use Sdkconsultoria\WhatsappCloudApi\Tests\Fake\Waba\FakeWabaResponses;
use Sdkconsultoria\WhatsappCloudApi\Tests\TestCase;

class WabaManagerTest extends TestCase
{
    use WithFaker;

    public function test_get_templates_from_waba()
    {
        $fakeTemplates = FakeWabaResponses::fakeTemplates();
        Http::fake([
            '*/message_templates' => Http::response($fakeTemplates, 200),
        ]);

        $waba = Waba::factory()->create(['waba_id' => '121544050937574']);
        $this->get(route('waba.getTemplatesFromMeta', ['wabaId' => $waba->waba_id]))->assertStatus(200);

        foreach ($fakeTemplates['data'] as $fakeTemplate) {
            $this->assertDatabaseHas('templates', [
                'waba_id' => $waba->waba_id,
                'name' => $fakeTemplate['name'],
            ]);
        }

    }

    public function test_get_templates_from_waba_template_caroucel()
    {
        $fakeTemplates = FakeWabaResponses::fakeTemplateCaroucel();
        Http::fake([
            '*/message_templates' => Http::response($fakeTemplates, 200),
        ]);

        $waba = Waba::factory()->create(['waba_id' => '121544050937574']);
        $this->get(route('waba.getTemplatesFromMeta', ['wabaId' => $waba->waba_id]))->assertStatus(200);

        foreach ($fakeTemplates['data'] as $fakeTemplate) {
            $this->assertDatabaseHas('templates', [
                'waba_id' => $waba->waba_id,
                'name' => $fakeTemplate['name'],
            ]);
        }

        $this->assertDatabaseCount('templates', 1);
    }

    public function test_get_waba_info()
    {
        $wabaId = '104996122399160';
        $wabaFakeInfo = FakeWabaResponses::getFakeWabaInfo();

        Http::fake(["*$wabaId" => Http::response($wabaFakeInfo, 200)]);

        $this->get(route('waba.getInfoFromMeta', ['wabaId' => $wabaId]))->assertStatus(200);

        $this->assertDatabaseHas('wabas', [
            'waba_id' => $wabaFakeInfo['id'],
            'name' => $wabaFakeInfo['name'],
            'timezone_id' => $wabaFakeInfo['timezone_id'],
            'currency' => $wabaFakeInfo['currency'],
            'message_template_namespace' => $wabaFakeInfo['message_template_namespace'],
        ]);
    }

    public function test_get_phone_numbers_from_waba()
    {
        $waba = Waba::factory()->create();
        $wabaId = $waba->waba_id;
        $wabaPhonesFake = FakeWabaResponses::fakePhoneNumbers();

        Http::fake([
            "*$wabaId/phone_numbers" => Http::response($wabaPhonesFake, 200),
            '*/whatsapp_business_profile?fields=about,address,description,email,profile_picture_url,websites,vertical' => Http::response(FakeWabaResponses::fakeBussinesProfile(), 200),
        ]);

        $this->get(route('waba.getPhonesFromMeta', ['wabaId' => $wabaId]))->assertStatus(200);

        foreach ($wabaPhonesFake['data'] as $wabaPhoneFake) {
            $this->assertDatabaseHas('waba_phones', [
                'name' => $wabaPhoneFake['verified_name'],
                'display_phone_number' => $wabaPhoneFake['display_phone_number'],
                'phone_id' => $wabaPhoneFake['id'],
                'quality_rating' => $wabaPhoneFake['quality_rating'],
            ]);
        }
    }

    public function test_waba_init()
    {
        $waba = Waba::factory()->create();
        $wabaId = $waba->waba_id;

        Http::fake([
            "*$wabaId" => Http::response(FakeWabaResponses::getFakeWabaInfo(), 200),
            "*$wabaId/phone_numbers" => Http::response(FakeWabaResponses::fakePhoneNumbers(), 200),
            '*/message_templates' => Http::response(FakeWabaResponses::fakeTemplates(), 200),
            '*/whatsapp_business_profile?fields=about,address,description,email,profile_picture_url,websites,vertical' => Http::response(FakeWabaResponses::fakeBussinesProfile(), 200),
        ]);

        $this->get(route('waba.init', ['wabaId' => $wabaId]))->assertStatus(200);
    }

    public function test_get_phone_numbers_from_waba_no_profile()
    {
        $waba = Waba::factory()->create();
        $wabaId = $waba->waba_id;
        $wabaPhonesFake = FakeWabaResponses::fakePhoneNumbers();

        Http::fake([
            "*$wabaId/phone_numbers" => Http::response($wabaPhonesFake, 200),
        ]);

        $this->get(route('waba.getPhonesFromMeta', ['wabaId' => $wabaId]))->assertStatus(200);

        foreach ($wabaPhonesFake['data'] as $wabaPhoneFake) {
            $this->assertDatabaseHas('waba_phones', [
                'name' => $wabaPhoneFake['verified_name'],
                'display_phone_number' => $wabaPhoneFake['display_phone_number'],
                'phone_id' => $wabaPhoneFake['id'],
                'quality_rating' => $wabaPhoneFake['quality_rating'],
            ]);
        }
    }
}
