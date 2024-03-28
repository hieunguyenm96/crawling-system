<?php
namespace App\Command;

use Campo\UserAgent;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

// the name of the command is what users type after "php bin/console"
#[AsCommand(name: 'app:crawl-products', description: 'Collect the products information from web pages')]
class CrawlProducts extends Command
{
    protected function configure() : void
    {
        $this
            ->addOption('store', 's', InputOption::VALUE_OPTIONAL, 'What branch or store do you want to crawl?', 'locklock-flagship-store')
            ->addOption('page', 'p', InputOption::VALUE_OPTIONAL, 'What page do you want to crawl?', 1)
            ->addOption('cookie', 'c', InputOption::VALUE_OPTIONAL, 'What cookie do you want to use?')
            ->addOption('minPage', 'm', InputOption::VALUE_OPTIONAL, 'What minimum page do you want to crawl?', 1)
            ->addOption('maxPage', 'M', InputOption::VALUE_OPTIONAL, 'What maximum page do you want to crawl?', 25)
            ->addOption('crawlAll', 'a', InputOption::VALUE_OPTIONAL, 'Crawl all pages?', false)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) : int
    {
        $output->writeln([
            'Crawl Products',
            '============',
            'Now is ' . (new \DateTime())->format('H:i:s'),
            ''
        ]);

        $store  = $input->getOption('store');
        $page   = $input->getOption('page');
        $cookie = $input->getOption('cookie');

        if ($page > $input->getOption('maxPage')) {
            $page = $input->getOption('maxPage');
        }

        $client = new HttpClient();
        $method = 'GET';
        $cookie = $cookie ?? '';

        $headers = [
            "user-agent"      => UserAgent::random(),
            'accept'          => 'application/json, text/plain, */*',
            'referer'         => 'https://www.lazada.vn/',
            'accept-language' => 'en-US,en;q=0.9',
            'cookie'          => $cookie
        ];

        do {
            $apiURL = "https://www.lazada.vn/{$store}/?ajax=true&from=wangpu&isFirstRequest=true&langFlag=vi&page={$page}&pageTypeId=2&q=All-Products";
            try {
                $response = $client->create()->request($method, $apiURL, ['headers' => $headers]);
                $content  = $response->toArray();
                $this->writeToCsv($content['mods']['listItems']);
            }
            catch (\Exception $e) {
                $output->writeln('You are blocked on page number :' . $page);
                throw $e;
            }
            if (!$input->getOption('crawlAll')) {
                break;
            }
            $page++;
        } while ($page <= $input->getOption('maxPage'));

        $output->writeln('Crawl completed...');
        $output->writeln('End at ' . (new \DateTime())->format('H:i:s'));

        return Command::SUCCESS;
    }

    /**
     * Writes the given data to a CSV file.
     *
     * @param array $data The data to be written to the CSV file. Each element of the array should be an associative array with the following keys:
     *                    - itemId: The ID of the item.
     *                    - name: The name of the item.
     *                    - image: The image URL of the item.
     *                    - originalPrice: The original price of the item.
     *                    - price: The current price of the item.
     *                    - discount: The discount percentage of the item.
     * @throws \Exception If there is an error while writing to the CSV file.
     * @return void
     */
    private function writeToCsv(&$data) : void
    {
        try {
            $fp = fopen('./src/Resources/products.csv', 'a');
            foreach ($data as $fields) {
                $row = [$fields['itemId'], $fields['name'] ?? '', $fields['image'] ?? '', $fields['originalPrice'] ?? 0, $fields['price'] ?? 0, $fields['discount'] ?? ''];
                fputcsv($fp, $row);
            }
            fclose($fp);
        }
        catch (\Exception $e) {
            if (isset($fp)) {
                fclose($fp);
            }
            throw $e;
        }
    }
}
