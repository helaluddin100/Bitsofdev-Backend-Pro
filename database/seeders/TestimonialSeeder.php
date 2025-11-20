<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Testimonial;
use Faker\Factory as Faker;

class TestimonialSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        $projectTypes = [
            'web-development',
            'mobile-app',
            'ui-ux-design',
            'e-commerce',
            'consulting',
            'seo',
            'digital-marketing',
            'other'
        ];

        $roles = [
            'CEO', 'CTO', 'Founder', 'Director', 'Manager', 'Lead Developer',
            'Marketing Director', 'Product Manager', 'Business Owner',
            'Operations Manager', 'Head of Technology', 'VP of Engineering'
        ];

        $companies = [
            'TechStart Inc.', 'HealthTech Solutions', 'GrowthCorp', 'FinanceFlow',
            'Digital Innovations', 'CloudTech Systems', 'DataDriven Corp', 'NextGen Solutions',
            'InnovateLab', 'FutureTech', 'SmartSystems', 'ProActive Solutions',
            'EliteDigital', 'PrimeTech', 'Advanced Solutions', 'Modern Enterprises',
            'TechVision', 'Digital Dynamics', 'Innovation Hub', 'TechForge',
            'NextWave Technologies', 'Digital Pioneers', 'TechMasters', 'Innovation Partners',
            'Future Systems', 'Digital Leaders', 'TechExcellence', 'Innovation Labs',
            'Advanced Digital', 'TechSolutions Pro', 'Digital Innovators', 'TechForward',
            'NextGen Digital', 'Innovation Systems', 'TechPioneers', 'Digital Excellence',
            'Future Innovations', 'TechLeaders', 'Digital Masters', 'Innovation Tech'
        ];

        $testimonials = [
            // Web Development Testimonials
            [
                'name' => 'Sarah Johnson',
                'role' => 'CEO',
                'company' => 'TechStart Inc.',
                'content' => 'sparkedev transformed our vision into a stunning web application. Their attention to detail and technical expertise exceeded our expectations. The team delivered on time and within budget.',
                'rating' => 5,
                'project_type' => 'web-development',
                'project_name' => 'E-commerce Platform',
                'location' => 'San Francisco, CA',
                'is_featured' => true,
                'is_verified' => true
            ],
            [
                'name' => 'Michael Chen',
                'role' => 'Founder',
                'company' => 'HealthTech Solutions',
                'content' => 'The mobile app they developed for us has revolutionized how our patients interact with our services. Exceptional work and ongoing support.',
                'rating' => 5,
                'project_type' => 'mobile-app',
                'project_name' => 'Healthcare Mobile App',
                'location' => 'New York, NY',
                'is_featured' => true,
                'is_verified' => true
            ],
            [
                'name' => 'Emily Rodriguez',
                'role' => 'Marketing Director',
                'company' => 'GrowthCorp',
                'content' => 'Professional, reliable, and incredibly talented. They delivered our project on time and within budget. Highly recommended!',
                'rating' => 5,
                'project_type' => 'web-development',
                'project_name' => 'Corporate Website',
                'location' => 'Los Angeles, CA',
                'is_featured' => true,
                'is_verified' => true
            ],
            [
                'name' => 'David Kim',
                'role' => 'CTO',
                'company' => 'FinanceFlow',
                'content' => 'Their expertise in both frontend and backend development made our complex financial platform a reality. Outstanding team!',
                'rating' => 5,
                'project_type' => 'web-development',
                'project_name' => 'Financial Dashboard',
                'location' => 'Chicago, IL',
                'is_featured' => true,
                'is_verified' => true
            ]
        ];

        // Generate additional testimonials
        for ($i = 0; $i < 200; $i++) {
            $projectType = $faker->randomElement($projectTypes);
            $role = $faker->randomElement($roles);
            $company = $faker->randomElement($companies);

            // 95% chance of 5-star rating, 5% chance of random rating (3-4 stars)
            $rating = $faker->boolean(95) ? 5 : $faker->numberBetween(3, 4);

            $testimonials[] = [
                'name' => $faker->name(),
                'role' => $role,
                'company' => $company,
                'content' => $this->generateTestimonialContent($projectType, $faker, $rating),
                'rating' => $rating,
                'project_type' => $projectType,
                'project_name' => $this->generateProjectName($projectType, $faker),
                'location' => $faker->city() . ', ' . $faker->stateAbbr(),
                'is_featured' => $faker->boolean(20), // 20% chance of being featured
                'is_verified' => $faker->boolean(85), // 85% chance of being verified
                'sort_order' => $faker->numberBetween(0, 1000)
            ];
        }

        // Insert all testimonials
        foreach ($testimonials as $testimonial) {
            Testimonial::create([
                ...$testimonial,
                'is_active' => true,
                'submitted_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'created_at' => $faker->dateTimeBetween('-2 years', 'now'),
                'updated_at' => now()
            ]);
        }
    }

    private function generateTestimonialContent($projectType, $faker, $rating = 5)
    {
        // Different templates based on rating
        if ($rating >= 5) {
            $templates = [
                'web-development' => [
                    'sparkedev created an amazing website that perfectly represents our brand. The user experience is outstanding and the performance is incredible.',
                    'Our new website has transformed our online presence. The team understood our vision and delivered beyond our expectations.',
                    'The website they built for us is not only beautiful but also highly functional. Our conversion rates have increased significantly.',
                    'Professional, creative, and technically excellent. They delivered a website that exceeded all our requirements.',
                    'The responsive design and user interface are perfect. Our customers love the new website experience.'
                ],
            'mobile-app' => [
                'The mobile app they developed has been a game-changer for our business. User engagement has increased dramatically.',
                'Outstanding mobile app development. The app is intuitive, fast, and exactly what we envisioned.',
                'They created a beautiful and functional mobile app that our users absolutely love. Highly recommended!',
                'The mobile app development process was smooth and professional. The final product exceeded our expectations.',
                'Our mobile app has received excellent reviews from users. The development team was fantastic to work with.'
            ],
            'ui-ux-design' => [
                'The UI/UX design work was exceptional. They created an intuitive and beautiful interface that our users love.',
                'Outstanding design work that perfectly captured our brand identity. The user experience is seamless.',
                'They transformed our ideas into a stunning visual design. The attention to detail is remarkable.',
                'The design team understood our vision and created something even better than we imagined.',
                'Beautiful, functional, and user-friendly design. Our customers have given us great feedback on the new interface.'
            ],
            'e-commerce' => [
                'Our e-commerce platform has been a huge success. The shopping experience is smooth and conversion rates are up.',
                'They built an amazing online store that handles high traffic and provides excellent user experience.',
                'The e-commerce solution they delivered has transformed our business. Sales have increased significantly.',
                'Professional e-commerce development with excellent payment integration and user experience.',
                'Our online store is now our primary revenue source. The development team did an outstanding job.'
            ],
            'consulting' => [
                'Their technical consulting helped us make the right technology decisions. Very knowledgeable and professional.',
                'Excellent consulting services that guided us through complex technical challenges.',
                'They provided valuable insights that helped us optimize our technology stack and processes.',
                'Professional consulting that delivered real value to our business operations.',
                'Their expertise in technology consulting was exactly what we needed to move forward.'
            ],
            'seo' => [
                'Our search engine rankings have improved dramatically since working with sparkedev. Great SEO results!',
                'The SEO optimization work has significantly increased our organic traffic and visibility.',
                'Professional SEO services that delivered measurable results. Our website traffic has doubled.',
                'They helped us understand and implement effective SEO strategies. The results speak for themselves.',
                'Outstanding SEO work that improved our online visibility and brought in more qualified leads.'
            ],
            'digital-marketing' => [
                'Our digital marketing campaigns have been highly successful. They understand how to reach our target audience.',
                'Professional digital marketing services that delivered excellent ROI. Highly recommended!',
                'They created and executed marketing campaigns that significantly increased our brand awareness.',
                'Outstanding digital marketing expertise. Our online presence has grown substantially.',
                'The marketing strategies they implemented have been very effective in growing our business.'
            ],
            'other' => [
                'Excellent work on our project. Professional, reliable, and delivered exactly what we needed.',
                'They exceeded our expectations with their technical expertise and attention to detail.',
                'Outstanding service and results. We would definitely work with them again.',
                'Professional team that delivered high-quality work on time and within budget.',
                'Great experience working with sparkedev. They understood our needs and delivered excellent results.'
            ]
        ];
        } else {
            // Templates for lower ratings (3-4 stars)
            $templates = [
                'web-development' => [
                    'The website development was good overall. There were some minor issues but they were resolved quickly.',
                    'Decent work on our website. The team was professional and delivered what was promised.',
                    'The website meets our basic requirements. Some features could be improved but overall satisfied.',
                    'Good website development experience. The team was responsive and addressed our concerns.',
                    'The website works well for our needs. There were some delays but the final result is acceptable.'
                ],
                'mobile-app' => [
                    'The mobile app development was satisfactory. The app works as expected with minor issues.',
                    'Good mobile app development. The team delivered on time and the app functions well.',
                    'The mobile app meets our requirements. Some features could be enhanced but overall good work.',
                    'Decent mobile app development experience. The team was professional and responsive.',
                    'The mobile app works for our business needs. There were some challenges but they were resolved.'
                ],
                'ui-ux-design' => [
                    'The design work was good. The interface is functional and meets our requirements.',
                    'Decent UI/UX design work. The team understood our needs and delivered accordingly.',
                    'Good design experience. The final product is clean and user-friendly.',
                    'The design meets our expectations. Some improvements could be made but overall satisfied.',
                    'Professional design work. The team delivered what was requested on time.'
                ],
                'e-commerce' => [
                    'The e-commerce platform works well for our business. Some features could be enhanced.',
                    'Good e-commerce development. The platform handles our transactions smoothly.',
                    'Decent e-commerce solution. The team delivered a functional platform for our needs.',
                    'The e-commerce site meets our requirements. Overall satisfied with the development.',
                    'Good e-commerce development experience. The platform works as expected.'
                ],
                'consulting' => [
                    'The consulting services were helpful. The team provided good insights for our project.',
                    'Decent consulting experience. The team was knowledgeable and provided useful advice.',
                    'Good consulting services. The team helped us make informed decisions.',
                    'The consulting was valuable for our project. Some recommendations were very helpful.',
                    'Professional consulting services. The team delivered what was promised.'
                ],
                'seo' => [
                    'The SEO work has been helpful. We\'ve seen some improvements in our rankings.',
                    'Good SEO services. The team provided useful strategies for our website.',
                    'Decent SEO work. We\'ve noticed some positive changes in our search visibility.',
                    'The SEO services meet our expectations. The team was professional and responsive.',
                    'Good SEO experience. The team delivered on their promises.'
                ],
                'digital-marketing' => [
                    'The digital marketing campaigns have been effective. We\'ve seen some good results.',
                    'Good digital marketing services. The team created engaging campaigns for our brand.',
                    'Decent digital marketing work. The campaigns have helped increase our online presence.',
                    'The digital marketing meets our needs. The team was professional and delivered results.',
                    'Good digital marketing experience. The campaigns have been beneficial for our business.'
                ],
                'other' => [
                    'Good work on our project. The team delivered what was requested.',
                    'Decent project experience. The team was professional and met our requirements.',
                    'The project was completed satisfactorily. Some improvements could be made but overall good.',
                    'Good project delivery. The team was responsive and addressed our needs.',
                    'Professional project work. The team delivered on time and within budget.'
                ]
            ];
        }

        $templateList = $templates[$projectType] ?? $templates['other'];
        return $faker->randomElement($templateList);
    }

    private function generateProjectName($projectType, $faker)
    {
        $names = [
            'web-development' => [
                'Corporate Website', 'Business Website', 'Portfolio Site', 'Landing Page',
                'Company Website', 'Professional Site', 'Business Portal', 'Marketing Website'
            ],
            'mobile-app' => [
                'Mobile Application', 'iOS App', 'Android App', 'Cross-Platform App',
                'Business App', 'Customer App', 'Employee App', 'Service App'
            ],
            'ui-ux-design' => [
                'UI/UX Redesign', 'Interface Design', 'User Experience', 'Design System',
                'Brand Identity', 'Visual Design', 'User Interface', 'Design Guidelines'
            ],
            'e-commerce' => [
                'Online Store', 'E-commerce Platform', 'Shopping Website', 'Digital Store',
                'Online Marketplace', 'Retail Website', 'E-commerce Solution', 'Online Shop'
            ],
            'consulting' => [
                'Technical Consulting', 'Technology Assessment', 'System Architecture',
                'Digital Strategy', 'Technology Planning', 'IT Consulting', 'Tech Advisory'
            ],
            'seo' => [
                'SEO Optimization', 'Search Engine Marketing', 'SEO Campaign',
                'Organic Growth', 'SEO Strategy', 'Search Optimization', 'SEO Services'
            ],
            'digital-marketing' => [
                'Digital Marketing Campaign', 'Online Marketing', 'Social Media Marketing',
                'Content Marketing', 'Digital Strategy', 'Marketing Automation', 'Brand Campaign'
            ],
            'other' => [
                'Custom Project', 'Business Solution', 'Digital Solution', 'Technology Project',
                'Custom Development', 'Business Application', 'Digital Platform', 'Tech Solution'
            ]
        ];

        $nameList = $names[$projectType] ?? $names['other'];
        return $faker->randomElement($nameList);
    }
}
