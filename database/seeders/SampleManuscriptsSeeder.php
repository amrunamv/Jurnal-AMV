<?php

namespace Database\Seeders;

use App\Models\Affiliation;
use App\Models\Contributor;
use App\Models\Journal;
use App\Models\Manuscript;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class SampleManuscriptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample affiliations
        $affiliations = [
            'Stanford University' => Affiliation::firstOrCreate(
                ['name' => 'Stanford University'],
                ['country' => 'United States', 'city' => 'Stanford']
            ),
            'Harvard University' => Affiliation::firstOrCreate(
                ['name' => 'Harvard University'],
                ['country' => 'United States', 'city' => 'Cambridge']
            ),
            'National University of Singapore' => Affiliation::firstOrCreate(
                ['name' => 'National University of Singapore'],
                ['country' => 'Singapore', 'city' => 'Singapore']
            ),
            'University of Tokyo' => Affiliation::firstOrCreate(
                ['name' => 'University of Tokyo'],
                ['country' => 'Japan', 'city' => 'Tokyo']
            ),
            'UAE University' => Affiliation::firstOrCreate(
                ['name' => 'UAE University'],
                ['country' => 'United Arab Emirates', 'city' => 'Al Ain']
            ),
            'MIT' => Affiliation::firstOrCreate(
                ['name' => 'Massachusetts Institute of Technology'],
                ['country' => 'United States', 'city' => 'Cambridge']
            ),
            'University of Waterloo' => Affiliation::firstOrCreate(
                ['name' => 'University of Waterloo'],
                ['country' => 'Canada', 'city' => 'Waterloo']
            ),
            'Johns Hopkins University' => Affiliation::firstOrCreate(
                ['name' => 'Johns Hopkins University'],
                ['country' => 'United States', 'city' => 'Baltimore']
            ),
            'UC Berkeley' => Affiliation::firstOrCreate(
                ['name' => 'University of California, Berkeley'],
                ['country' => 'United States', 'city' => 'Berkeley']
            ),
            'Kyoto University' => Affiliation::firstOrCreate(
                ['name' => 'Kyoto University'],
                ['country' => 'Japan', 'city' => 'Kyoto']
            ),
            'UC San Diego' => Affiliation::firstOrCreate(
                ['name' => 'University of California, San Diego'],
                ['country' => 'United States', 'city' => 'San Diego']
            ),
            'Chinese Academy of Sciences' => Affiliation::firstOrCreate(
                ['name' => 'Chinese Academy of Sciences'],
                ['country' => 'China', 'city' => 'Beijing']
            ),
            'University of Gothenburg' => Affiliation::firstOrCreate(
                ['name' => 'University of Gothenburg'],
                ['country' => 'Sweden', 'city' => 'Gothenburg']
            ),
        ];

        // Create sample journals
        $journals = [
            'Computer Science' => Journal::firstOrCreate(
                ['name' => 'AMV Journal of Computer Science and Medicine'],
                [
                    'issn' => '2234-5678',
                    'e_issn' => '2234-5679',
                    'description' => 'A multidisciplinary journal focusing on the intersection of computer science and medical research.',
                    'publisher' => 'AMV Open Science',
                    'is_active' => true,
                ]
            ),
            'Environmental Science' => Journal::firstOrCreate(
                ['name' => 'AMV Journal of Environmental Science and Sustainability'],
                [
                    'issn' => '2345-6789',
                    'e_issn' => '2345-6790',
                    'description' => 'Publishing research on environmental sustainability and ecological solutions.',
                    'publisher' => 'AMV Open Science',
                    'is_active' => true,
                ]
            ),
            'Quantum Computing' => Journal::firstOrCreate(
                ['name' => 'AMV Journal of Quantum Computing and Security'],
                [
                    'issn' => '2456-7890',
                    'e_issn' => '2456-7891',
                    'description' => 'Advancing research in quantum computing and cybersecurity.',
                    'publisher' => 'AMV Open Science',
                    'is_active' => true,
                ]
            ),
            'Neuroscience' => Journal::firstOrCreate(
                ['name' => 'AMV Journal of Neuroscience and Cognitive Development'],
                [
                    'issn' => '2567-8901',
                    'e_issn' => '2567-8902',
                    'description' => 'Exploring the frontiers of neuroscience and cognitive development.',
                    'publisher' => 'AMV Open Science',
                    'is_active' => true,
                ]
            ),
            'Marine Science' => Journal::firstOrCreate(
                ['name' => 'AMV Journal of Marine Environmental Science'],
                [
                    'issn' => '2678-9012',
                    'e_issn' => '2678-9013',
                    'description' => 'Research on marine ecosystems and environmental protection.',
                    'publisher' => 'AMV Open Science',
                    'is_active' => true,
                ]
            ),
        ];

        // Create Volumes and Issues for each Journal
        $issues = [];
        foreach ($journals as $key => $journal) {
            $volume = \App\Models\Volume::firstOrCreate(
                ['journal_id' => $journal->id, 'volume_number' => 1],
                ['year' => 2024]
            );

            $issue = \App\Models\Issue::firstOrCreate(
                ['volume_id' => $volume->id, 'issue_number' => 1],
                [
                    'is_published' => true,
                    'publication_date' => now()->subMonths(2),
                    'title' => 'Volume 1, Issue 1 (2024)',
                    'cover_image' => null, // scalable if needed
                ]
            );
            $issues[$key] = $issue;
        }

        // Create sample users (authors)
        $users = [
            'sarah.johnson@stanford.edu' => User::firstOrCreate(
                ['email' => 'sarah.johnson@stanford.edu'],
                [
                    'name' => 'Dr. Sarah Johnson',
                    'first_name' => 'Sarah',
                    'last_name' => 'Johnson',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0002-1234-5678',
                    'affiliation_id' => $affiliations['Stanford University']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'm.chen@harvard.edu' => User::firstOrCreate(
                ['email' => 'm.chen@harvard.edu'],
                [
                    'name' => 'Prof. Michael Chen',
                    'first_name' => 'Michael',
                    'last_name' => 'Chen',
                    'salutation' => 'Prof.',
                    'orcid' => '0000-0003-2345-6789',
                    'affiliation_id' => $affiliations['Harvard University']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'maria.rodriguez@nus.edu.sg' => User::firstOrCreate(
                ['email' => 'maria.rodriguez@nus.edu.sg'],
                [
                    'name' => 'Dr. Maria Rodriguez',
                    'first_name' => 'Maria',
                    'last_name' => 'Rodriguez',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0004-3456-7890',
                    'affiliation_id' => $affiliations['National University of Singapore']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'k.tanaka@u-tokyo.ac.jp' => User::firstOrCreate(
                ['email' => 'k.tanaka@u-tokyo.ac.jp'],
                [
                    'name' => 'Dr. Kenji Tanaka',
                    'first_name' => 'Kenji',
                    'last_name' => 'Tanaka',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0005-4567-8901',
                    'affiliation_id' => $affiliations['University of Tokyo']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'ahmed.almansouri@uaeu.ac.ae' => User::firstOrCreate(
                ['email' => 'ahmed.almansouri@uaeu.ac.ae'],
                [
                    'name' => 'Dr. Ahmed Al-Mansouri',
                    'first_name' => 'Ahmed',
                    'last_name' => 'Al-Mansouri',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0006-5678-9012',
                    'affiliation_id' => $affiliations['UAE University']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'emily.zhang@mit.edu' => User::firstOrCreate(
                ['email' => 'emily.zhang@mit.edu'],
                [
                    'name' => 'Dr. Emily Zhang',
                    'first_name' => 'Emily',
                    'last_name' => 'Zhang',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0007-6789-0123',
                    'affiliation_id' => $affiliations['MIT']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'd.williams@uwaterloo.ca' => User::firstOrCreate(
                ['email' => 'd.williams@uwaterloo.ca'],
                [
                    'name' => 'Prof. David Williams',
                    'first_name' => 'David',
                    'last_name' => 'Williams',
                    'salutation' => 'Prof.',
                    'orcid' => '0000-0008-7890-1234',
                    'affiliation_id' => $affiliations['University of Waterloo']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'l.anderson@jhu.edu' => User::firstOrCreate(
                ['email' => 'l.anderson@jhu.edu'],
                [
                    'name' => 'Dr. Lisa Anderson',
                    'first_name' => 'Lisa',
                    'last_name' => 'Anderson',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0009-8901-2345',
                    'affiliation_id' => $affiliations['Johns Hopkins University']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'c.mendez@berkeley.edu' => User::firstOrCreate(
                ['email' => 'c.mendez@berkeley.edu'],
                [
                    'name' => 'Dr. Carlos Mendez',
                    'first_name' => 'Carlos',
                    'last_name' => 'Mendez',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0010-9012-3456',
                    'affiliation_id' => $affiliations['UC Berkeley']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'y.nakamura@kyoto-u.ac.jp' => User::firstOrCreate(
                ['email' => 'y.nakamura@kyoto-u.ac.jp'],
                [
                    'name' => 'Prof. Yuki Nakamura',
                    'first_name' => 'Yuki',
                    'last_name' => 'Nakamura',
                    'salutation' => 'Prof.',
                    'orcid' => '0000-0011-0123-4567',
                    'affiliation_id' => $affiliations['Kyoto University']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'j.oconnor@ucsd.edu' => User::firstOrCreate(
                ['email' => 'j.oconnor@ucsd.edu'],
                [
                    'name' => 'Dr. James O\'Connor',
                    'first_name' => 'James',
                    'last_name' => 'O\'Connor',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0012-1234-5678',
                    'affiliation_id' => $affiliations['UC San Diego']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'ml.wu@qdio.ac.cn' => User::firstOrCreate(
                ['email' => 'ml.wu@qdio.ac.cn'],
                [
                    'name' => 'Dr. Mei-Ling Wu',
                    'first_name' => 'Mei-Ling',
                    'last_name' => 'Wu',
                    'salutation' => 'Dr.',
                    'orcid' => '0000-0013-2345-6789',
                    'affiliation_id' => $affiliations['Chinese Academy of Sciences']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
            'henrik.svensson@marine.gu.se' => User::firstOrCreate(
                ['email' => 'henrik.svensson@marine.gu.se'],
                [
                    'name' => 'Prof. Henrik Svensson',
                    'first_name' => 'Henrik',
                    'last_name' => 'Svensson',
                    'salutation' => 'Prof.',
                    'orcid' => '0000-0014-3456-7890',
                    'affiliation_id' => $affiliations['University of Gothenburg']->id,
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]
            ),
        ];

        // Assign author role to all users
        foreach ($users as $user) {
            if (!$user->hasRole('author')) {
                $user->assignRole('author');
            }
        }

        // Manuscript 1: Machine Learning in Healthcare (Published)
        $manuscript1 = Manuscript::firstOrCreate(
            ['title' => 'The Impact of Machine Learning on Healthcare Diagnostics: A Systematic Review'],
            [
                'abstract' => 'This systematic review examines the application of machine learning algorithms in healthcare diagnostics, analyzing 150 peer-reviewed studies published between 2018-2023. Our findings indicate that machine learning models, particularly deep learning architectures, demonstrate superior performance in medical image analysis with an average accuracy of 94.3% compared to traditional methods.',
                'keywords' => ['machine learning', 'healthcare diagnostics', 'deep learning', 'medical imaging', 'systematic review'],
                'status' => Manuscript::STATUS_PUBLISHED,
                'journal_id' => $journals['Computer Science']->id,
                'submitter_id' => $users['sarah.johnson@stanford.edu']->id,
                'doi' => '10.1234/amvos.2024.001',
                'page_start' => 145,
                'page_end' => 178,
                'view_count' => rand(500, 2000),
                'download_count' => rand(200, 800),
                'submitted_at' => '2024-03-15',
                'accepted_at' => '2024-07-10',
                'accepted_at' => '2024-07-10',
                'published_at' => '2024-08-22',
                'issue_id' => $issues['Computer Science']->id,
            ]
        );

        // Add contributors for Manuscript 1
        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript1->id, 'email' => 'sarah.johnson@stanford.edu'],
            [
                'user_id' => $users['sarah.johnson@stanford.edu']->id,
                'given_name' => 'Sarah',
                'family_name' => 'Johnson',
                'orcid' => '0000-0002-1234-5678',
                'affiliation_id' => $affiliations['Stanford University']->id,
                'affiliation_text' => 'Department of Computer Science, Stanford University',
                'role' => 'author',
                'order' => 1,
                'is_corresponding' => true,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript1->id, 'email' => 'm.chen@harvard.edu'],
            [
                'user_id' => $users['m.chen@harvard.edu']->id,
                'given_name' => 'Michael',
                'family_name' => 'Chen',
                'orcid' => '0000-0003-2345-6789',
                'affiliation_id' => $affiliations['Harvard University']->id,
                'affiliation_text' => 'School of Medicine, Harvard University',
                'role' => 'author',
                'order' => 2,
                'is_corresponding' => false,
            ]
        );

        // Manuscript 2: Vertical Farming (Published)
        $manuscript2 = Manuscript::firstOrCreate(
            ['title' => 'Sustainable Urban Agriculture: Vertical Farming Solutions for Food Security in Megacities'],
            [
                'abstract' => 'Urban population growth presents unprecedented challenges for food security and sustainability. This study investigates vertical farming as a viable solution for megacities, examining three pilot projects in Singapore, Tokyo, and Dubai over 24 months.',
                'keywords' => ['vertical farming', 'urban agriculture', 'food security', 'sustainability', 'megacities'],
                'status' => Manuscript::STATUS_PUBLISHED,
                'journal_id' => $journals['Environmental Science']->id,
                'submitter_id' => $users['maria.rodriguez@nus.edu.sg']->id,
                'doi' => '10.1234/amvos.2024.002',
                'page_start' => 45,
                'page_end' => 89,
                'view_count' => rand(400, 1500),
                'download_count' => rand(150, 600),
                'submitted_at' => '2024-01-20',
                'accepted_at' => '2024-05-05',
                'accepted_at' => '2024-05-05',
                'published_at' => '2024-06-15',
                'issue_id' => $issues['Environmental Science']->id,
            ]
        );

        // Add contributors for Manuscript 2
        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript2->id, 'email' => 'maria.rodriguez@nus.edu.sg'],
            [
                'user_id' => $users['maria.rodriguez@nus.edu.sg']->id,
                'given_name' => 'Maria',
                'family_name' => 'Rodriguez',
                'orcid' => '0000-0004-3456-7890',
                'affiliation_id' => $affiliations['National University of Singapore']->id,
                'affiliation_text' => 'Institute of Sustainable Agriculture, National University of Singapore',
                'role' => 'author',
                'order' => 1,
                'is_corresponding' => true,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript2->id, 'email' => 'k.tanaka@u-tokyo.ac.jp'],
            [
                'user_id' => $users['k.tanaka@u-tokyo.ac.jp']->id,
                'given_name' => 'Kenji',
                'family_name' => 'Tanaka',
                'orcid' => '0000-0005-4567-8901',
                'affiliation_id' => $affiliations['University of Tokyo']->id,
                'affiliation_text' => 'Department of Environmental Engineering, University of Tokyo',
                'role' => 'author',
                'order' => 2,
                'is_corresponding' => false,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript2->id, 'email' => 'ahmed.almansouri@uaeu.ac.ae'],
            [
                'user_id' => $users['ahmed.almansouri@uaeu.ac.ae']->id,
                'given_name' => 'Ahmed',
                'family_name' => 'Al-Mansouri',
                'orcid' => '0000-0006-5678-9012',
                'affiliation_id' => $affiliations['UAE University']->id,
                'affiliation_text' => 'Sustainable Development Research Center, UAE University',
                'role' => 'author',
                'order' => 3,
                'is_corresponding' => false,
            ]
        );

        // Manuscript 3: Quantum Computing (Published)
        $manuscript3 = Manuscript::firstOrCreate(
            ['title' => 'Quantum Computing Applications in Cryptography: Post-Quantum Security Protocols'],
            [
                'abstract' => 'The advent of quantum computing poses existential threats to current cryptographic systems. This paper presents a comprehensive analysis of post-quantum cryptographic protocols designed to withstand attacks from quantum computers.',
                'keywords' => ['quantum computing', 'post-quantum cryptography', 'lattice-based cryptography', 'cybersecurity', 'encryption'],
                'status' => Manuscript::STATUS_PUBLISHED,
                'journal_id' => $journals['Quantum Computing']->id,
                'submitter_id' => $users['emily.zhang@mit.edu']->id,
                'doi' => '10.1234/amvos.2024.003',
                'page_start' => 201,
                'page_end' => 245,
                'view_count' => rand(600, 2500),
                'download_count' => rand(300, 1000),
                'submitted_at' => '2024-02-10',
                'accepted_at' => '2024-07-20',
                'accepted_at' => '2024-07-20',
                'published_at' => '2024-09-05',
                'issue_id' => $issues['Quantum Computing']->id,
            ]
        );

        // Add contributors for Manuscript 3
        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript3->id, 'email' => 'emily.zhang@mit.edu'],
            [
                'user_id' => $users['emily.zhang@mit.edu']->id,
                'given_name' => 'Emily',
                'family_name' => 'Zhang',
                'orcid' => '0000-0007-6789-0123',
                'affiliation_id' => $affiliations['MIT']->id,
                'affiliation_text' => 'Department of Computer Science, MIT',
                'role' => 'author',
                'order' => 1,
                'is_corresponding' => true,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript3->id, 'email' => 'd.williams@uwaterloo.ca'],
            [
                'user_id' => $users['d.williams@uwaterloo.ca']->id,
                'given_name' => 'David',
                'family_name' => 'Williams',
                'orcid' => '0000-0008-7890-1234',
                'affiliation_id' => $affiliations['University of Waterloo']->id,
                'affiliation_text' => 'Institute for Quantum Computing, University of Waterloo',
                'role' => 'author',
                'order' => 2,
                'is_corresponding' => false,
            ]
        );

        // Manuscript 4: Neuroplasticity (Under Review)
        $manuscript4 = Manuscript::firstOrCreate(
            ['title' => 'Neuroplasticity and Language Acquisition in Bilingual Children: A Longitudinal fMRI Study'],
            [
                'abstract' => 'This longitudinal study investigates neuroplasticity mechanisms underlying language acquisition in bilingual children aged 5-10 years. Using functional magnetic resonance imaging (fMRI), we tracked neural activation patterns during language processing tasks over 36 months in 85 participants.',
                'keywords' => ['neuroplasticity', 'bilingualism', 'language acquisition', 'fMRI', 'child development'],
                'status' => Manuscript::STATUS_UNDER_REVIEW,
                'journal_id' => $journals['Neuroscience']->id,
                'submitter_id' => $users['l.anderson@jhu.edu']->id,
                'view_count' => 0,
                'download_count' => 0,
                'submitted_at' => '2024-11-08',
            ]
        );

        // Add contributors for Manuscript 4
        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript4->id, 'email' => 'l.anderson@jhu.edu'],
            [
                'user_id' => $users['l.anderson@jhu.edu']->id,
                'given_name' => 'Lisa',
                'family_name' => 'Anderson',
                'orcid' => '0000-0009-8901-2345',
                'affiliation_id' => $affiliations['Johns Hopkins University']->id,
                'affiliation_text' => 'Department of Neuroscience, Johns Hopkins University',
                'role' => 'author',
                'order' => 1,
                'is_corresponding' => true,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript4->id, 'email' => 'c.mendez@berkeley.edu'],
            [
                'user_id' => $users['c.mendez@berkeley.edu']->id,
                'given_name' => 'Carlos',
                'family_name' => 'Mendez',
                'orcid' => '0000-0010-9012-3456',
                'affiliation_id' => $affiliations['UC Berkeley']->id,
                'affiliation_text' => 'Institute of Cognitive Science, University of California, Berkeley',
                'role' => 'author',
                'order' => 2,
                'is_corresponding' => false,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript4->id, 'email' => 'y.nakamura@kyoto-u.ac.jp'],
            [
                'user_id' => $users['y.nakamura@kyoto-u.ac.jp']->id,
                'given_name' => 'Yuki',
                'family_name' => 'Nakamura',
                'orcid' => '0000-0011-0123-4567',
                'affiliation_id' => $affiliations['Kyoto University']->id,
                'affiliation_text' => 'Brain Research Institute, Kyoto University',
                'role' => 'author',
                'order' => 3,
                'is_corresponding' => false,
            ]
        );

        // Manuscript 5: Microplastics (Submitted)
        $manuscript5 = Manuscript::firstOrCreate(
            ['title' => 'Microplastic Pollution in Deep Ocean Ecosystems: Distribution, Impact, and Remediation Strategies'],
            [
                'abstract' => 'Microplastic pollution has emerged as a critical environmental threat, yet deep ocean ecosystems remain understudied. This research presents findings from 18-month deep-sea sampling campaigns across the Pacific, Atlantic, and Indian Oceans at depths of 2,000-6,000 meters.',
                'keywords' => ['microplastics', 'deep ocean', 'marine pollution', 'biodiversity', 'environmental remediation'],
                'status' => Manuscript::STATUS_SUBMITTED,
                'journal_id' => $journals['Marine Science']->id,
                'submitter_id' => $users['j.oconnor@ucsd.edu']->id,
                'view_count' => 0,
                'download_count' => 0,
                'submitted_at' => '2025-01-15',
            ]
        );

        // Add contributors for Manuscript 5
        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript5->id, 'email' => 'j.oconnor@ucsd.edu'],
            [
                'user_id' => $users['j.oconnor@ucsd.edu']->id,
                'given_name' => 'James',
                'family_name' => 'O\'Connor',
                'orcid' => '0000-0012-1234-5678',
                'affiliation_id' => $affiliations['UC San Diego']->id,
                'affiliation_text' => 'Scripps Institution of Oceanography, UC San Diego',
                'role' => 'author',
                'order' => 1,
                'is_corresponding' => true,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript5->id, 'email' => 'ml.wu@qdio.ac.cn'],
            [
                'user_id' => $users['ml.wu@qdio.ac.cn']->id,
                'given_name' => 'Mei-Ling',
                'family_name' => 'Wu',
                'orcid' => '0000-0013-2345-6789',
                'affiliation_id' => $affiliations['Chinese Academy of Sciences']->id,
                'affiliation_text' => 'Institute of Oceanology, Chinese Academy of Sciences',
                'role' => 'author',
                'order' => 2,
                'is_corresponding' => false,
            ]
        );

        Contributor::firstOrCreate(
            ['manuscript_id' => $manuscript5->id, 'email' => 'henrik.svensson@marine.gu.se'],
            [
                'user_id' => $users['henrik.svensson@marine.gu.se']->id,
                'given_name' => 'Henrik',
                'family_name' => 'Svensson',
                'orcid' => '0000-0014-3456-7890',
                'affiliation_id' => $affiliations['University of Gothenburg']->id,
                'affiliation_text' => 'Department of Marine Sciences, University of Gothenburg',
                'role' => 'author',
                'order' => 3,
                'is_corresponding' => false,
            ]
        );

        $this->command->info('✅ Sample manuscripts seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - 13 Affiliations');
        $this->command->info('   - 5 Journals');
        $this->command->info('   - 13 Users (Authors)');
        $this->command->info('   - 5 Manuscripts (3 Published, 1 Under Review, 1 Submitted)');
        $this->command->info('   - 13 Contributors');
    }
}
