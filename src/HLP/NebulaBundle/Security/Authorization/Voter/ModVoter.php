<?php
// src/HLP/NebulaBundle/Security/Authorization/Voter/ModVoter.php
namespace HLP\NebulaBundle\Security\Authorization\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ModVoter implements VoterInterface
{
    const ADD = 'add';

    public function supportsAttribute($attribute)
    {
        return in_array($attribute, array(
            self::ADD,
        ));
    }

    public function supportsClass($class)
    {
        $supportedClass = 'HLP\NebulaBundle\Entity\User';

        return $supportedClass === $class || is_subclass_of($class, $supportedClass);
    }

    /**
     * @var \HLP\NebulaBundle\Entity\User $modder
     */
    public function vote(TokenInterface $token, $modder, array $attributes)
    {
        // check if class of this object is supported by this voter
        if (!$this->supportsClass(get_class($modder))) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // check if the voter is used correct, only allow one attribute
        // this isn't a requirement, it's just one easy way for you to
        // design your voter
        if(1 !== count($attributes)) {
            throw new \InvalidArgumentException(
                'Only one attribute is allowed for VIEW or EDIT'
            );
        }

        // set the attribute to check against
        $attribute = $attributes[0];

        // check if the given attribute is covered by this voter
        if (!$this->supportsAttribute($attribute)) {
            return VoterInterface::ACCESS_ABSTAIN;
        }

        // get current logged in user
        $user = $token->getUser();

        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return VoterInterface::ACCESS_DENIED;
        }

        switch($attribute) {
            case self::ADD:
                // we assume that our data object has a method getOwner() to
                // get the current owner user entity for this data object
                if ($user->getId() === $modder->getId()) {
                    return VoterInterface::ACCESS_GRANTED;
                }
                break;
        }

        return VoterInterface::ACCESS_DENIED;
    }
}
