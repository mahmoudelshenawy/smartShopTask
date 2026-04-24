<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'id' => 1,
                'name' => 'Mechanical Keyboards',
            ],
            [
                'id' => 2,
                'name' => 'Gaming Mice',
            ],
            [
                'id' => 3,
                'name' => 'Monitors',
            ],
            [
                'id' => 4,
                'name' => 'Headphones',
            ],
            [
                'id' => 5,
                'name' => 'Desk Accessories & Ergonomics',
            ],
        ];
        $products = [
            // Category 1: Mechanical Keyboards
            [

                'id' => 1,
                'category_id' => 1,
                'name' => 'Keychron K2 Pro',
                'description' => 'Compact 75% wireless mechanical keyboard with hot-swappable switches, RGB backlight, and Mac/Windows compatibility.',
                'price' => 99.99,
            ],
            [
                'id' => 2,
                'category_id' => 1,
                'name' => 'Logitech MX Mechanical Mini',
                'description' => 'Minimalist wireless mechanical keyboard with tactile quiet switches, smart illumination, and multi-device pairing.',
                'price' => 119.99,
            ],
            [
                'id' => 3,
                'category_id' => 1,
                'name' => 'Ducky One 3 TKL',
                'description' => 'Tenkeyless mechanical keyboard with PBT double-shot keycaps, RGB per-key lighting, and Cherry MX switches.',
                'price' => 109.99,
            ],
            [
                'id' => 4,
                'category_id' => 1,
                'name' => 'HHKB Professional Hybrid',
                'description' => 'Premium 60% keyboard with Topre electrostatic capacitive switches, Bluetooth 4.2, and ultra-quiet typing experience.',
                'price' => 249.99,
            ],
            [
                'id' => 5,
                'category_id' => 1,
                'name' => 'Glorious GMMK Pro',
                'description' => 'Full aluminum 75% gasket-mounted mechanical keyboard with a rotary knob, hot-swap PCB, and south-facing RGB.',
                'price' => 149.99,
            ],
            [
                'id' => 6,
                'category_id' => 1,
                'name' => 'Anne Pro 2',
                'description' => 'Compact 60% Bluetooth mechanical keyboard with Gateron switches, full RGB, and tap-layer for arrow keys.',
                'price' => 69.99,
            ],

            // Category 2: Gaming Mice
            [
                'id' => 7,
                'category_id' => 2,
                'name' => 'Logitech G Pro X Superlight 2',
                'description' => 'Ultra-lightweight wireless gaming mouse at 60g, with HERO 2 sensor, 95-hour battery, and zero-additive PTFE feet.',
                'price' => 159.99,
            ],
            [
                'id' => 8,
                'category_id' => 2,
                'name' => 'Razer DeathAdder V3 HyperSpeed',
                'description' => 'Ergonomic wireless gaming mouse with Focus X optical sensor, 90-hour battery, and iconic right-handed shape.',
                'price' => 79.99,
            ],
            [
                'id' => 9,
                'category_id' => 2,
                'name' => 'Zowie EC2-C',
                'description' => 'Plug-and-play ergonomic gaming mouse with 3200 DPI sensor, paracord cable, and no software required design.',
                'price' => 69.99,
            ],
            [
                'id' => 10,
                'category_id' => 2,
                'name' => 'SteelSeries Aerox 5 Wireless',
                'description' => 'Lightweight holey-shell gaming mouse with 9 programmable buttons, AquaBarrier protection, and dual wireless connectivity.',
                'price' => 139.99,
            ],
            [
                'id' => 11,
                'category_id' => 2,
                'name' => 'Glorious Model O Wireless',
                'description' => 'Honeycomb shell gaming mouse at 69g, featuring BAMF sensor, 71-hour battery, and ambidextrous form factor.',
                'price' => 79.99,
            ],
            [
                'id' => 12,
                'category_id' => 2,
                'name' => 'Endgame Gear XM1r',
                'description' => 'Wired gaming mouse with PixArt 3370 sensor, ultra-flexible cable, and low-profile symmetrical shell for claw grip.',
                'price' => 54.99,
            ],

            // Category 3: Monitors
            [
                'id' => 13,
                'category_id' => 3,
                'name' => 'LG 27GP850-B',
                'description' => '27-inch QHD gaming monitor with 165Hz refresh rate, 1ms GtG, Nano IPS panel, and NVIDIA G-Sync compatibility.',
                'price' => 299.99,
            ],
            [
                'id' => 14,
                'category_id' => 3,
                'name' => 'Samsung Odyssey G7 32"',
                'description' => '32-inch curved QLED gaming monitor with 240Hz, 1ms, VESA DisplayHDR 600, and 1000R curvature for immersion.',
                'price' => 499.99,
            ],
            [
                'id' => 15,
                'category_id' => 3,
                'name' => 'Dell Ultrasharp U2723D',
                'description' => '27-inch 4K IPS Black monitor designed for professionals, with 98% DCI-P3 coverage, USB-C 90W, and zero-blur clarity.',
                'price' => 569.99,
            ],
            [
                'id' => 16,
                'category_id' => 3,
                'name' => 'ASUS ROG Swift PG279QM',
                'description' => '27-inch QHD Fast IPS gaming monitor with 240Hz, G-Sync Ultimate, and overclockable refresh rate for esports.',
                'price' => 699.99,
            ],
            [
                'id' => 17,
                'category_id' => 3,
                'name' => 'BenQ MOBIUZ EX2710Q',
                'description' => '27-inch QHD IPS gaming monitor with 165Hz, HDRi technology, built-in 2.1 speakers, and Eye-Care Plus suite.',
                'price' => 379.99,
            ],
            [
                'id' => 18,
                'category_id' => 3,
                'name' => 'Gigabyte M27Q',
                'description' => '27-inch QHD IPS gaming monitor with 170Hz, KVM switch built-in, USB-C connectivity, and AMD FreeSync Premium.',
                'price' => 249.99,
            ],

            // Category 4: Headphones
            [
                'id' => 19,
                'category_id' => 4,
                'name' => 'Sony WH-1000XM5',
                'description' => 'Over-ear noise-canceling headphones with industry-leading ANC, 30-hour battery, multipoint Bluetooth, and crystal-clear call quality.',
                'price' => 349.99,
            ],
            [
                'id' => 20,
                'category_id' => 4,
                'name' => 'Apple AirPods Max',
                'description' => 'Premium over-ear headphones with Apple H1 chip, adaptive EQ, spatial audio, and stainless steel frame build.',
                'price' => 549.99,
            ],
            [
                'id' => 21,
                'category_id' => 4,
                'name' => 'Bose QuietComfort 45',
                'description' => 'Wireless noise-canceling headphones with Aware mode, 24-hour battery, balanced audio performance, and lightweight design.',
                'price' => 279.99,
            ],
            [
                'id' => 22,
                'category_id' => 4,
                'name' => 'Sennheiser HD 560S',
                'description' => 'Open-back audiophile headphones with neutral sound signature, 120-ohm impedance, wide soundstage, and velour ear pads.',
                'price' => 149.99,
            ],
            [
                'id' => 23,
                'category_id' => 4,
                'name' => 'Beyerdynamic DT 770 Pro',
                'description' => 'Closed-back studio monitoring headphones with 80-ohm impedance, excellent sound isolation, and robust build for long sessions.',
                'price' => 149.99,
            ],
            [
                'id' => 24,
                'category_id' => 4,
                'name' => 'HyperX Cloud Alpha Wireless',
                'description' => 'Gaming headset with 300-hour battery life, DTS Headphone:X spatial audio, dual chamber drivers, and Discord-certified mic.',
                'price' => 199.99,
            ],

            // Category 5: Desk Accessories & Ergonomics
            [
                'id' => 25,
                'category_id' => 5,
                'name' => 'Flexispot E7 Pro Standing Desk',
                'description' => 'Electric height-adjustable desk with dual motor, 355 lb capacity, anti-collision detection, and programmable memory presets.',
                'price' => 499.99,
            ],
            [
                'id' => 26,
                'category_id' => 5,
                'name' => 'Herman Miller Embody Chair',
                'description' => 'Ergonomic office chair with BackFit adjustment, Pixelated Support matrix, and PostureFit SL spinal support for all-day comfort.',
                'price' => 1795.00,
            ],
            [
                'id' => 27,
                'category_id' => 5,
                'name' => 'Elgato Stream Deck MK.2',
                'description' => 'Customizable 15-key LCD controller for macros, app switching, and streaming workflows with swappable faceplate.',
                'price' => 149.99,
            ],
            [
                'id' => 28,
                'category_id' => 5,
                'name' => 'Ergotron LX Desk Mount',
                'description' => 'Single monitor arm with 360° rotation, full-motion articulation, cable management channel, and supports up to 34-inch displays.',
                'price' => 119.99,
            ],
            [
                'id' => 29,
                'category_id' => 5,
                'name' => 'Razer Firefly V2 Pro',
                'description' => 'Fully illuminated RGB hard mousepad with 15 lighting zones, non-slip base, USB-A passthrough, and micro-textured surface.',
                'price' => 99.99,
            ],
            [
                'id' => 30,
                'category_id' => 5,
                'name' => 'Logitech Litra Glow',
                'description' => 'Premium LED streaming light with TrueSoft technology, adjustable brightness and color temperature, and compact clip-on design.',
                'price' => 59.99,
            ],
        ];

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('categories')->truncate();
        DB::table('products')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        //
        DB::table('categories')->insert($categories);
        DB::table('products')->insert($products);
    }
}
