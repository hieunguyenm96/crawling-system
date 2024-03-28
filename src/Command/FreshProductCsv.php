<?php
namespace App\Command;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:fresh-product-csv', description: 'Clean up products.csv')]
class FreshProductCsv extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln([
            'Fresh Product CSV',
            '============',
            'Now is ' . (new \DateTime())->format('H:i:s'),
            ''
        ]);

        $fsObject = new Filesystem();

        try {
            // Remove old file
            $fsObject->remove('./src/Resources/products.csv');

            // Create new file
            $new_file_path = './src/Resources/products.csv';
            $fsObject->touch($new_file_path);
            $fsObject->chmod($new_file_path, 0777);
            $fsObject->dumpFile($new_file_path, 'product_id, product_name, product_image, original_price, price, discount' . PHP_EOL);
        }
        catch (IOExceptionInterface $exception) {
            echo "Error creating file at" . $exception->getPath();
        }

        $output->writeln('End at ' . (new \DateTime())->format('H:i:s'));

        return Command::SUCCESS;
    }
}