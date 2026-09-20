<?php

namespace App\Security\Voter;

use App\Contract\UserOwnedInterface;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

final class UserOwnedVoter extends Voter
{
    public const string CAN_EDIT = 'CAN_EDIT';
    public const string CAN_VIEW = 'CAN_VIEW';
    public const string CAN_DELETE = 'CAN_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::CAN_EDIT, self::CAN_VIEW, self::CAN_DELETE])
            && $subject instanceof UserOwnedInterface;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        /** @var null|User $user */
        $user = $token->getUser();

//        // if the user is anonymous, do not grant access
//        if (!$user instanceof UserInterface) {
//            $vote?->addReason('The user must be logged in to access this resource.');
//
//            return false;
//        }

        if (!$subject instanceof UserOwnedInterface) {
            return false;
        }

        if (self::CAN_VIEW === $attribute) {
            // Same rule as the collection: your own posts, or ownerless posts if you're anonymous
            if (!$user instanceof User) {
                return null === $subject->getUser();
            }

            return $subject->getUser()?->getId() === $user->getId();
        }

        if (!$user instanceof User) {
            return false;
        }

        return $user->getId() === $subject->getUser()?->getId();
    }
}
