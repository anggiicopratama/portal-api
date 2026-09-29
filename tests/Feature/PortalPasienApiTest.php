<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PortalPasienApiTest extends TestCase
{
    public function test_token_can_be_created_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/token/create', [
            'username' => 'edp',
            'password' => 'edpsip',
        ]);

        $response
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('token_type', 'Bearer')
            ->assertJsonStructure(['token', 'expires_at']);
    }

    public function test_token_creation_rejects_invalid_credentials(): void
    {
        $this->postJson('/api/token/create', [
            'username' => 'edp',
            'password' => 'salah',
        ])->assertUnauthorized();
    }

    public function test_portal_endpoint_requires_bearer_token(): void
    {
        $response = $this->getJson('/api/portal-pasien/skdp?no_kartu=123&bln=9&thn=2026');

        $response
            ->assertUnauthorized()
            ->assertHeader('WWW-Authenticate', 'Bearer')
            ->assertJsonPath('success', false);
    }

    public function test_skdp_endpoint_forwards_validated_query_parameters(): void
    {
        Http::fake([
            '*' => Http::response(['status' => 'ok'], 200),
        ]);

        $response = $this
            ->withToken($this->createToken())
            ->getJson('/api/portal-pasien/skdp?no_kartu=000123&bln=9&thn=2026');

        $response->assertOk()->assertExactJson(['status' => 'ok']);

        Http::assertSent(function ($request): bool {
            parse_str((string) parse_url($request->url(), PHP_URL_QUERY), $query);

            return $query === [
                'bln' => '9',
                'thn' => '2026',
                'noka' => '000123',
                'filter' => '1',
            ];
        });
    }

    public function test_skdp_endpoint_rejects_invalid_month(): void
    {
        $response = $this
            ->withToken($this->createToken())
            ->getJson('/api/portal-pasien/skdp?no_kartu=123&bln=13&thn=2026');

        $response->assertUnprocessable()->assertJsonValidationErrors('bln');
    }

    private function createToken(): string
    {
        return (string) $this->postJson('/api/token/create', [
            'username' => 'edp',
            'password' => 'edpsip',
        ])->json('token');
    }
}
