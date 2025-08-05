<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\AsciiSlugger;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $slugger = new AsciiSlugger();

        for ($i = 1; $i <= 12; $i++) {
            $product = new Product();
            $name = 'product ' . $i;
            $product->setName($name);
            $product->setSlug($slugger->slug($name));
            $product->setPrice(\random_int(10, 100));
            $product->setDescription("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut iaculis ipsum ut libero hendrerit, eu aliquam lectus porttitor. Pellentesque dignissim odio in felis ultricies consectetur.");

            $manager->persist($product);
        }

        $manager->flush();
    }
}
