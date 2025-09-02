<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\QAPair;

class QAPairSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $qaPairs = [
            [
                'question' => 'What services do you offer?',
                'answer_1' => 'We offer comprehensive web development services including custom websites, e-commerce solutions, and web applications.',
                'answer_2' => 'Our mobile app development services cover both iOS and Android platforms with native and cross-platform solutions.',
                'answer_3' => 'We provide UI/UX design services to create beautiful and user-friendly interfaces for your digital products.',
                'answer_4' => 'Our digital consultation services help businesses optimize their online presence and digital strategy.',
                'answer_5' => 'We also offer maintenance and support services to keep your digital products running smoothly.',
                'category' => 'Services',
                'is_active' => true
            ],
            [
                'question' => 'How much does a website cost?',
                'answer_1' => 'Our basic website packages start from $999 for simple business websites with essential features.',
                'answer_2' => 'Professional websites with advanced functionality typically range from $2,499 to $4,999.',
                'answer_3' => 'Enterprise-level solutions with custom features can cost $5,000 and above depending on complexity.',
                'answer_4' => 'E-commerce websites start from $3,999 and include payment integration and inventory management.',
                'answer_5' => 'We offer flexible payment plans and can work within your budget to deliver the best value.',
                'category' => 'Pricing',
                'is_active' => true
            ],
            [
                'question' => 'What is your development process?',
                'answer_1' => 'Our process begins with discovery and planning where we understand your requirements and goals.',
                'answer_2' => 'Next, we create detailed wireframes and designs to visualize your project before development.',
                'answer_3' => 'During development, we use agile methodology with regular updates and feedback sessions.',
                'answer_4' => 'We conduct thorough testing to ensure quality and performance before launch.',
                'answer_5' => 'After launch, we provide ongoing support and maintenance to keep your project running smoothly.',
                'category' => 'Process',
                'is_active' => true
            ],
            [
                'question' => 'Can I see your portfolio?',
                'answer_1' => 'Absolutely! You can view our portfolio on the Projects page of our website.',
                'answer_2' => 'We have completed over 150 projects for various industries including healthcare, education, and e-commerce.',
                'answer_3' => 'Our portfolio includes case studies showing our development process and results.',
                'answer_4' => 'We can also provide specific examples relevant to your industry or project type.',
                'answer_5' => 'Feel free to contact us for a detailed portfolio presentation tailored to your needs.',
                'category' => 'Portfolio',
                'is_active' => true
            ],
            [
                'question' => 'How long does it take to complete a project?',
                'answer_1' => 'Simple websites typically take 2-4 weeks to complete from start to finish.',
                'answer_2' => 'Complex web applications can take 8-16 weeks depending on features and requirements.',
                'answer_3' => 'Mobile apps usually require 6-12 weeks for development and testing.',
                'answer_4' => 'E-commerce projects typically take 4-8 weeks including payment integration and testing.',
                'answer_5' => 'We provide detailed timelines during the planning phase and keep you updated throughout the process.',
                'category' => 'Timeline',
                'is_active' => true
            ]
        ];

        foreach ($qaPairs as $qaPair) {
            QAPair::create($qaPair);
        }
    }
}
