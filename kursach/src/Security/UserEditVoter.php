<?php


namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Security;


class UserEditVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {

        if (!in_array($attribute, [self::VIEW, self::EDIT])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Post object, thanks to supports
        /** @var User $user */
        $user_to_edit = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user_to_edit, $user);
            case self::EDIT:
                return $this->canEdit($user_to_edit, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $user_to_edit, User $user): bool
    {
        // if they can edit, they can view
        if ($this->canEdit($user_to_edit, $user)) {
            return true;
        }

        return true;
    }

    private function canEdit(User $user_to_edit, User $user): bool
    {
        // this assumes that the data object has a getOwner() method
        // to get the entity of the user who owns this data object
        return $user === $user_to_edit || $this->security->isGranted('ROLE_MODERATOR');
    }
}