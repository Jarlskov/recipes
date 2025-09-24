<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Dish;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class DishVoter extends Voter
{
    public const OWNER = 'OWNER';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return $attribute === self::OWNER && $subject instanceof Dish;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            return false;
        }

        /** @var Dish $dish */
        $dish = $subject;

        return $dish->getOwner() === $user;
    }
}
