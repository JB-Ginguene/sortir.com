<?php


namespace App\Upload;


use App\Entity\Participant;

class ProfilAvatar
{
    public function save($file, Participant $profile,$directory)
    {
        $newFileName = $profile->getNom() . '-' . uniqid() . '-' . $file->guessExtension();
        $file->move($directory, $newFileName);
        $profile->setAvatar($newFileName);

    }

}