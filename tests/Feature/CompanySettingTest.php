<?php

namespace Tests\Feature;

use App\Models\CompanySetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CompanySettingTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_can_update_company_name_and_logo_for_pdf_reports(): void
    {
        Storage::fake('public');

        $operator = User::create([
            'name' => 'Operator Test',
            'username' => 'operatortest',
            'email' => 'operator@test.com',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);

        $this->actingAs($operator)
            ->get(route('company-settings.edit'))
            ->assertOk()
            ->assertSee('Nama Perusahaan')
            ->assertSee('Alamat Perusahaan')
            ->assertSee('Logo Perusahaan');

        $this->put(route('company-settings.update'), [
            'company_name' => 'Koperasi Nusantara',
            'company_address' => 'Jl. Merdeka No. 10, Jakarta',
            'logo' => UploadedFile::fake()->image('logo.png'),
        ])->assertRedirect(route('company-settings.edit'));

        $setting = CompanySetting::where('user_id', $operator->id)->firstOrFail();
        $this->assertSame('Koperasi Nusantara', $setting->company_name);
        $this->assertSame('Jl. Merdeka No. 10, Jakarta', $setting->company_address);
        Storage::disk('public')->assertExists($setting->logo_path);

        $header = view('laporan.partials.company-header')->render();
        $this->assertStringContainsString('Koperasi Nusantara', $header);
        $this->assertStringContainsString('Jl. Merdeka No. 10, Jakarta', $header);
        $this->assertStringContainsString('data:image/png;base64,', $header);

        $this->get(route('laporan.pdf-pinjaman'))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_company_settings_are_scoped_to_the_authenticated_user(): void
    {
        $operator = User::create([
            'name' => 'Operator Test',
            'username' => 'operatortest',
            'email' => 'operator@test.com',
            'password' => 'secret123',
            'role' => 'operator',
            'is_active' => true,
        ]);

        $this->actingAs($operator)
            ->put(route('company-settings.update'), [
                'company_name' => 'Koperasi Uji',
                'company_address' => 'Alamat Uji',
            ])
            ->assertRedirect(route('company-settings.edit'));

        $this->assertDatabaseHas('company_settings', [
            'user_id' => $operator->id,
            'company_name' => 'Koperasi Uji',
            'company_address' => 'Alamat Uji',
        ]);
        $this->assertDatabaseCount('company_settings', 1);
    }
}
