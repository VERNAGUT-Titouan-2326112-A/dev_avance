<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Teacher extends User
{
    public function getRoles(): array
    {
        $roles = parent::getRoles();
        $roles[] = 'ROLE_TEACHER';

        return array_unique($roles);
    }
}