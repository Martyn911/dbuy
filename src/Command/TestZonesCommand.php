<?php
namespace App\Command;

use App\Entity\Zone;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Services\WhoisChecker;
use Symfony\Component\Finder\Finder;

/**
 * A console command that test domain zones.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console domain:test-zones zone_name
 *
 * @author Sergey Martynenko
 */
class TestZonesCommand extends Command
{
    // to make your command lazily loaded, configure the $defaultName static property,
    // so it will be instantiated only when the command is actually called.
    protected static $defaultName = 'domain:test-zones';

    /**
     * @var SymfonyStyle
     */
    private $io;

    private $entityManager;

    public function __construct(EntityManagerInterface $em, WhoisChecker $whois)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->whois = $whois;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Check domain zones')
            ->setHelp($this->getCommandHelp())
            ->addArgument('zone', InputArgument::REQUIRED, 'The domain zone name')
        ;
    }

    /**
     * This optional method is the first one executed for a command after configure()
     * and is useful to initialize properties based on the input arguments and options.
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        // SymfonyStyle is an optional feature that Symfony provides so you can
        // apply a consistent look to the commands of your application.
        // See https://symfony.com/doc/current/console/style.html
        $this->io = new SymfonyStyle($input, $output);
    }

    /**
     * This method is executed after initialize() and before execute(). Its purpose
     * is to check if some of the options/arguments are missing and interactively
     * ask the user for those values.
     *
     * This method is completely optional. If you are developing an internal console
     * command, you probably should not implement this method because it requires
     * quite a lot of work. However, if the command is meant to be used by external
     * users, this method is a nice way to fall back and prevent errors.
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        if (null !== $input->getArgument('zone')) {
            return;
        }
        
        $this->io->title('Check zone');

        // Ask for the zone if it's not defined
        $zone = $input->getArgument('zone');
        if (null !== $zone) {
            $this->io->text(' > <info>Zonn</info>: ' . $zone);
        } else {
            $zone = $this->io->ask('Zone');
            $input->setArgument('zone', $zone);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $zone = $input->getArgument('zone');

        $finder = new Finder();
        if('all' == $zone){
            $finder->files()->in(__DIR__ . '/domains')->name('*.txt');
        } else {
            $finder->files()->in(__DIR__ . '/domains')->name($zone . '.txt');
        }

        $contents = '';
        foreach ($finder as $file) {
            $contents .= $file->getContents();
        }
        $domains = explode("\n", $contents);
        shuffle($domains);

        foreach($domains as $domain){
            $this->io->comment($domain);
            $this->whois->check($domain);
            sleep(1);
        }
    }

    /**
     * The command help is usually included in the configure() method, but when
     * it's too long, it's better to define a separate method to maintain the
     * code readability.
     */
    private function getCommandHelp()
    {
        return <<<'HELP'

HELP;
    }
}
