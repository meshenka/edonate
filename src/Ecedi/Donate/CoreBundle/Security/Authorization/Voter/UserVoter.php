<?php

namespace Ecedi\Donate\CoreBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter implements VoterInterface
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * List of supported attributes
     * @param  string  $attribute
     * @return boolean
     */
    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::VIEW,
            self::EDIT,
            self::DELETE,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'Ecedi\Donate\CoreBundle\Entity\User';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @var Ecedi\Donate\CoreBundle\Entity\User
     */
    public function vote(TokenInterface $token, $user, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($user))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if (1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW, EDIT or DELETE'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $currentUser = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$currentUser instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch ($attribute) {
            case self::VIEW:

                // * can view self
                if ($currentUser->isUser($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                // * super admin can viex any other users
                if ($currentUser->hasRole('ROLE_ADMIN')) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                //others cannot view others
                break;

            case self::EDIT:
                // * can edit self
                if ($currentUser->isUser($user)) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                // * super admin can edit any other users
                if ($currentUser->hasRole('ROLE_ADMIN')) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                break;
            case self::DELETE:
                // * cannot delete self
                if ($currentUser->isUser($user)) {
                    return VoterInterface::ACCESS_DENIED;
                }

                // * standard user cannot delete anyone
                if ($currentUser->hasRole('ROLE_USER')) {
                    return VoterInterface::ACCESS_DENIED;
                }

                // * super admin can delete any other users
                if ($currentUser->hasRole('ROLE_ADMIN')) {
                    return VoterInterface::ACCESS_GRANTED;
                }

                // we assume that our data object has a method getOwner() to
                // get the current owner user entity for this data object
                // if ($user->getId() === $post->getOwner()->getId()) {
                //     return VoterInterface::ACCESS_GRANTED;
                // }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
