{{-- Site-wide structured data: who I am and what I sell. --}}
@php
    $schema = [
        '@context' => 'https://schema.org',
        '@graph'   => [
            [
                '@type'    => 'Person',
                '@id'      => url('/#person'),
                'name'     => 'Josh Espinoza',
                'jobTitle' => 'Full Stack Software Engineer',
                'url'      => url('/'),
                'sameAs'   => ['https://github.com/joshespi'],
                'knowsAbout' => [
                    'PHP', 'Laravel', 'WordPress', 'Docker',
                    'Linux', 'DevOps', 'CI/CD', 'Web Application Security',
                ],
            ],
            [
                '@type'       => 'ProfessionalService',
                '@id'         => url('/#business'),
                'name'        => 'Josh Espinoza — Full Stack Engineer',
                'url'         => url('/'),
                'description' => 'Fixed-price WordPress, Laravel, and DevOps work from a 15-year engineer. No calls required. Written quote within 1 business day.',
                'founder'     => ['@id' => url('/#person')],
                'priceRange'  => '$99–$5,000+',
                'areaServed'  => ['@type' => 'Place', 'name' => 'Worldwide (remote)'],
                'serviceType' => [
                    'WordPress Development', 'WordPress Maintenance', 'Laravel Development',
                    'DevOps', 'Server Hardening', 'Website Security Audit',
                ],
            ],
            [
                '@type'     => 'WebSite',
                '@id'       => url('/#website'),
                'url'       => url('/'),
                'name'      => 'Josh Espinoza',
                'publisher' => ['@id' => url('/#person')],
            ],
        ],
    ];
@endphp
<x-json-ld :data="$schema" />
