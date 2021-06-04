<?php

namespace App\Command;

use App\Entity\Participant;
use App\Entity\Site;
use App\ManageEntity\UpdateEntity;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CsvImportCommand extends Command
{
    private $entityManager;

    private $dataDirectory;

    private $updateEntity;

    private $io;

    private $participantRepository;

    public function __construct(EntityManagerInterface $entityManager,
                                ParticipantRepository $participantRepository,
                                UpdateEntity $updateEntity,
                                string $dataDirectory)
    {
        parent::__construct();
        $this->participantRepository = $participantRepository;
        $this->entityManager = $entityManager;
        $this->dataDirectory = $dataDirectory;
        $this->updateEntity =$updateEntity;

    }


    protected static $defaultName = 'app:CsvImport';
    protected static $defaultDescription = 'Add a short description for your command';

    protected function configure(): void
    {
        $this
            ->setName('csv:import')
            ->setDescription('Importe un fichier csv');
    }

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $this->io = new SymfonyStyle($input, $output);
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $compteur=0;
        $io = new SymfonyStyle($input, $output);
        $io->title("Tentative d'importation du fichier csv...");

        /** COLONNES DU CSV :
         * email ; role ; password ; nom ; prenom ; telephone ; pseudo
         */

        $data = $this->getDataFromFile();

        foreach ($data as $row){
            $participant = new Participant();
            $string = implode(";",$row);
            //on récupère un tableau d'attributs
            $attributs = explode(";",$string);
            $participant->setEmail($attributs[0])
                        ->setPassword($attributs[1])
                        ->setNom($attributs[2])
                        ->setPrenom($attributs[3])
                        ->setTelephone($attributs[4])
                        ->setPseudo($attributs[5])
                        ->setSite($this->entityManager->getRepository(Site::class)->findOneBy(['nom'=>$attributs[6]]))
                        ->setRoles(["ROLE_USER"]);
            $compteur++;
            $this->updateEntity->save($participant);
        }

        $io->success("Super ! ".$compteur." participants ont été ajoutés");

        return Command::SUCCESS;
    }


    private function getDataFromFile()
    {
        $file = $this->dataDirectory . 'data.csv';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizer = [new ObjectNormalizer()];

        $encoders = [new CsvEncoder()];

        $serializer = new Serializer($normalizer, $encoders);

        /**@var string $fileString * */
        $fileString = file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);


        return $data;
    }

}
