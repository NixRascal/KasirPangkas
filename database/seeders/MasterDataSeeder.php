<?php

namespace Database\Seeders;

use App\Models\Chair;
use App\Models\CommissionRule;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\Setting;
use App\Models\Shift;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        cache()->flush();

        $this->seedSettings();
        $this->seedServiceCatalog();
        $this->seedEmployees();
        $this->seedCustomers();
        $this->seedShifts();
        $this->seedChairs();
        $this->seedCommissionRules();
        $this->seedUsers();
    }

    private function seedSettings(): void
    {
        Setting::updateOrCreate(
            ['key' => 'override_price_limit_percent'],
            ['value' => '15', 'type' => 'numeric']
        );

        Setting::updateOrCreate(
            ['key' => 'require_admin_approval_above_limit'],
            ['value' => '1', 'type' => 'boolean']
        );
    }

    private function seedServiceCatalog(): void
    {
        $categories = [
            'Classic Grooming' => [
                ['name' => 'Classic Cut', 'code' => 'HC-CLASSIC', 'price' => 60000, 'duration' => 30],
                ['name' => 'Premium Cut', 'code' => 'HC-PREMIUM', 'price' => 90000, 'duration' => 45],
            ],
            'Color & Treatment' => [
                ['name' => 'Color Touch-Up', 'code' => 'CLR-TOUCH', 'price' => 180000, 'duration' => 90],
                ['name' => 'Scalp Treatment', 'code' => 'TRT-SCALP', 'price' => 150000, 'duration' => 60],
            ],
            'Kids Zone' => [
                ['name' => 'Kids Cut', 'code' => 'KID-CUT', 'price' => 50000, 'duration' => 25],
            ],
        ];

        foreach ($categories as $categoryName => $services) {
            $category = ServiceCategory::firstOrCreate([
                'name' => $categoryName,
            ], [
                'description' => $categoryName . ' services',
                'order' => ServiceCategory::count() + 1,
            ]);

            foreach ($services as $serviceData) {
                Service::updateOrCreate(
                    ['code' => $serviceData['code']],
                    [
                        'service_category_id' => $category->id,
                        'name' => $serviceData['name'],
                        'base_price' => $serviceData['price'],
                        'est_duration_min' => $serviceData['duration'],
                        'commission_type' => 'percent',
                        'commission_value' => 10,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function seedEmployees(): void
    {
        $employees = [
            ['name' => 'Budi Santoso', 'code' => 'EMP-BUDI', 'level' => 'senior'],
            ['name' => 'Sari Wulandari', 'code' => 'EMP-SARI', 'level' => 'master'],
            ['name' => 'Andi Prasetyo', 'code' => 'EMP-ANDI', 'level' => 'junior'],
        ];

        foreach ($employees as $employee) {
            Employee::updateOrCreate(
                ['code' => $employee['code']],
                [
                    'name' => $employee['name'],
                    'level' => $employee['level'],
                    'phone' => fake()->phoneNumber(),
                    'is_active' => true,
                    'hire_date' => Carbon::now()->subMonths(rand(3, 24))->toDateString(),
                ]
            );
        }
    }

    private function seedCustomers(): void
    {
        $customers = [
            ['name' => 'Rudi Hartono', 'phone' => '0812000111', 'type' => 'reguler'],
            ['name' => 'Maya Lestari', 'phone' => '0812000222', 'type' => 'member'],
            ['name' => 'Dewi Anggraini', 'phone' => '0812000333', 'type' => 'vip'],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(
                ['phone' => $customer['phone']],
                [
                    'name' => $customer['name'],
                    'type' => $customer['type'],
                    'notes' => 'Seeded customer',
                ]
            );
        }
    }

    private function seedShifts(): void
    {
        $shifts = [
            ['name' => 'Pagi', 'start' => '08:00:00', 'end' => '16:00:00'],
            ['name' => 'Sore', 'start' => '16:00:00', 'end' => '22:00:00'],
        ];

        foreach ($shifts as $shift) {
            Shift::updateOrCreate(
                ['name' => $shift['name']],
                [
                    'start_time' => $shift['start'],
                    'end_time' => $shift['end'],
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedChairs(): void
    {
        foreach (range(1, 4) as $index) {
            Chair::updateOrCreate(
                ['name' => 'Chair ' . $index],
                [
                    'location' => 'Zona ' . ceil($index / 2),
                    'is_active' => true,
                ]
            );
        }
    }

    private function seedCommissionRules(): void
    {
        $classicCut = Service::where('code', 'HC-CLASSIC')->first();

        if ($classicCut) {
            CommissionRule::updateOrCreate(
                ['name' => 'Classic Cut Bonus'],
                [
                    'scope' => 'per_service',
                    'service_id' => $classicCut->id,
                    'type' => 'percent',
                    'value' => 20,
                    'start_date' => now()->subMonth()->toDateString(),
                    'end_date' => null,
                    'is_active' => true,
                ]
            );
        }

        CommissionRule::updateOrCreate(
            ['name' => 'Master Stylist Premium'],
            [
                'scope' => 'per_employee_level',
                'employee_level' => 'master',
                'type' => 'percent',
                'value' => 25,
                'start_date' => now()->subMonths(2)->toDateString(),
                'end_date' => null,
                'is_active' => true,
            ]
        );

        CommissionRule::updateOrCreate(
            ['name' => 'Default Komisi'],
            [
                'scope' => 'global',
                'type' => 'percent',
                'value' => 10,
                'start_date' => now()->subMonths(6)->toDateString(),
                'end_date' => null,
                'is_active' => true,
            ]
        );
    }

    private function seedUsers(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@kasirpangkas.test'],
            [
                'name' => 'Admin Utama',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'pin' => '9999',
            ]
        );

        User::updateOrCreate(
            ['email' => 'kasir@kasirpangkas.test'],
            [
                'name' => 'Kasir Utama',
                'password' => bcrypt('password'),
                'role' => 'kasir',
                'pin' => '1234',
            ]
        );

        User::updateOrCreate(
            ['email' => 'stakeholder@kasirpangkas.test'],
            [
                'name' => 'Stakeholder',
                'password' => bcrypt('password'),
                'role' => 'stakeholder',
                'pin' => null,
            ]
        );
    }
}
