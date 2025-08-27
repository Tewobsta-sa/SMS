<?php

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\OrganizationContact;
use App\Enums\StatusEnum;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
  
    public function run(): void
    {
        $organization = Organization::create([
            'title' => 'Veritas Afrika',
            'po_box' => '',
            'address' => 'Hia cinema, JubaSouth Sudan',
            'opening_hours' => [
                [
                    'days' => ['mon', 'tue', 'wed', 'thu', 'fri'],
                    'from' => '08:00:00',
                    'to' => '17:00:00',
                ],
                [
                    'days' => ['sat'],
                    'from' => '09:00:00',
                    'to' => '13:00:00',
                ],
            ],
            'map_url' => '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3940.796088016692!2d38.79160421150415!3d8.990902591031654!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x164b84e3dece8e99%3A0xf406ae3387722a80!2sDasset%20Plc!5e0!3m2!1sen!2set!4v1755353360581!5m2!1sen!2set" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>',
            'status' => 'active',
        ]);

        OrganizationContact::create([
            'type' => 'phone',
            'value' => '+211 923 2 41 605',
            'status' => StatusEnum::active,
        ]);

        OrganizationContact::create([
            'type' => 'phone',
            'value' => '+27 749 505 555',
            'status' => StatusEnum::active,
        ]);

        OrganizationContact::create([
            'type' => 'email',
            'value' => 'contact@veritasafrika.com',
            'status' => StatusEnum::active,
        ]);

        OrganizationContact::create([
            'type' => 'email',
            'value' => 'info@veritasafrika.com',
            'status' => StatusEnum::active,
        ]);
    }
}
