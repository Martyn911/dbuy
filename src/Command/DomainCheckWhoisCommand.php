<?php
namespace App\Command;

use App\Entity\Zone;
use App\Utils\Validator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Services\WhoisChecker;

/**
 * A console command that update and return whois info by domain.
 *
 * To use this command, open a terminal window, enter into your project
 * directory and execute the following:
 *
 *     $ php bin/console domain:check-whois
 *
 * @author Sergey Martynenko
 */
class DomainCheckWhoisCommand extends Command
{
    // to make your command lazily loaded, configure the $defaultName static property,
    // so it will be instantiated only when the command is actually called.
    protected static $defaultName = 'domain:check-whois';

    /**
     * @var SymfonyStyle
     */
    private $io;

    private $entityManager;
    private $validator;

    public function __construct(EntityManagerInterface $em, Validator $validator, WhoisChecker $whois)
    {
        parent::__construct();

        $this->entityManager = $em;
        $this->validator = $validator;
        $this->whois = $whois;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Update and return whois info by domain')
            ->setHelp($this->getCommandHelp())
            // commands can optionally define arguments and/or options (mandatory and optional)
            // see https://symfony.com/doc/current/components/console/console_arguments.html
            ->addArgument('domain', InputArgument::REQUIRED, 'The domain name')
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
        if (null !== $input->getArgument('domain')) {
            return;
        }

        $this->io->title('Domain Check Whois Command');

        // Ask for the domain if it's not defined
        $domain = $input->getArgument('domain');
        if (null !== $domain) {
            $this->io->text(' > <info>Domain</info>: '.$domain);
        } else {
            $domain = $this->io->ask('Domain', null, [$this->validator, 'validateDomain']);
            $input->setArgument('domain', $domain);
        }
    }

    /**
     * This method is executed after interact() and initialize(). It usually
     * contains the logic to execute to complete this command task.
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $domain = $input->getArgument('domain');

        $this->io->success('Whois info updated');

        print_r($this->whois->check($domain));
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
