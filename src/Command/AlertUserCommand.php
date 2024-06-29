<?php

// src/Command/AlertUserCommand.php

namespace App\Command;

use App\Repository\ContratRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class AlertUserCommand extends Command
{
    protected static $defaultName = 'app:alert-user';
    private $entityManager;
    private $contratRepository;
    private $mailer;


    public function __construct(EntityManagerInterface $entityManager, ContratRepository $contratRepository, MailerInterface $mailer)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->contratRepository = $contratRepository;
        $this->mailer = $mailer;
    }

    protected function configure()
    {
        $this
            ->setDescription('Send alert emails to users when contract end date is less than 2 months away.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
{
    $currentDate = new \DateTime();
    $twoMonthsFromNow = (clone $currentDate)->modify('+2 months');

    $contrats = $this->contratRepository->createQueryBuilder('c')
        ->where('c.date_fin <= :twoMonthsFromNow')
        ->andWhere('c.date_fin >= :currentDate') // Add condition to exclude contracts with already finished date_fin
        ->setParameter('twoMonthsFromNow', $twoMonthsFromNow)
        ->setParameter('currentDate', $currentDate) // Pass current date to the query
        ->getQuery()
        ->getResult();

    foreach ($contrats as $contrat) {
        $email = (new Email())
            ->from('no-reply@contratlab.com')
            ->to($contrat->getUser()->getEmail())
            ->subject('Votre contrat expire bientôt')
            ->text('Cher ' . $contrat->getUser()->getNom() . ', votre contrat expire le ' . $contrat->getDateFin()->format('Y-m-d') . '. Veuillez prendre les mesures nécessaires, votre nom de contrat : ' . $contrat->getNom() );

        $this->mailer->send($email);
    }

    $output->writeln('Alert emails sent successfully.');

    return Command::SUCCESS;

    }
    function alertUser($userId) {
        global $userId;
    $userId = 1;
    $user = $this->entityManager->getRepository('App:User')->find($userId);
    }
}
