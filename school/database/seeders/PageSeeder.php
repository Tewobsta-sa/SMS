<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\ContentBlock;
use App\Models\User;

class PageSeeder extends Seeder
{
    public function run(): void
    {
        // Get the first user for created_by
        $user = User::first();

        if (!$user) {
            $this->command->error('No users found. Please run UserSeeder first.');
            return;
        }

        // Create Home page
        $homePage = Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'short_description' => 'Welcome to Veritas Afrika - Empowering Your Business',
            'is_active' => true,
            'display_order' => 0,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $aboutPage = Page::create([
            'title' => 'About',
            'slug' => 'about',
            'short_description' => 'Learn more about Veritas Afrika Co.Ltd',
            'is_active' => true,
            'display_order' => 1,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);


        // Create section for Home page (Features)
        $heroFeatures = PageSection::create([
            'page_id' => $homePage->id,
            'title' => 'hero Features',
            'subtitle' => 'Discover Our Key Features',
            'display_order' => 1,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $aboutFeatures = PageSection::create([
            'page_id' => $homePage->id,
            'title' => 'About Features',
            'subtitle' => 'Who We Are',
            'display_order' => 2,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $videoSection = PageSection::create([
            'page_id' => $homePage->id,
            'title' => 'Video Section',
            'subtitle' => 'Watch Our Introduction',
            'display_order' => 3,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        $aboutSection = PageSection::create([
            'page_id' => $aboutPage->id,
            'title' => 'About Us',
            'subtitle' => 'Learn More About Us',
            'display_order' => 4,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Create content block for Home page features (as a list)
        ContentBlock::create([
            'section_id' => $heroFeatures->id,
            'type' => 'list',
            'title' => 'Key Features',
            'slug' => 'key-features',
            'list_items' => [
                [
                    'title' => 'Professionalism',
                    'icon' => 'bi bi-shield-check',
                    'description' => 'Our team consists of experienced leaders recognized regionally and...',
                ],
                [
                    'title' => 'Client-Centric Approach',
                    'icon' => 'bi bi-person-heart',
                    'description' => 'Getting our clients what they deserve is our mission. We prioritize...',
                ],
                [
                    'title' => 'Regional Impact',
                    'icon' => 'bi bi-globe',
                    'description' => 'We address local development challenges using effective...',
                ],
            ],
            'display_order' => 1,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

    
        // Create content block for About Us image
        ContentBlock::create([
            'section_id' => $aboutFeatures->id,
            'type' => 'image',
            'title' => 'Veritas Afrika Co.Ltd',
            'slug' => 'veritas-afrika-co-ltd',
            'subtitle' => 'Who We Are',
            'short_description' => "",
            'content' => '<p>Veritas Afrika Co.Ltd is a multi-disciplinary company of professional consultants specializing in a wide range of civil engineering works. We provide expert services to government, non-government, and private-sector customers.</p>',
            'display_order' => 2,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Create Video Section content block
        ContentBlock::create([
            'section_id' => $videoSection->id,
            'type' => 'video',
            'title' => 'Video Section',
            'slug' => 'video-section',
            'subtitle' => 'Working Process',
            'short_description' => 'Company market share in the domestic market',
            'content' => '',
            'video_url' => 'https://www.youtube.com/watch?v=MDF2vmMFtQg&list=RDzHdAB4xj3GI&index=7',
            'display_order' => 3,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Create image content block for Video section
        ContentBlock::create([
            'section_id' => $videoSection->id,
            'type' => 'image',
            'title' => 'Video Thumbnail',
            'slug' => 'video-thumbnail',
            'subtitle' => '',
            'short_description' => '',
            'content' => '',

            'display_order' => 4,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Create image content block for Video section
        ContentBlock::create([
            'section_id' => $videoSection->id,
            'type' => 'list',
            'title' => 'Video Details',
            'slug' => 'video-details',
            'subtitle' => '',
            'short_description' => 'Serving with expertise in industries as one of World leading Corporation ',
            'list_items' => [
                [
                    'title' => 'Available To All Industries',
                    'icon' => '',
                    'description' => 'Our specialists offer manufacturing of complex machined precision parts, as well as turning and milling, to support a wide host of industries.',
                ],
            ],
            'metadata' => [
                'data1' => 'Manufacturing',
                'data2' => 'Pharmaceutical',
                'data3' => 'Defense',
                'data4' => 'Off-Road / Petroleum',
                'data5' => 'Nuclear',
                'data6' => 'Automotive',
            ],
            'display_order' => 5,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);

        // Create content block for About Us section
        ContentBlock::create([ 
            'section_id' => $aboutSection->id,
            'type' => 'image',
            'title' => 'About Section 1',
            'slug' => 'about-section-1',
            'subtitle' => 'About Us',
            'short_description' => "",
            'content' => '<h3><strong>Veritas Afrika</strong>&nbsp;</h3><p>Co.Ltd is a multi-disciplinary company of professional consultants specializing in a wide range of civil engineering works. We provide expert services to government, non-government,&nbsp;</p><ul><li>and private-sector&nbsp;</li><li>customers.</li></ul>',
            'display_order' => 6,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        ContentBlock::create([ 
            'section_id' => $aboutSection->id,
            'type' => 'image',
            'title' => 'About Section 2',
            'slug' => 'about-section-2',
            'subtitle' => 'About Us',
            'short_description' => "",
            'content' => '<h2><strong>professional</strong></h2><p><em style="text-decoration: underline;">&nbsp;consultant</em>s specializing in a wide range of civil engineering works. We provide expert services to government, non-government, and private-sector customers.</p>',
            'display_order' => 7,
            'is_active' => true,
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
    }
}
