<?php

namespace Tests\Feature;

use App\Models\City;
use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomerAddressTest extends TestCase
{
    use RefreshDatabase;

    private function createCustomer(): User
    {
        $city = City::create(['name' => 'Kota Yogyakarta', 'is_active' => true, 'province' => 'DI Yogyakarta']);

        return User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
            'phone' => '0812'.fake()->unique()->numerify('########'),
            'nik' => fake()->unique()->numerify('################'),
            'city_id' => $city->id,
            'address' => 'Jl. Malioboro No. 10',
        ]);
    }

    public function test_customer_can_access_addresses_page(): void
    {
        $customer = $this->createCustomer();

        $this->actingAs($customer)
            ->get(route('customer.addresses'))
            ->assertOk()
            ->assertSee('Alamat Saya');
    }

    public function test_customer_can_access_create_and_edit_address_pages(): void
    {
        $customer = $this->createCustomer();
        $city = City::first();

        $this->actingAs($customer)
            ->get(route('customer.addresses.create'))
            ->assertOk()
            ->assertSee('Tambah Alamat Baru');

        $addr = CustomerAddress::create([
            'user_id' => $customer->id,
            'label' => 'Rumah',
            'recipient_name' => 'Dewi',
            'phone' => '081234567890',
            'city_id' => $city->id,
            'address' => 'Jl. Kaliurang',
            'is_primary' => true,
        ]);

        $this->actingAs($customer)
            ->get(route('customer.addresses.edit', $addr->id))
            ->assertOk()
            ->assertSee('Ubah Alamat');
    }

    public function test_mitra_cannot_access_customer_addresses_page(): void
    {
        $city = City::create(['name' => 'Kota Sleman', 'is_active' => true, 'province' => 'DI Yogyakarta']);
        $mitra = User::factory()->create([
            'role' => 'mitra',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
            'phone' => '0813'.fake()->unique()->numerify('########'),
            'nik' => fake()->unique()->numerify('################'),
            'city_id' => $city->id,
        ]);

        $this->actingAs($mitra)
            ->get(route('customer.addresses'))
            ->assertRedirect(route('mitra.dashboard'));
    }

    public function test_customer_can_create_multiple_addresses(): void
    {
        $city = City::create(['name' => 'Kota Bantul', 'is_active' => true, 'province' => 'DI Yogyakarta']);
        $customer = User::factory()->create([
            'role' => 'customer',
            'status' => 'active',
            'verified' => true,
            'is_completed' => true,
            'email_verified_at' => now(),
            'phone' => '0812'.fake()->unique()->numerify('########'),
            'nik' => fake()->unique()->numerify('################'),
            'city_id' => $city->id,
            'address' => null,
        ]);

        Livewire::actingAs($customer)
            ->test(\App\Livewire\Customer\Addresses\Form::class)
            ->set('label', 'Rumah')
            ->set('recipient_name', 'Budi Santoso')
            ->set('phone', '081299998888')
            ->set('city_id', $city->id)
            ->set('address', 'Jl. Kaliurang KM 5 No. 12')
            ->set('district', 'Depok')
            ->set('village', 'Caturtunggal')
            ->set('benchmark', 'Pagar Hitam')
            ->set('is_primary', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('customer.addresses'));

        $this->assertDatabaseHas('customer_addresses', [
            'user_id' => $customer->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi Santoso',
            'is_primary' => 1,
        ]);

        // Tambah alamat kedua (Kantor)
        Livewire::actingAs($customer)
            ->test(\App\Livewire\Customer\Addresses\Form::class)
            ->set('label', 'Kantor')
            ->set('recipient_name', 'Budi Kantor')
            ->set('phone', '081299998888')
            ->set('city_id', $city->id)
            ->set('address', 'Gedung Cyber Lt. 3')
            ->set('is_primary', false)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('customer.addresses'));

        $this->assertEquals(2, CustomerAddress::where('user_id', $customer->id)->count());
    }

    public function test_customer_can_set_address_as_primary(): void
    {
        $customer = $this->createCustomer();
        $city = City::first();

        $addr1 = CustomerAddress::create([
            'user_id' => $customer->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081299998888',
            'city_id' => $city->id,
            'address' => 'Alamat 1',
            'is_primary' => true,
        ]);

        $addr2 = CustomerAddress::create([
            'user_id' => $customer->id,
            'label' => 'Kantor',
            'recipient_name' => 'Budi',
            'phone' => '081299998888',
            'city_id' => $city->id,
            'address' => 'Alamat 2',
            'is_primary' => false,
        ]);

        Livewire::actingAs($customer)
            ->test(\App\Livewire\Customer\Addresses\Index::class)
            ->call('setAsPrimary', $addr2->id)
            ->assertSee('Alamat utama berhasil diubah.');

        $this->assertFalse((bool) $addr1->fresh()->is_primary);
        $this->assertTrue((bool) $addr2->fresh()->is_primary);

        // Dan profil user tersinkron
        $this->assertSame('Alamat 2', $customer->fresh()->address);

        // Switch kembali ke alamat 1
        Livewire::actingAs($customer)
            ->test(\App\Livewire\Customer\Addresses\Index::class)
            ->call('setAsPrimary', $addr1->id)
            ->assertSee('Alamat utama berhasil diubah.');

        $this->assertTrue((bool) $addr1->fresh()->is_primary);
        $this->assertFalse((bool) $addr2->fresh()->is_primary);
        $this->assertSame('Alamat 1', $customer->fresh()->address);
    }

    public function test_customer_can_edit_address_and_set_as_primary_in_form(): void
    {
        $customer = $this->createCustomer();
        $city = City::first();

        $addr1 = CustomerAddress::create([
            'user_id' => $customer->id,
            'label' => 'Rumah',
            'recipient_name' => 'Budi',
            'phone' => '081299998888',
            'city_id' => $city->id,
            'address' => 'Alamat Awal',
            'is_primary' => true,
        ]);

        $addr2 = CustomerAddress::create([
            'user_id' => $customer->id,
            'label' => 'Kantor',
            'recipient_name' => 'Budi',
            'phone' => '081299998888',
            'city_id' => $city->id,
            'address' => 'Alamat Kedua',
            'is_primary' => false,
        ]);

        Livewire::actingAs($customer)
            ->test(\App\Livewire\Customer\Addresses\Form::class, ['address' => $addr2->id])
            ->set('address', 'Alamat Kedua Diperbarui')
            ->set('is_primary', true)
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('customer.addresses'));

        $this->assertFalse((bool) $addr1->fresh()->is_primary);
        $this->assertTrue((bool) $addr2->fresh()->is_primary);
        $this->assertSame('Alamat Kedua Diperbarui', $addr2->fresh()->address);
        $this->assertSame('Alamat Kedua Diperbarui', $customer->fresh()->address);
    }

    public function test_customer_can_delete_address(): void
    {
        $customer = $this->createCustomer();
        $city = City::first();

        $addr = CustomerAddress::create([
            'user_id' => $customer->id,
            'label' => 'Kos',
            'recipient_name' => 'Budi',
            'phone' => '081299998888',
            'city_id' => $city->id,
            'address' => 'Alamat Kos',
            'is_primary' => false,
        ]);

        Livewire::actingAs($customer)
            ->test(\App\Livewire\Customer\Addresses\Index::class)
            ->call('confirmDelete', $addr->id)
            ->call('delete');

        $this->assertDatabaseMissing('customer_addresses', [
            'id' => $addr->id,
        ]);
    }
}
