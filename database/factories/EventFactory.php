<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Tableau d'événements réalistes
        $events = [
            [
                'title' => 'Coupe du Monde de Football',
                'description' => 'La compétition internationale de football la plus prestigieuse, réunissant les meilleures équipes nationales du monde.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Cérémonie des Golden Globes',
                'description' => 'Cérémonie annuelle récompensant les meilleures productions cinématographiques et télévisées.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Cérémonie des Oscars',
                'description' => 'La plus haute distinction dans l\'industrie cinématographique, récompensant les meilleures réalisations du cinéma.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Assemblée générale des Nations Unies',
                'description' => 'Réunion annuelle des représentants de tous les États membres des Nations Unies.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de Cannes',
                'description' => 'Le festival international du film le plus prestigieux au monde.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Roland-Garros',
                'description' => 'Tournoi de tennis majeur disputé sur les courts du Stade Roland-Garros à Paris.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Tour de France',
                'description' => 'La plus célèbre course cycliste au monde, traversant la France et parfois les pays voisins.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Fête de la Musique',
                'description' => 'Célébration annuelle de la musique qui a lieu chaque 21 juin dans de nombreuses villes à travers le monde.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Salon de l\'Auto de Genève',
                'description' => 'Le plus ancien salon automobile international, présentant les dernières innovations de l\'industrie automobile.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Foire de Paris',
                'description' => 'Grande foire commerciale et industrielle annuelle à Paris.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival d\'Avignon',
                'description' => 'Festival international de théâtre qui se déroule chaque été à Avignon, mettant en scène des compagnies du monde entier.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Nuit Blanche',
                'description' => 'Manifestation culturelle nocturne où les musées, monuments et lieux publics restent ouverts toute la nuit.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Fête des Lumières',
                'description' => 'Festival lyonnais célèbre pour ses spectacles de lumière et d\'art lumineux qui illuminent la ville.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Mondial de l\'Automobile',
                'description' => 'Le plus grand salon automobile allemand, présentant les dernières innovations et concepts automobiles.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival des Vieilles Charrues',
                'description' => 'Le plus grand festival de musique breton, rassemblant des artistes internationaux dans un cadre champêtre.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Biennale de Venise',
                'description' => 'La plus ancienne biennale d\'art du monde, présentant des œuvres d\'artistes contemporains internationaux.',
                'cycle' => 'biennial'
            ],
            [
                'title' => 'Festival de Jazz de Montreux',
                'description' => 'Festival suisse de jazz prestigieux attirant des musiciens de renommée mondiale depuis plus de 50 ans.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Ultra Music Festival',
                'description' => 'Festival de musique électronique majeur qui se déroule dans plusieurs villes du monde.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Comic-Con',
                'description' => 'Convention internationale dédiée aux bandes dessinées, films, séries et jeux vidéo.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de Glastonbury',
                'description' => 'L\'un des plus grands festivals de musique et d\'arts du monde, se déroulant dans le Somerset en Angleterre.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'MotoGP',
                'description' => 'Championnat du monde de vitesse moto, la plus haute compétition de motocyclisme sur circuit.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Formule 1',
                'description' => 'Championnat du monde de course automobile, la plus prestigieuse compétition de vitesse sur circuit.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Jeux Olympiques',
                'description' => 'Compétition sportive internationale multiséculaire rassemblant des athlètes du monde entier.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Conférence TED',
                'description' => 'Conférence internationale sur la technologie, le divertissement et le design, rassemblant des penseurs et innovateurs.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de Sundance',
                'description' => 'Festival américain de cinéma indépendant, célèbre pour lancer les carrières de nouveaux réalisateurs.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Fête de la Science',
                'description' => 'Manifestation nationale française visant à promouvoir les sciences et techniques auprès du grand public.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival des Arts de la Rue',
                'description' => 'Festival célébrant les arts de la rue avec des spectacles de cirque, de danse et de théâtre.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Salon du Livre',
                'description' => 'Salon international dédié à l\'édition et à la littérature, rassemblant auteurs, éditeurs et lecteurs.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de la Gastronomie',
                'description' => 'Événement célébrant la cuisine française et internationale avec des démonstrations de chefs renommés.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival du Cinéma Africain',
                'description' => 'Festival dédié au cinéma africain, mettant en avant les talents et les histoires du continent africain.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de la Photographie',
                'description' => 'Exposition internationale de photographie rassemblant des artistes et des œuvres de renommée mondiale.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de l\'Architecture',
                'description' => 'Manifestation internationale dédiée à l\'architecture contemporaine et aux innovations en matière de design urbain.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de la Mode',
                'description' => 'Semaine de la mode internationale présentant les collections des plus grands créateurs et maisons de couture.',
                'cycle' => 'seasonly'
            ],
            [
                'title' => 'Festival de la Technologie',
                'description' => 'Conférence internationale sur les dernières innovations technologiques et les tendances de demain.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de l\'Écologie',
                'description' => 'Manifestation dédiée à la protection de l\'environnement et aux solutions durables pour l\'avenir.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de l\'Histoire',
                'description' => 'Événement éducatif célébrant l\'histoire mondiale avec des conférences, des reconstitutions et des expositions.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de la Musique Classique',
                'description' => 'Festival prestigieux de musique classique rassemblant des orchestres et solistes de renommée internationale.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival du Court Métrage',
                'description' => 'Festival international dédié au court métrage, mettant en avant les talents émergents du cinéma.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de la Bande Dessinée',
                'description' => 'Convention internationale dédiée à la bande dessinée, rassemblant auteurs, éditeurs et fans.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival de la Danse',
                'description' => 'Festival international de danse présentant des compagnies et chorégraphes de renommée mondiale.',
                'cycle' => 'yearly'
            ],
            [
                'title' => 'Festival du Cinéma d\'Animation',
                'description' => 'Festival dédié au cinéma d\'animation, mettant en avant les meilleures productions nationales et internationales.',
                'cycle' => 'yearly'
            ]
        ];

        // Sélectionner un événement aléatoire
        $event = fake()->randomElement($events);

        // Générer des dates réalistes (dates futures uniquement)
        $startDate = fake()->dateTimeBetween('now', '+2 years');
        // S'assurer que la date de fin est toujours après la date de début
        $endDate = fake()->dateTimeBetween(
            $startDate->modify('+1 hour'),
            $startDate->modify('+1 week')
        );

        return [
            'title' => $event['title'],
            'description' => $event['description'],
            'cycle' => $event['cycle'],
            'created_by' => fake()->numberBetween(1, 10),
            'date_time_start' => $startDate,
            'date_time_end' => $endDate,
            'address' => fake()->streetAddress() . ', ' . fake()->city() . ', ' . fake()->country(),
            'latitude' => fake()->latitude(-13.459, 5.386),
            'longitude' => fake()->longitude(12.039, 31.305),
            'available' => fake()->numberBetween(20, 50),
            'remaining_seats' => 0,
                ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Event $event) {
            $users = User::inRandomOrder()->take(rand(10, 200))->pluck('id');
            // Préparer les données avec l'état
            $userData = [];
            foreach ($users as $userId) {
                $userData[$userId] = ['etat' => '1'];
            }
            $event->favoritedByUsers()->attach($userData);
        });
    }
}