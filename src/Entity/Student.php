<?php

namespace App\Entity;

use App\Repository\UserRepository; // On peut utiliser le même repo ou en créer un spécifique
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Student extends User
{
    public function getRoles(): array
    {
        $roles = parent::getRoles();
        $roles[] = 'ROLE_STUDENT';

        return array_unique($roles);
    }
}