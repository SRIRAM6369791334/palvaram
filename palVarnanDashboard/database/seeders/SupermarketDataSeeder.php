<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Brand;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Product;
use App\Models\ProductVarient;
use App\Models\GiftSize;
use App\Models\GiftColor;
use App\Models\SupplierDetail;
use App\Models\Unit;

class SupermarketDataSeeder extends Seeder
{
    private function resolveImage(string $relativePath, string $defaultImage): string
    {
        return file_exists(public_path('images/' . $relativePath)) ? $relativePath : $defaultImage;
    }

    public function run()
    {
        $defaultImage = 'category_images/0xy7a4fGPMjpme725nLPmDVZ5Kk9mr2zgZBMUE3f.png';
        $countryIds = '243,244';
        $primaryCountryId = 243;

        // 1. Ensure Standard Units exist
        $unitsData = [
            'kg'  => ['unit_name' => 'Kilogram', 'short_name' => 'kg'],
            'g'   => ['unit_name' => 'Gram', 'short_name' => 'g'],
            'l'   => ['unit_name' => 'Litre', 'short_name' => 'l'],
            'ml'  => ['unit_name' => 'Millilitre', 'short_name' => 'ml'],
            'pcs' => ['unit_name' => 'Pieces', 'short_name' => 'pcs'],
            'pkt' => ['unit_name' => 'Packet', 'short_name' => 'pkt'],
        ];

        $unitMap = [];
        foreach ($unitsData as $key => $u) {
            $existing = Unit::where('short_name', $u['short_name'])->first();
            if (!$existing) {
                $existing = Unit::create([
                    'country_id' => $countryIds,
                    'unit_name'  => $u['unit_name'],
                    'short_name' => $u['short_name'],
                ]);
            }
            $unitMap[$key] = $existing->id;
        }

        // 2. Supermarket Brands
        $brandsData = [
            ['name' => 'Britannia', 'slug' => 'britannia'],
            ['name' => 'Parle', 'slug' => 'parle'],
            ['name' => 'ITC Sunfeast', 'slug' => 'itc-sunfeast'],
            ['name' => 'Cadbury', 'slug' => 'cadbury'],
            ['name' => 'Nestle', 'slug' => 'nestle'],
            ['name' => 'Aashirvaad', 'slug' => 'aashirvaad'],
            ['name' => 'Tata Consumer', 'slug' => 'tata-consumer'],
            ['name' => 'Amul', 'slug' => 'amul-dairy'],
            ['name' => 'Haldirams', 'slug' => 'haldirams'],
            ['name' => 'Dabur', 'slug' => 'dabur'],
            ['name' => 'Surf Excel', 'slug' => 'surf-excel'],
            ['name' => 'Colgate', 'slug' => 'colgate'],
        ];

        $brandMap = [];
        foreach ($brandsData as $b) {
            $brandImg = $this->resolveImage('brand_images/' . $b['slug'] . '.jpg', $defaultImage);
            $brand = Brand::where('brand_slug', $b['slug'])->first();
            if (!$brand) {
                $brand = Brand::create([
                    'country_id'  => $countryIds,
                    'brand_name'  => $b['name'],
                    'brand_slug'  => $b['slug'],
                    'brand_image' => $brandImg,
                    'status'      => 1,
                ]);
            } elseif ($brandImg !== $defaultImage && $brand->brand_image !== $brandImg) {
                $brand->update(['brand_image' => $brandImg]);
            }
            $brandMap[$b['slug']] = $brand;
        }

        // 3. Gift Sizes
        $giftSizesData = [
            'Small (Hamper)',
            'Medium (Hamper)',
            'Large (Deluxe)',
            'Executive Corporate Pack'
        ];
        $giftSizeMap = [];
        foreach ($giftSizesData as $s) {
            $sizeObj = GiftSize::where('size', $s)->first();
            if (!$sizeObj) {
                $sizeObj = GiftSize::create([
                    'country_id' => $countryIds,
                    'size'       => $s
                ]);
            }
            $giftSizeMap[$s] = $sizeObj->id;
        }

        // 4. Gift Colors
        $giftColorsData = [
            'Royal Gold',
            'Crimson Red',
            'Midnight Blue',
            'Emerald Green'
        ];
        $giftColorMap = [];
        foreach ($giftColorsData as $c) {
            $colorObj = GiftColor::where('color', $c)->first();
            if (!$colorObj) {
                $colorObj = GiftColor::create([
                    'country_id' => $countryIds,
                    'color'      => $c
                ]);
            }
            $giftColorMap[$c] = $colorObj->id;
        }

        // 5. Gift & Regular Suppliers
        $suppliersData = [
            ['name' => 'Royal Festive Hampers & Gifts Ltd', 'shop' => 'Royal Hampers Hub', 'location' => 'Mumbai', 'phone' => '9876543210', 'is_gift' => 1],
            ['name' => 'Apex Corporate Gifting Solutions', 'shop' => 'Apex Gifts Center', 'location' => 'Bengaluru', 'phone' => '9876543211', 'is_gift' => 1],
            ['name' => 'Metro Supermarket Wholesale Suppliers', 'shop' => 'Metro Wholesale Hub', 'location' => 'Chennai', 'phone' => '9876543212', 'is_gift' => 0],
        ];

        foreach ($suppliersData as $sup) {
            $existingSup = SupplierDetail::where('name', $sup['name'])->where('is_gift', $sup['is_gift'])->first();
            if (!$existingSup) {
                SupplierDetail::create([
                    'country_id' => $primaryCountryId,
                    'name'       => $sup['name'],
                    'shop'       => $sup['shop'],
                    'location'   => $sup['location'],
                    'phone'      => $sup['phone'],
                    'is_gift'    => $sup['is_gift']
                ]);
            }
        }

        // 6. Supermarket Categories, Subcategories, and 50+ Products
        $supermarketStructure = [
            [
                'category_name' => 'Biscuits & Cookies',
                'is_gift'       => 0,
                'subcategories' => [
                    'Glucose & Marie',
                    'Cream Biscuits',
                    'Digestive & Oats',
                    'Cookies',
                    'Crackers & Salted'
                ],
                'products' => [
                    [
                        'name'        => 'Britannia Marie Gold 250g',
                        'subcat'      => 'Glucose & Marie',
                        'brand_slug'  => 'britannia',
                        'desc'        => 'Crispy and light tea-time biscuits made with wholesome wheat.',
                        'variant'     => ['name' => 'Britannia Marie Gold (250g)', 'val' => '250', 'unit' => 'g', 'mrp' => 35, 'offer' => 30, 'qty' => 100]
                    ],
                    [
                        'name'        => 'Parle-G Gold Biscuits 1kg',
                        'subcat'      => 'Glucose & Marie',
                        'brand_slug'  => 'parle',
                        'desc'        => 'Rich in glucose and goodness of wheat, the nation\'s favorite biscuit.',
                        'variant'     => ['name' => 'Parle-G Gold (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 120, 'offer' => 105, 'qty' => 100]
                    ],
                    [
                        'name'        => 'Sunfeast Dark Fantasy Choco Fills 300g',
                        'subcat'      => 'Cream Biscuits',
                        'brand_slug'  => 'itc-sunfeast',
                        'desc'        => 'Crispy golden crust wrapped around a molten choco center.',
                        'variant'     => ['name' => 'Dark Fantasy Choco Fills (300g)', 'val' => '300', 'unit' => 'g', 'mrp' => 150, 'offer' => 130, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Oreo Original Vanilla Cream 300g',
                        'subcat'      => 'Cream Biscuits',
                        'brand_slug'  => 'cadbury',
                        'desc'        => 'Rich, smooth vanilla creme sandwiched between two crunchy chocolate cookies.',
                        'variant'     => ['name' => 'Oreo Vanilla Cream (300g)', 'val' => '300', 'unit' => 'g', 'mrp' => 90, 'offer' => 80, 'qty' => 90]
                    ],
                    [
                        'name'        => 'Britannia NutriChoice Digestive 1kg',
                        'subcat'      => 'Digestive & Oats',
                        'brand_slug'  => 'britannia',
                        'desc'        => 'High dietary fibre biscuits for healthy and balanced snacking.',
                        'variant'     => ['name' => 'NutriChoice Digestive (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 190, 'offer' => 165, 'qty' => 60]
                    ],
                    [
                        'name'        => 'Sunfeast Mom\'s Magic Cashew & Almond 600g',
                        'subcat'      => 'Cookies',
                        'brand_slug'  => 'itc-sunfeast',
                        'desc'        => 'Rich butter cookies loaded with crunchy cashews and roasted almonds.',
                        'variant'     => ['name' => 'Mom\'s Magic Cashew Almond (600g)', 'val' => '600', 'unit' => 'g', 'mrp' => 140, 'offer' => 120, 'qty' => 75]
                    ],
                    [
                        'name'        => 'Parle Monaco Salted Crackers 400g',
                        'subcat'      => 'Crackers & Salted',
                        'brand_slug'  => 'parle',
                        'desc'        => 'Light, crispy salted crackers perfect for savory toppings and dips.',
                        'variant'     => ['name' => 'Monaco Salted Crackers (400g)', 'val' => '400', 'unit' => 'g', 'mrp' => 80, 'offer' => 70, 'qty' => 85]
                    ]
                ]
            ],
            [
                'category_name' => 'Dairy & Breakfast',
                'is_gift'       => 0,
                'subcategories' => [
                    'Fresh Milk',
                    'Butter & Cheese',
                    'Curd & Yogurt',
                    'Ghee & Cream'
                ],
                'products' => [
                    [
                        'name'        => 'Amul Taaza Homogenised Toned Milk 1L',
                        'subcat'      => 'Fresh Milk',
                        'brand_slug'  => 'amul-dairy',
                        'desc'        => 'Pure toned milk with long shelf life, rich in calcium and protein.',
                        'variant'     => ['name' => 'Amul Taaza Milk (1L)', 'val' => '1', 'unit' => 'l', 'mrp' => 72, 'offer' => 68, 'qty' => 120]
                    ],
                    [
                        'name'        => 'Nestle Everyday Dairy Whitener 1kg',
                        'subcat'      => 'Fresh Milk',
                        'brand_slug'  => 'nestle',
                        'desc'        => 'Special milk powder blend designed to make thick, creamy tea and coffee.',
                        'variant'     => ['name' => 'Nestle Everyday Dairy Whitener (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 590, 'offer' => 540, 'qty' => 50]
                    ],
                    [
                        'name'        => 'Amul Salted Butter 500g',
                        'subcat'      => 'Butter & Cheese',
                        'brand_slug'  => 'amul-dairy',
                        'desc'        => 'Classic pasteurized salted butter made from fresh dairy milk.',
                        'variant'     => ['name' => 'Amul Butter (500g)', 'val' => '500', 'unit' => 'g', 'mrp' => 275, 'offer' => 260, 'qty' => 70]
                    ],
                    [
                        'name'        => 'Amul Processed Cheese Block 400g',
                        'subcat'      => 'Butter & Cheese',
                        'brand_slug'  => 'amul-dairy',
                        'desc'        => 'Rich and creamy processed cheese block ideal for grating on pizzas and sandwiches.',
                        'variant'     => ['name' => 'Amul Cheese Block (400g)', 'val' => '400', 'unit' => 'g', 'mrp' => 240, 'offer' => 225, 'qty' => 60]
                    ],
                    [
                        'name'        => 'Amul Pure Cow Ghee Tin 1L',
                        'subcat'      => 'Ghee & Cream',
                        'brand_slug'  => 'amul-dairy',
                        'desc'        => 'Traditional aromatic cow ghee for delicious sweets and everyday cooking.',
                        'variant'     => ['name' => 'Amul Cow Ghee (1L)', 'val' => '1', 'unit' => 'l', 'mrp' => 650, 'offer' => 610, 'qty' => 45]
                    ],
                    [
                        'name'        => 'Nestle A+ Nourish Dahi 400g',
                        'subcat'      => 'Curd & Yogurt',
                        'brand_slug'  => 'nestle',
                        'desc'        => 'Thick, creamy, and natural curd with active live cultures.',
                        'variant'     => ['name' => 'Nestle A+ Nourish Dahi (400g)', 'val' => '400', 'unit' => 'g', 'mrp' => 50, 'offer' => 45, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Amul Malai Paneer 200g',
                        'subcat'      => 'Butter & Cheese',
                        'brand_slug'  => 'amul-dairy',
                        'desc'        => 'Soft and spongy fresh cottage cheese cubes for restaurant-style curries.',
                        'variant'     => ['name' => 'Amul Malai Paneer (200g)', 'val' => '200', 'unit' => 'g', 'mrp' => 95, 'offer' => 88, 'qty' => 90]
                    ]
                ]
            ],
            [
                'category_name' => 'Beverages & Drinks',
                'is_gift'       => 0,
                'subcategories' => [
                    'Tea',
                    'Coffee',
                    'Fruit Juices',
                    'Health Drinks'
                ],
                'products' => [
                    [
                        'name'        => 'Tata Tea Gold Leaf 1kg',
                        'subcat'      => 'Tea',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Exquisite blend of CTC tea with 15% gently rolled long tea leaves for rich aroma.',
                        'variant'     => ['name' => 'Tata Tea Gold (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 520, 'offer' => 470, 'qty' => 65]
                    ],
                    [
                        'name'        => 'Tata Tea Premium 500g',
                        'subcat'      => 'Tea',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'India\'s pride tea with the perfect balance of strength and flavor.',
                        'variant'     => ['name' => 'Tata Tea Premium (500g)', 'val' => '500', 'unit' => 'g', 'mrp' => 260, 'offer' => 235, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Nescafe Classic Instant Coffee Jar 200g',
                        'subcat'      => 'Coffee',
                        'brand_slug'  => 'nestle',
                        'desc'        => '100% pure roasted coffee beans delivering an intense bold taste.',
                        'variant'     => ['name' => 'Nescafe Classic Jar (200g)', 'val' => '200', 'unit' => 'g', 'mrp' => 650, 'offer' => 580, 'qty' => 55]
                    ],
                    [
                        'name'        => 'Bru Instant Coffee Powder 200g',
                        'subcat'      => 'Coffee',
                        'brand_slug'  => 'nestle',
                        'desc'        => 'Fine blend of plantation coffee beans and chicory for a delightful cup.',
                        'variant'     => ['name' => 'Bru Instant Coffee (200g)', 'val' => '200', 'unit' => 'g', 'mrp' => 380, 'offer' => 340, 'qty' => 70]
                    ],
                    [
                        'name'        => 'Real Mixed Fruit Juice 1L',
                        'subcat'      => 'Fruit Juices',
                        'brand_slug'  => 'dabur',
                        'desc'        => 'Naturally blended juices of 9 delicious fruits packed with vitamin C.',
                        'variant'     => ['name' => 'Real Mixed Fruit Juice (1L)', 'val' => '1', 'unit' => 'l', 'mrp' => 130, 'offer' => 110, 'qty' => 90]
                    ],
                    [
                        'name'        => 'Cadbury Bournvita Pro-Health 1kg',
                        'subcat'      => 'Health Drinks',
                        'brand_slug'  => 'cadbury',
                        'desc'        => 'Malted chocolate health drink enriched with inner strength formula vitamins.',
                        'variant'     => ['name' => 'Cadbury Bournvita (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 440, 'offer' => 395, 'qty' => 60]
                    ],
                    [
                        'name'        => 'Nestle Milo Chocolate Malt Drink 500g',
                        'subcat'      => 'Health Drinks',
                        'brand_slug'  => 'nestle',
                        'desc'        => 'Nutritious malted chocolate drink giving active energy for sports and study.',
                        'variant'     => ['name' => 'Nestle Milo Drink (500g)', 'val' => '500', 'unit' => 'g', 'mrp' => 280, 'offer' => 250, 'qty' => 50]
                    ]
                ]
            ],
            [
                'category_name' => 'Snacks & Namkeen',
                'is_gift'       => 0,
                'subcategories' => [
                    'Traditional Mixture',
                    'Bhujia & Sev',
                    'Chips & Crisps'
                ],
                'products' => [
                    [
                        'name'        => 'Haldirams Nagpur Aloo Bhujia 1kg',
                        'subcat'      => 'Bhujia & Sev',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Spicy, tangy potato and gram flour crisp noodles flavored with mint and spices.',
                        'variant'     => ['name' => 'Haldirams Aloo Bhujia (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 260, 'offer' => 230, 'qty' => 100]
                    ],
                    [
                        'name'        => 'Haldirams All in One Mixture 1kg',
                        'subcat'      => 'Traditional Mixture',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Generous mix of crispy namkeen, cornflakes, dry fruits, and puffed rice.',
                        'variant'     => ['name' => 'Haldirams All in One (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 280, 'offer' => 245, 'qty' => 85]
                    ],
                    [
                        'name'        => 'Haldirams Classic Salted Peanuts 400g',
                        'subcat'      => 'Traditional Mixture',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Crispy golden fried peanuts sprinkled with rock salt.',
                        'variant'     => ['name' => 'Haldirams Salted Peanuts (400g)', 'val' => '400', 'unit' => 'g', 'mrp' => 140, 'offer' => 120, 'qty' => 70]
                    ],
                    [
                        'name'        => 'Haldirams Fried Moong Dal 400g',
                        'subcat'      => 'Traditional Mixture',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Crunchy split yellow lentils seasoned lightly with salt.',
                        'variant'     => ['name' => 'Haldirams Moong Dal (400g)', 'val' => '400', 'unit' => 'g', 'mrp' => 150, 'offer' => 130, 'qty' => 75]
                    ],
                    [
                        'name'        => 'Haldirams Classic Bhujia Sev 400g',
                        'subcat'      => 'Bhujia & Sev',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Crispy gram flour sev lightly salted and spiced.',
                        'variant'     => ['name' => 'Haldirams Bhujia Sev (400g)', 'val' => '400', 'unit' => 'g', 'mrp' => 130, 'offer' => 115, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Haldirams Khatta Meetha Mixture 1kg',
                        'subcat'      => 'Traditional Mixture',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Delicious sweet and sour blend of sev, fried green peas, and boondi.',
                        'variant'     => ['name' => 'Haldirams Khatta Meetha (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 250, 'offer' => 220, 'qty' => 90]
                    ]
                ]
            ],
            [
                'category_name' => 'Rice, Atta & Grains',
                'is_gift'       => 0,
                'subcategories' => [
                    'Wheat Atta',
                    'Basmati Rice',
                    'Rava & Sooji',
                    'Flours & Grains'
                ],
                'products' => [
                    [
                        'name'        => 'Aashirvaad Shudh Chakki Atta 10kg',
                        'subcat'      => 'Wheat Atta',
                        'brand_slug'  => 'aashirvaad',
                        'desc'        => '100% pure whole wheat flour processed with 4-step advantage for fluffy rotis.',
                        'variant'     => ['name' => 'Aashirvaad Chakki Atta (10kg)', 'val' => '10', 'unit' => 'kg', 'mrp' => 480, 'offer' => 430, 'qty' => 50]
                    ],
                    [
                        'name'        => 'Aashirvaad Select Sharbati Atta 5kg',
                        'subcat'      => 'Wheat Atta',
                        'brand_slug'  => 'aashirvaad',
                        'desc'        => 'Made from 100% Sharbati wheat grains harvested in Madhya Pradesh.',
                        'variant'     => ['name' => 'Aashirvaad Sharbati Atta (5kg)', 'val' => '5', 'unit' => 'kg', 'mrp' => 320, 'offer' => 285, 'qty' => 60]
                    ],
                    [
                        'name'        => 'Tata Sampann Unpolished Toor Dal 1kg',
                        'subcat'      => 'Flours & Grains',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Wholesome unpolished pigeon pea lentils rich in natural dietary fiber.',
                        'variant'     => ['name' => 'Tata Sampann Toor Dal (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 195, 'offer' => 175, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Tata Sampann Unpolished Moong Dal 1kg',
                        'subcat'      => 'Flours & Grains',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Nutritious yellow split moong dal cleaned without water, oil, or polish.',
                        'variant'     => ['name' => 'Tata Sampann Moong Dal (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 180, 'offer' => 160, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Aashirvaad Superior MP Atta 1kg',
                        'subcat'      => 'Wheat Atta',
                        'brand_slug'  => 'aashirvaad',
                        'desc'        => 'Convenient 1kg pouch of chakki ground whole wheat flour.',
                        'variant'     => ['name' => 'Aashirvaad MP Atta (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 55, 'offer' => 48, 'qty' => 100]
                    ],
                    [
                        'name'        => 'Tata Sampann Roasted Sooji / Rava 500g',
                        'subcat'      => 'Rava & Sooji',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Pre-roasted semolina rava made from select wheat grains for instant upma.',
                        'variant'     => ['name' => 'Tata Sampann Roasted Sooji (500g)', 'val' => '500', 'unit' => 'g', 'mrp' => 45, 'offer' => 38, 'qty' => 90]
                    ]
                ]
            ],
            [
                'category_name' => 'Masalas, Spices & Pastes',
                'is_gift'       => 0,
                'subcategories' => [
                    'Pure Spices',
                    'Blended Masalas',
                    'Cooking Pastes'
                ],
                'products' => [
                    [
                        'name'        => 'Tata Sampann Turmeric Powder 500g',
                        'subcat'      => 'Pure Spices',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Haldi powder containing at least 3% natural curcumin with intact essential oils.',
                        'variant'     => ['name' => 'Tata Sampann Turmeric (500g)', 'val' => '500', 'unit' => 'g', 'mrp' => 160, 'offer' => 140, 'qty' => 70]
                    ],
                    [
                        'name'        => 'Tata Sampann Red Chilli Powder 500g',
                        'subcat'      => 'Pure Spices',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Ground from sun-dried Indian red chillies for vibrant color and balanced heat.',
                        'variant'     => ['name' => 'Tata Sampann Chilli Powder (500g)', 'val' => '500', 'unit' => 'g', 'mrp' => 220, 'offer' => 195, 'qty' => 70]
                    ],
                    [
                        'name'        => 'Tata Sampann Coriander Powder 500g',
                        'subcat'      => 'Pure Spices',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Fragrant dhaniya powder ground from fresh whole coriander seeds.',
                        'variant'     => ['name' => 'Tata Sampann Dhaniya (500g)', 'val' => '500', 'unit' => 'g', 'mrp' => 150, 'offer' => 130, 'qty' => 70]
                    ],
                    [
                        'name'        => 'Tata Sampann Garam Masala 100g',
                        'subcat'      => 'Blended Masalas',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Rich aromatic master blend of whole spices recipe crafted by master chefs.',
                        'variant'     => ['name' => 'Tata Sampann Garam Masala (100g)', 'val' => '100', 'unit' => 'g', 'mrp' => 95, 'offer' => 82, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Dabur Hommade Ginger Garlic Paste 200g',
                        'subcat'      => 'Cooking Pastes',
                        'brand_slug'  => 'dabur',
                        'desc'        => 'Freshly ground paste with authentic culinary flavor and zero added water.',
                        'variant'     => ['name' => 'Dabur Ginger Garlic Paste (200g)', 'val' => '200', 'unit' => 'g', 'mrp' => 55, 'offer' => 48, 'qty' => 100]
                    ],
                    [
                        'name'        => 'Tata Salt Vacuum Evaporated Iodised 1kg',
                        'subcat'      => 'Pure Spices',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Desh ka Namak, vacuum evaporated pure white iodised cooking salt.',
                        'variant'     => ['name' => 'Tata Salt (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 28, 'offer' => 25, 'qty' => 150]
                    ]
                ]
            ],
            [
                'category_name' => 'Chocolates & Confectionery',
                'is_gift'       => 0,
                'subcategories' => [
                    'Milk Chocolates',
                    'Dark Chocolates',
                    'Chocolate Bars'
                ],
                'products' => [
                    [
                        'name'        => 'Cadbury Dairy Milk Silk Chocolate 150g',
                        'subcat'      => 'Milk Chocolates',
                        'brand_slug'  => 'cadbury',
                        'desc'        => 'Smooth, velvety milk chocolate that melts effortlessly in your mouth.',
                        'variant'     => ['name' => 'Dairy Milk Silk (150g)', 'val' => '150', 'unit' => 'g', 'mrp' => 185, 'offer' => 165, 'qty' => 90]
                    ],
                    [
                        'name'        => 'Cadbury Dairy Milk Fruit & Nut 150g',
                        'subcat'      => 'Milk Chocolates',
                        'brand_slug'  => 'cadbury',
                        'desc'        => 'Creamy milk chocolate studded with crunchy roasted almonds and juicy raisins.',
                        'variant'     => ['name' => 'Dairy Milk Fruit & Nut (150g)', 'val' => '150', 'unit' => 'g', 'mrp' => 195, 'offer' => 175, 'qty' => 85]
                    ],
                    [
                        'name'        => 'Cadbury Dairy Milk Roast Almond 150g',
                        'subcat'      => 'Milk Chocolates',
                        'brand_slug'  => 'cadbury',
                        'desc'        => 'Loaded with whole Californian almonds roasted to golden perfection.',
                        'variant'     => ['name' => 'Dairy Milk Roast Almond (150g)', 'val' => '150', 'unit' => 'g', 'mrp' => 195, 'offer' => 175, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Nestle KitKat 4-Finger Pack of 8',
                        'subcat'      => 'Chocolate Bars',
                        'brand_slug'  => 'nestle',
                        'desc'        => 'Crispy oven-baked wafer fingers enveloped in smooth milk chocolate.',
                        'variant'     => ['name' => 'KitKat 4-Finger (Pack of 8)', 'val' => '8', 'unit' => 'pcs', 'mrp' => 160, 'offer' => 140, 'qty' => 70]
                    ],
                    [
                        'name'        => 'Cadbury Celebrations Rich Dry Fruit Box 177g',
                        'subcat'      => 'Milk Chocolates',
                        'brand_slug'  => 'cadbury',
                        'desc'        => 'Festive dry fruit gift pack with almond, raisin, and cashew chocolates.',
                        'variant'     => ['name' => 'Celebrations Dry Fruit Box (177g)', 'val' => '177', 'unit' => 'g', 'mrp' => 250, 'offer' => 225, 'qty' => 60]
                    ],
                    [
                        'name'        => 'Nestle Munch Crunchy Chocolate Bar 200g',
                        'subcat'      => 'Chocolate Bars',
                        'brand_slug'  => 'nestle',
                        'desc'        => 'Crunchilicious wafer layers coated with scrumptious chocolate.',
                        'variant'     => ['name' => 'Nestle Munch Bar (200g)', 'val' => '200', 'unit' => 'g', 'mrp' => 100, 'offer' => 85, 'qty' => 90]
                    ]
                ]
            ],
            [
                'category_name' => 'Personal Care & Cleaning',
                'is_gift'       => 0,
                'subcategories' => [
                    'Oral Care',
                    'Detergents & Laundry',
                    'Soaps & Body Wash'
                ],
                'products' => [
                    [
                        'name'        => 'Colgate Total Advanced Health Toothpaste 150g',
                        'subcat'      => 'Oral Care',
                        'brand_slug'  => 'colgate',
                        'desc'        => 'Antibacterial toothpaste that fights germs on teeth, tongue, cheeks and gums for 12 hours.',
                        'variant'     => ['name' => 'Colgate Total Advanced (150g)', 'val' => '150', 'unit' => 'g', 'mrp' => 145, 'offer' => 125, 'qty' => 110]
                    ],
                    [
                        'name'        => 'Colgate MaxFresh Red Gel Toothpaste 300g',
                        'subcat'      => 'Oral Care',
                        'brand_slug'  => 'colgate',
                        'desc'        => 'Infused with cooling crystals for intense spicy freshness.',
                        'variant'     => ['name' => 'Colgate MaxFresh Red (300g)', 'val' => '300', 'unit' => 'g', 'mrp' => 220, 'offer' => 190, 'qty' => 95]
                    ],
                    [
                        'name'        => 'Surf Excel Easy Wash Detergent Powder 1kg',
                        'subcat'      => 'Detergents & Laundry',
                        'brand_slug'  => 'surf-excel',
                        'desc'        => 'Engineered with superfine powder that dissolves instantly to remove tough stains.',
                        'variant'     => ['name' => 'Surf Excel Easy Wash (1kg)', 'val' => '1', 'unit' => 'kg', 'mrp' => 155, 'offer' => 138, 'qty' => 100]
                    ],
                    [
                        'name'        => 'Surf Excel Matic Front Load Liquid 1L',
                        'subcat'      => 'Detergents & Laundry',
                        'brand_slug'  => 'surf-excel',
                        'desc'        => 'Liquid detergent formula customized for high efficiency front-loading washing machines.',
                        'variant'     => ['name' => 'Surf Excel Matic Liquid (1L)', 'val' => '1', 'unit' => 'l', 'mrp' => 240, 'offer' => 210, 'qty' => 60]
                    ],
                    [
                        'name'        => 'Dabur Red Ayurvedic Toothpaste 300g',
                        'subcat'      => 'Oral Care',
                        'brand_slug'  => 'dabur',
                        'desc'        => 'Contains clove oil, pudina satva, and tomar beej for strong teeth and gums.',
                        'variant'     => ['name' => 'Dabur Red Toothpaste (300g)', 'val' => '300', 'unit' => 'g', 'mrp' => 190, 'offer' => 165, 'qty' => 80]
                    ],
                    [
                        'name'        => 'Dabur Vatika Enriched Coconut Hair Oil 300ml',
                        'subcat'      => 'Soaps & Body Wash',
                        'brand_slug'  => 'dabur',
                        'desc'        => 'Coconut hair oil enriched with triphala, brahmi, and henna for root strengthening.',
                        'variant'     => ['name' => 'Dabur Vatika Hair Oil (300ml)', 'val' => '300', 'unit' => 'ml', 'mrp' => 180, 'offer' => 155, 'qty' => 75]
                    ]
                ]
            ],
            // 7. Gift Categories, Subcategories & Gift Products (is_gift = 1)
            [
                'category_name' => 'Festive Gift Hampers',
                'is_gift'       => 1,
                'subcategories' => [
                    'Diwali Special Hampers',
                    'Corporate Celebration Hampers'
                ],
                'products' => [
                    [
                        'name'        => 'Cadbury Celebrations Grand Gift Hamper',
                        'subcat'      => 'Diwali Special Hampers',
                        'brand_slug'  => 'cadbury',
                        'desc'        => 'Grand celebration festive box with premium chocolates and festive greeting card.',
                        'variant'     => ['name' => 'Cadbury Grand Hamper (Gold/Deluxe)', 'val' => '1', 'unit' => 'pkt', 'mrp' => 850, 'offer' => 750, 'qty' => 40, 'size' => 'Large (Deluxe)', 'color' => 'Royal Gold']
                    ],
                    [
                        'name'        => 'Royal Dry Fruit Celebration Gift Box',
                        'subcat'      => 'Corporate Celebration Hampers',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Handpicked California almonds, whole cashews, pistachios, and golden raisins in a velvet box.',
                        'variant'     => ['name' => 'Royal Dry Fruit Box (Gold/Medium)', 'val' => '1', 'unit' => 'pkt', 'mrp' => 1200, 'offer' => 1050, 'qty' => 30, 'size' => 'Medium (Hamper)', 'color' => 'Royal Gold']
                    ]
                ]
            ],
            [
                'category_name' => 'Gourmet Treats & Gifts',
                'is_gift'       => 1,
                'subcategories' => [
                    'Artisanal Cookie Boxes',
                    'Exotic Tea Gift Sets'
                ],
                'products' => [
                    [
                        'name'        => 'Artisanal Handcrafted Cookies & Chocolates Hamper',
                        'subcat'      => 'Artisanal Cookie Boxes',
                        'brand_slug'  => 'itc-sunfeast',
                        'desc'        => 'Assorted artisanal butter cookies paired with rich chocolate bonbons.',
                        'variant'     => ['name' => 'Artisanal Cookie Hamper (Red/Medium)', 'val' => '1', 'unit' => 'pkt', 'mrp' => 750, 'offer' => 680, 'qty' => 35, 'size' => 'Medium (Hamper)', 'color' => 'Crimson Red']
                    ],
                    [
                        'name'        => 'Tata Luxury Exotic Tea Gift Collection',
                        'subcat'      => 'Exotic Tea Gift Sets',
                        'brand_slug'  => 'tata-consumer',
                        'desc'        => 'Collection of Kashmiri Kahwa, Darjeeling First Flush, and Green Tea caddies.',
                        'variant'     => ['name' => 'Luxury Tea Collection (Blue/Corporate)', 'val' => '1', 'unit' => 'pkt', 'mrp' => 950, 'offer' => 850, 'qty' => 25, 'size' => 'Executive Corporate Pack', 'color' => 'Midnight Blue']
                    ],
                    [
                        'name'        => 'Festive Gourmet Sweet & Savoury Combo Box',
                        'subcat'      => 'Artisanal Cookie Boxes',
                        'brand_slug'  => 'haldirams',
                        'desc'        => 'Delightful combination of premium soan papdi, kaju katli, and traditional mixture.',
                        'variant'     => ['name' => 'Gourmet Combo Box (Green/Small)', 'val' => '1', 'unit' => 'pkt', 'mrp' => 600, 'offer' => 520, 'qty' => 50, 'size' => 'Small (Hamper)', 'color' => 'Emerald Green']
                    ]
                ]
            ]
        ];

        // Process creation of Categories, Subcategories, Products, Variants, and Stocks
        $totalCreatedProducts = 0;
        $totalCreatedVariants = 0;

        foreach ($supermarketStructure as $catData) {
            // Category
            $catSlug = Str::slug($catData['category_name']);
            $catImg = $this->resolveImage('category_images/' . $catSlug . '.jpg', $defaultImage);
            $category = Category::where('category_name', $catData['category_name'])
                ->where('is_gift', $catData['is_gift'])
                ->first();

            if (!$category) {
                $category = Category::create([
                    'country_id'      => $countryIds,
                    'category_name'   => $catData['category_name'],
                    'category_image'  => $catImg,
                    'category_banner' => null,
                    'category_icon'   => null,
                    'status'          => 0,
                    'is_gift'         => $catData['is_gift'],
                ]);
            } elseif ($catImg !== $defaultImage && $category->category_image !== $catImg) {
                $category->update(['category_image' => $catImg]);
            }

            // Subcategories
            $subcatMap = [];
            foreach ($catData['subcategories'] as $subcatName) {
                $subSlug = Str::slug($subcatName);
                $subImg = $this->resolveImage('subcategory_images/' . $subSlug . '.jpg', $defaultImage);
                $subcat = SubCategory::where('subcategory_name', $subcatName)
                    ->where('category_name', $category->id)
                    ->where('is_gift', $catData['is_gift'])
                    ->first();

                if (!$subcat) {
                    $subcat = SubCategory::create([
                        'country_id'        => $primaryCountryId,
                        'category_name'     => $category->id,
                        'subcategory_name'  => $subcatName,
                        'category_display'  => $category->category_name,
                        'subcategory_image' => $subImg,
                        'status'            => null,
                        'is_gift'           => $catData['is_gift'],
                    ]);
                } elseif ($subImg !== $defaultImage && $subcat->subcategory_image !== $subImg) {
                    $subcat->update(['subcategory_image' => $subImg]);
                }
                $subcatMap[$subcatName] = $subcat;
            }

            // Products
            foreach ($catData['products'] as $pData) {
                $slug = Str::slug($pData['name']);
                $prodImg = $this->resolveImage('product_images/' . $slug . '.jpg', $defaultImage);
                $brand = $brandMap[$pData['brand_slug']] ?? null;
                $subcat = $subcatMap[$pData['subcat']] ?? null;

                $product = Product::where('slug', $slug)->first();
                if (!$product) {
                    $product = Product::create([
                        'country_id'            => $countryIds,
                        'category_id'           => $category->id,
                        'brand_id'              => $brand ? $brand->id : null,
                        'subcategory_id'        => $subcat ? (string)$subcat->id : null,
                        'product_name'          => $pData['name'],
                        'slug'                  => $slug,
                        'product_quantity'      => $pData['variant']['qty'],
                        'product_mrp_price'     => $pData['variant']['mrp'],
                        'product_regular_price' => $pData['variant']['offer'],
                        'product_description'   => $pData['desc'],
                        'product_image'         => $prodImg,
                        'product_specification' => $pData['desc'],
                        'cate_name'             => $category->category_name,
                        'subcate_name'          => $subcat ? $subcat->subcategory_name : '',
                        'brand_name'            => $brand ? $brand->brand_name : '',
                        'is_gift'               => $catData['is_gift'],
                    ]);
                } elseif ($prodImg !== $defaultImage && $product->product_image !== $prodImg) {
                    $product->update(['product_image' => $prodImg]);
                }
                $totalCreatedProducts++;

                // Variant
                $varData = $pData['variant'];
                $sku = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $pData['name']), 0, 4)) . '-' . rand(1000, 9999);
                $barcode = 'BC' . rand(10000000, 99999999);

                $unitId = $unitMap[$varData['unit']] ?? null;
                $sizeId = isset($varData['size']) ? ($giftSizeMap[$varData['size']] ?? null) : null;
                $colorId = isset($varData['color']) ? ($giftColorMap[$varData['color']] ?? null) : null;

                $existingVariant = ProductVarient::where('product_id', $product->id)->first();
                if (!$existingVariant) {
                    $existingVariant = ProductVarient::create([
                        'categoryid'       => $category->id,
                        'subcategoryid'    => $subcat ? (string)$subcat->id : null,
                        'subcatename'      => $subcat ? $subcat->subcategory_name : '',
                        'product_id'       => $product->id,
                        'country_id'       => $countryIds,
                        'sku'              => $sku,
                        'barcode'          => $barcode,
                        'unit_id'          => $unitId,
                        'size_id'          => $sizeId,
                        'color_id'         => $colorId,
                        'varient'          => (int)$varData['val'],
                        'value'            => $varData['val'],
                        'varient_img'      => $prodImg,
                        'varient_name'     => $varData['name'],
                        'offer_price'      => $varData['offer'],
                        'mrp_price'        => $varData['mrp'],
                        'product_qty'      => $varData['qty'],
                        'low_stock'        => '5',
                        'hot_deals'        => 0,
                        'Popular_products' => 1,
                        'pre_order'        => 0,
                        'product_gst'      => 0,
                        'flash_sale'       => 0,
                    ]);

                    // Add Product Stock entry
                    DB::table('productstocks')->insert([
                        'productid'             => $product->id,
                        'category_id'           => $category->id,
                        'subcategory_id'        => $subcat ? (string)$subcat->id : null,
                        'country_id'            => $countryIds,
                        'warehouse_id'          => 1,
                        'branch_id'             => 1,
                        'pro_ver_id'            => $existingVariant->id,
                        'productname'           => $product->product_name,
                        'overallstock'          => $varData['qty'],
                        'availablestock'        => $varData['qty'],
                        'salestock'             => 0,
                        'low_stocks'            => '5',
                        'last_stockupdate_date' => date('Y-m-d H:i:s'),
                    ]);
                } elseif ($prodImg !== $defaultImage && $existingVariant->varient_img !== $prodImg) {
                    $existingVariant->update(['varient_img' => $prodImg]);
                }
                $totalCreatedVariants++;
            }
        }

        $this->command->info("Supermarket seeding completed!");
        $this->command->info("Products processed: {$totalCreatedProducts}");
        $this->command->info("Variants processed: {$totalCreatedVariants}");
    }
}
