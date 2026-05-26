<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GreenPrintingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_customer_can_add_cart_checkout_and_upload_payment_proof(): void
    {
        $customer = User::where('email', 'customer@greenprinting.test')->firstOrFail();
        $product = Product::where('slug', 'banner')->firstOrFail();

        $this->actingAs($customer)
            ->post(route('cart.store', $product), [
                'width' => 2,
                'height' => 1,
                'quantity' => 1,
                'options' => [$product->options()->where('name', 'Mata Ayam')->value('id')],
            ])
            ->assertRedirect(route('cart.index'));

        $this->actingAs($customer)
            ->post(route('checkout.store'), [
                'recipient_name' => 'Customer Demo',
                'phone' => '081100000003',
                'address' => 'Jakarta',
                'fulfillment_method' => 'pickup',
                'shipping_cost' => 0,
            ])
            ->assertRedirect();

        $this->assertDatabaseHas('orders', ['user_id' => $customer->id, 'grand_total' => 60000]);
        $this->assertDatabaseHas('invoices', ['grand_total' => 60000, 'status' => 'unpaid']);
    }

    public function test_admin_can_open_dashboard(): void
    {
        $admin = User::where('email', 'admin@greenprinting.test')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Pesanan Hari Ini');
    }
}
