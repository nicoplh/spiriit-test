<?php

namespace App\Command;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:product:export',
    description: 'CSV product export',
)]
class ProductExportCommand extends Command
{
    public function __construct(
        private readonly ProductRepository $productRepository,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $products = $this->productRepository->findAllSorted();

        $filePath = "var/products.csv";
        $handle = fopen($filePath, 'wb+');
        fputcsv($handle, [
            'Nom',
            'Prix',
        ]);

        /** @var Product $product */
        foreach ($products as $product) {
            fputcsv($handle, [
               $product->getName(),
               $product->getPrice(),
            ]);
        }
        fclose($handle);

        $io->success('Les produits ont été exportés, le fichier se trouve dans le dossier var.');

        return Command::SUCCESS;
    }
}
