<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\Product;
use App\Models\ProductionStep;
use App\Models\Role;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class GreenPrintingSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Admin']);
        $staffRole = Role::create(['name' => 'staff', 'display_name' => 'Staff']);
        $customerRole = Role::create(['name' => 'customer', 'display_name' => 'Customer']);

        User::create(['role_id' => $adminRole->id, 'name' => 'Admin GreenPrinting', 'email' => 'admin@greenprinting.test', 'phone' => '081100000001', 'password' => Hash::make('password')]);
        User::create(['role_id' => $staffRole->id, 'name' => 'Staff Produksi', 'email' => 'staff@greenprinting.test', 'phone' => '081100000002', 'password' => Hash::make('password')]);
        $customer = User::create(['role_id' => $customerRole->id, 'name' => 'Customer Demo', 'email' => 'customer@greenprinting.test', 'phone' => '081100000003', 'password' => Hash::make('password')]);
        $customer->customer()->create(['customer_code' => 'CUST-00003', 'address' => 'Jakarta']);

        $categories = collect(['Digital Printing', 'Offset Printing', 'Large Format', 'Souvenir', 'Stationery', 'Packaging'])
            ->map(fn ($name, $i) => Category::create(['name' => $name, 'slug' => Str::slug($name), 'description' => 'Kategori '.$name, 'sort_order' => $i + 1, 'is_active' => true]));

        $banner = Product::create(['category_id' => $categories[2]->id, 'product_code' => 'GP-LF-001', 'name' => 'Banner', 'slug' => 'banner', 'service_type' => 'printing', 'description' => 'Cetak banner custom untuk promosi toko, event, dan pameran.', 'technical_specs' => 'Resolusi file minimum 100 dpi ukuran asli. Bahan flexi 280-340 gsm.', 'file_guidelines' => 'Upload PDF/AI/CDR/JPG dengan ukuran final dan bleed minimal 1 cm.', 'base_price' => 25000, 'pricing_type' => 'square_meter', 'print_method' => 'Large format digital printing', 'default_material' => 'Flexi China', 'unit' => 'm2', 'estimated_days' => 2, 'min_order_qty' => 1, 'is_custom_size' => true, 'is_active' => true]);
        foreach ([['material', 'Flexi China', 0, 'fixed'], ['material', 'Flexi Korea', 15000, 'per_square_meter'], ['finishing', 'Mata Ayam', 10000, 'fixed'], ['finishing', 'Selongsong', 15000, 'fixed']] as $option) {
            $banner->options()->create(['option_type' => $option[0], 'name' => $option[1], 'price_modifier' => $option[2], 'calculation_type' => $option[3], 'is_active' => true]);
        }

        $card = Product::create(['category_id' => $categories[0]->id, 'product_code' => 'GP-DP-002', 'name' => 'Kartu Nama', 'slug' => 'kartu-nama', 'service_type' => 'printing', 'description' => 'Kartu nama profesional dengan pilihan bahan dan laminasi.', 'technical_specs' => 'Ukuran standar 9 x 5.5 cm. Art carton/BW/Linen.', 'file_guidelines' => 'File PDF/AI/CDR mode warna CMYK, bleed 2 mm.', 'base_price' => 70000, 'pricing_type' => 'package', 'print_method' => 'Digital print / offset', 'default_material' => 'Art Carton 260gsm', 'unit' => 'paket', 'estimated_days' => 3, 'min_order_qty' => 1, 'is_active' => true]);
        foreach ([100 => 70000, 200 => 125000, 500 => 250000, 1000 => 450000] as $qty => $price) {
            $card->variants()->create(['name' => $qty.' pcs', 'price' => $price, 'min_qty' => $qty, 'max_qty' => $qty, 'is_active' => true]);
        }
        foreach ([['lamination', 'Glossy', 15000], ['lamination', 'Doff', 20000], ['side', 'Cetak 2 sisi', 25000]] as $option) {
            $card->options()->create(['option_type' => $option[0], 'name' => $option[1], 'price_modifier' => $option[2], 'calculation_type' => 'fixed', 'is_active' => true]);
        }

        foreach (['Brosur', 'Stiker', 'Poster', 'Undangan', 'Nota', 'Kalender', 'Merchandise'] as $i => $name) {
            Product::create(['category_id' => $categories[$i % $categories->count()]->id, 'product_code' => 'GP-GN-'.str_pad($i + 10, 3, '0', STR_PAD_LEFT), 'name' => $name, 'slug' => Str::slug($name), 'service_type' => $name === 'Merchandise' ? 'merchandise' : 'printing', 'description' => 'Layanan cetak '.$name.' dengan kualitas rapi.', 'technical_specs' => 'Spesifikasi mengikuti pilihan bahan dan finishing.', 'file_guidelines' => 'Upload file siap cetak PDF/AI/CDR/PSD/JPG/PNG.', 'base_price' => 50000 + ($i * 10000), 'pricing_type' => 'pcs', 'print_method' => 'Digital printing', 'unit' => 'pcs', 'estimated_days' => 2 + ($i % 3), 'min_order_qty' => 1, 'is_active' => true]);
        }

        $shirt = Product::create(['category_id' => $categories[3]->id, 'product_code' => 'GP-SB-001', 'name' => 'Sablon Kaos', 'slug' => 'sablon-kaos', 'service_type' => 'sablon', 'description' => 'Sablon kaos custom untuk komunitas, event, merchandise, dan brand apparel.', 'technical_specs' => 'Metode DTF, polyflex, atau screen printing. Area cetak A4/A3/custom.', 'file_guidelines' => 'File desain PNG transparan 300 dpi, AI, PSD, atau PDF. Pisahkan warna untuk screen printing.', 'base_price' => 35000, 'pricing_type' => 'pcs', 'print_method' => 'DTF / Polyflex / Screen Printing', 'default_material' => 'Kaos Cotton Combed 24s/30s', 'unit' => 'pcs', 'estimated_days' => 4, 'min_order_qty' => 1, 'is_active' => true]);
        foreach ([['method', 'DTF A4', 15000], ['method', 'DTF A3', 25000], ['method', 'Screen Printing 1 Warna', 30000], ['finishing', 'Press ulang', 5000]] as $option) {
            $shirt->options()->create(['option_type' => $option[0], 'name' => $option[1], 'price_modifier' => $option[2], 'calculation_type' => 'per_qty', 'is_active' => true]);
        }

        $paper = MaterialCategory::create(['name' => 'Kertas']);
        $ink = MaterialCategory::create(['name' => 'Tinta dan Bahan Cetak']);
        $supplier = Supplier::create(['name' => 'Supplier Utama', 'contact_person' => 'Budi', 'phone' => '08123456789']);
        foreach ([[$ink->id, 'Flexi China', 'm2', 100, 20, 12000], [$ink->id, 'Flexi Korea', 'm2', 40, 10, 22000], [$paper->id, 'Art Carton 260gsm', 'rim', 15, 5, 350000], [$ink->id, 'Tinta CMYK', 'liter', 12, 3, 180000], [$paper->id, 'Kertas HVS', 'rim', 20, 5, 55000]] as $material) {
            Material::create(['material_category_id' => $material[0], 'supplier_id' => $supplier->id, 'name' => $material[1], 'unit' => $material[2], 'current_stock' => $material[3], 'minimum_stock' => $material[4], 'purchase_price' => $material[5]]);
        }

        foreach ([
            ['Menunggu Pembayaran', 'pending_payment', 1],
            ['Pembayaran Dikonfirmasi', 'payment_confirmed', 2],
            ['File Diterima', 'file_received', 3],
            ['Proses Desain', 'design_process', 4],
            ['Approval Customer', 'waiting_approval', 5],
            ['Proses Cetak', 'printing', 6],
            ['Proses Sablon', 'sablon_process', 7],
            ['Finishing', 'finishing', 8],
            ['Siap Diambil', 'ready_pickup', 9],
            ['Dikirim', 'shipped', 10],
            ['Selesai', 'completed', 11],
        ] as $step) {
            ProductionStep::create(['name' => $step[0], 'status_key' => $step[1], 'sort_order' => $step[2], 'is_active' => true]);
        }

        foreach ([
            'company_name' => 'GreenPrinting',
            'company_profile' => 'GreenPrinting adalah sistem manajemen percetakan online untuk produk digital printing, offset, large format, souvenir, packaging, dan sablon.',
            'company_phone' => '081249997084',
            'company_email' => 'winbhu@live.com',
            'company_website' => 'green-apps.my.id',
            'company_address' => 'Ponorogo',
            'receipt_printer_name' => 'POS-58 Thermal',
            'receipt_printer_paper' => '58mm',
            'production_printer_name' => 'Large Format Printer',
            'production_printer_paper' => 'Roll / A3 / A2',
            'theme_name' => 'professional',
            'header_text' => 'GreenPrinting - sistem manajemen percetakan profesional',
            'footer_text' => 'Aplikasi dibuat oleh Greentech.dev | 081249997084 | green-apps.my.id',
            'waha_enabled' => '0',
            'waha_base_url' => 'http://localhost:3000',
            'waha_api_key' => '',
            'waha_session' => 'default',
            'waha_admin_number' => '6281249997084',
            'waha_notify_admin_order' => '1',
            'waha_notify_customer_order' => '1',
            'waha_notify_payment' => '1',
            'waha_verify_ssl' => '0',
            'waha_template_order' => 'Halo {name}, order {order_number} sudah dibuat dengan total {total}.',
            'waha_template_payment' => 'Pembayaran order {order_number} berstatus {payment_status}.',
        ] as $key => $value) {
            Setting::put($key, $value);
        }
        Setting::put('role_permissions', [
            'admin' => ['products', 'orders', 'payments', 'stocks', 'reports', 'settings'],
            'staff' => ['orders', 'payments', 'stocks'],
            'customer' => [],
        ], 'permissions', 'json');

        \App\Models\Notification::create([
            'user_id' => 1,
            'title' => 'Selamat datang',
            'message' => 'Setting perusahaan, WAHA, printer, tema, dan alur produksi siap dikonfigurasi.',
            'type' => 'system',
        ]);
    }
}
