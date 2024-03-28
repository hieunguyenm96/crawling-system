<?php
namespace App\Command;

use App\Entity\Product;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:import-product', description: 'Import products.csv')]
class ImportProducts extends Command
{
    private $csvPath = './src/Resources/products.csv';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln([
            'Import Products',
            '============',
            'Now is ' . (new \DateTime())->format('H:i:s'),
            ''
        ]);

        $parsedCsv = array();
        if (($handle = fopen($this->csvPath, 'r')) !== FALSE) {
            fgetcsv($handle); // Skip the header line 
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                // $data[0] is the product id
                // $data[1] is the product name
                // $data[2] is the product image
                // $data[3] is the product original price
                // $data[4] is the product price
                // $data[5] is the product discount
                $parsedCsv[$data[0]] = [
                    'lz_id'          => $data[0],
                    'name'           => $data[1],
                    'image'          => $data[2],
                    'original_price' => $data[3],
                    'price'          => $data[4],
                    'discount'       => $data[5]
                ];
            }
            fclose($handle);
        }

        // Noted: If the amount of products is big, it will take many resource for $parsedCsv, maybe it will break the application;
        if (count($parsedCsv) > 0) {
            $em = $this->container->get('doctrine')->getManager();

            // Do bulk update existing products
            $batchSize    = 50;
            $i            = 0;
            $query        = $em->createQuery('SELECT p FROM App\Entity\Product p WHERE p.lz_id IN(:ids)')->setParameter('ids', array_keys($parsedCsv));
            $existingPIds = [];
            foreach ($query->toIterable() as $product) {
                $existingPIds[$product->getLzId()] = 'existing';

                $product->setName($parsedCsv[$product->getLzId()]['name']);
                $product->setImage($parsedCsv[$product->getLzId()]['image']);
                $product->setOriginalPrice($parsedCsv[$product->getLzId()]['original_price']);
                $product->setPrice($parsedCsv[$product->getLzId()]['price']);
                $product->setDiscount($parsedCsv[$product->getLzId()]['discount']);
                ++$i;
                if (($i % $batchSize) === 0) {
                    $em->flush(); // Executes all updates.
                    $em->clear(); // Detaches all objects from Doctrine!
                }
            }
            $em->flush(); // Persist objects that did not make up an entire batch
            // end of update existing products

            if (count($existingPIds) > 0 && count($existingPIds) === count($parsedCsv)) {
                $output->writeln('All products are imported. Nothing to create.');
                $output->writeln('End at ' . (new \DateTime())->format('H:i:s'));
                return Command::SUCCESS;
            }

            if (count($existingPIds) > 0 && count($existingPIds) !== count($parsedCsv)) {
                $parsedCsv = array_diff_key($parsedCsv, $existingPIds);
                if (count($parsedCsv) === 0) {
                    $output->writeln('Something is wrong. Nothing to create.');
                    $output->writeln('End at ' . (new \DateTime())->format('H:i:s'));
                    return Command::SUCCESS;
                }
            }

            // Do bulk insert
            $batchSize = 50;
            $parsedCsv = array_values($parsedCsv);
            for ($i = 0; $i < count($parsedCsv); ++$i) {
                $p = new Product;
                $p->setLzId($parsedCsv[$i]['lz_id']);
                $p->setName($parsedCsv[$i]['name']);
                $p->setImage($parsedCsv[$i]['image']);
                $p->setOriginalPrice($parsedCsv[$i]['original_price']);
                $p->setPrice($parsedCsv[$i]['price']);
                $p->setDiscount($parsedCsv[$i]['discount']);
                $em->persist($p);
                if (($i % $batchSize) === 0) {
                    $em->flush();
                    $em->clear(); // Detaches all objects from Doctrine!
                }
            }
            $em->flush(); // Persist objects that did not make up an entire batch
            $em->clear();
        }

        $output->writeln('Import completed.');
        $output->writeln('End at ' . (new \DateTime())->format('H:i:s'));

        return Command::SUCCESS;
    }
}