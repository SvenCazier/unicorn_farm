<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\{Message, Unicorn};

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $unicornNames = [
            "Sparkle",
            "Rainbow",
            "Twilight",
            "Stardust",
            "Moonbeam"
        ];

        $randomMessages = [
            "I absolutely adore [name]!",
            "Meeting [name] was the highlight of my day!",
            "Can't believe how magical [name] is!",
            "Thank you for letting me spend time with [name].",
            "[name] brought so much joy to my visit.",
            "I've never seen a unicorn as beautiful as [name].",
            "Spending time with [name] was a dream come true.",
            "[name] is the most graceful unicorn I've ever met.",
            "Thank you for introducing me to [name].",
            "I'll never forget my time with [name]."
        ];

        $authorNames = [
            "Unicorn Lover",
            "Magic Enthusiast",
            "Fairy Tale Fan",
            "Mystic Admirer",
            "Dreamer",
            "Fantasy Explorer",
            "Whimsical Wanderer",
            "Enchanted Traveler",
            "Celestial Seeker",
            "Mystic Dreamer",
            "Arcane Aficionado",
            "Fantastical Fanatic",
            "Unicorn Whisperer",
            "Ethereal Enthusiast",
            "Mystical Fairy",
            "Legendary Admirer",
            "Enchanted Soul",
            "Starry-eyed Seeker",
            "Mythos Lover",
            "Magical Voyager",
        ];

        foreach ($unicornNames as $unicornName) {
            $unicorn = new Unicorn();
            $unicorn->setName($unicornName);
            $manager->persist($unicorn);

            $randomMessageAmount = rand(1, 5);
            for ($i = 0; $i < $randomMessageAmount; $i++) {
                $randomMessage = $randomMessages[array_rand($randomMessages)];
                $randomMessage = str_replace('[name]', $unicornName, $randomMessage);

                $author = $authorNames[array_rand($authorNames)];

                $message = new Message();
                $message->setAuthor($author);
                $message->setMessage($randomMessage);
                $message->setUnicorn($unicorn);
                $manager->persist($message);
            }
        }

        $manager->flush();
    }
}
