<?php

namespace App\Command;

use App\Service\MailerService;
use App\Repository\UsersRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:boas-vindas',
    description: 'Envia um e-mail de boas-vindas a todos os usuários ativos.',
)]
class BoasVindasCommand extends Command
{
    private MailerService $mailer;
    private UsersRepository $usersRepository;

    public function __construct(MailerService $mailer, UsersRepository $usersRepository)
    {
        parent::__construct();
        $this->mailer = $mailer;
        $this->usersRepository = $usersRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // $usuarios = $this->usersRepository->findBy(['status' => true]);
        $usuarios = $this->usersRepository->findAtivos();

        foreach ($usuarios as $usuario) {
            $this->mailer->boasVindas($usuario->getEmail(), $usuario->getNome());
            $output->writeln("E-mail enviado para: " . $usuario->getEmail());
        }

        $output->writeln('Todos os e-mails de boas-vindas foram enviados com sucesso.');
        return Command::SUCCESS;
    }
}
