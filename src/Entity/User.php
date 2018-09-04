<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\EquatableInterface;
use Serializable;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Misd\PhoneNumberBundle\Validator\Constraints\PhoneNumber as AssertPhoneNumber;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @UniqueEntity("email")
 * @UniqueEntity("phone")
 */
class User implements UserInterface, Serializable, EquatableInterface
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="phone_number", unique=true, nullable=false)
     * @AssertPhoneNumber(defaultRegion="UA")
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255, nullable=false)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="first_name", type="string", length=255, nullable=false)
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(name="last_name", type="string", length=255, nullable=false)
     */
    private $lastName;

    /**
     * @var string
     *
     * @ORM\Column(name="confirmation_code", type="string", length=255, nullable=true)
     */
    private $confirmationCode;

    /**
     * @var string
     *
     * @ORM\Column(name="forgot_code", type="string", length=255, nullable=true)
     */
    private $forgotCode;

    /**
     * @var Group 
     *
     * @ORM\ManyToOne(targetEntity="Group", inversedBy="students")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="group_id", referencedColumnName="id")
     * })
     */
    private $group;

    /**
     * @var Location[]
     *
     * @ORM\OneToMany(targetEntity="Location", mappedBy="user")
     */
    private $locations;

    /**
     * @var Lesson[]
     *
     * @ORM\ManyToMany(targetEntity="Lesson", mappedBy="users")
     */
    private $lessons;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_admin", type="boolean", options={"default": 0})
     */
    private $isAdmin;

    /**
     * @var Exam[]
     *
     * @ORM\OneToMany(targetEntity="Exam", mappedBy="user")
     */
    private $exams;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->locations = new \Doctrine\Common\Collections\ArrayCollection();
        $this->lessons = new \Doctrine\Common\Collections\ArrayCollection();
        $this->isAdmin = 0;
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     *
     * @return User
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return User
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Convert User to String
     *
     * @return String
     */
    public function __toString()
    {
        return $this->getFirstName().' '.$this->getLastName().' ('.$this->getGroup().')';
    }

    public function getSalt()
    {
        return null;
    }

    public function getUsername()
    {
        return $this->email ?: $this->uid;
    }

    public function eraseCredentials()
    {
        //
    }

    /** @see \Serializable::serialize() */
    public function serialize()
    {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    /** @see \Serializable::unserialize() */
    public function unserialize($serialized)
    {
        list (
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized);
    }

    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof User) {
            return $user->getId() == $this->getId();
        }

        return false;
    }

    /**
     * Get roles
     *
     * @return String[]
     */
    public function getRoles()
    {
        $result = ['ROLE_USER'];
        if ($this->getIsAdmin()) {
            $result[] = 'ROLE_ADMIN';
        }
        return $result;
    }

    /**
     * Add location
     *
     * @param \App\Entity\Location $location
     *
     * @return User
     */
    public function addLocation(\App\Entity\Location $location)
    {
        $this->locations[] = $location;

        return $this;
    }

    /**
     * Remove location
     *
     * @param \App\Entity\Location $location
     */
    public function removeLocation(\App\Entity\Location $location)
    {
        $this->locations->removeElement($location);
    }

    /**
     * Get locations
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     * Set group
     *
     * @param \App\Entity\Group $group
     *
     * @return User
     */
    public function setGroup(\App\Entity\Group $group = null)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return \App\Entity\Group
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Add lesson
     *
     * @param \App\Entity\Lesson $lesson
     *
     * @return User
     */
    public function addLesson(\App\Entity\Lesson $lesson)
    {
        $this->lessons[] = $lesson;

        return $this;
    }

    /**
     * Remove lesson
     *
     * @param \App\Entity\Lesson $lesson
     */
    public function removeLesson(\App\Entity\Lesson $lesson)
    {
        $this->lessons->removeElement($lesson);
    }

    /**
     * Get lessons
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLessons()
    {
        return $this->lessons;
    }

    /**
     * Set isAdmin
     *
     * @param boolean $isAdmin
     *
     * @return User
     */
    public function setIsAdmin($isAdmin)
    {
        $this->isAdmin = $isAdmin;

        return $this;
    }

    /**
     * Get isAdmin
     *
     * @return boolean
     */
    public function getIsAdmin()
    {
        return $this->isAdmin;
    }

    /**
     * Add exam
     *
     * @param \App\Entity\Exam $exam
     *
     * @return User
     */
    public function addExam(\App\Entity\Exam $exam)
    {
        $this->exams[] = $exam;

        return $this;
    }

    /**
     * Remove exam
     *
     * @param \App\Entity\Exam $exam
     */
    public function removeExam(\App\Entity\Exam $exam)
    {
        $this->exams->removeElement($exam);
    }

    /**
     * Get exams
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getExams()
    {
        return $this->exams;
    }

    public function getConfirmationCode()
    {
        return $this->confirmationCode;
    }

    public function setConfirmationCode($confirmationCode)
    {
        $this->confirmationCode = $confirmationCode;

        return $this;
    }

    public function getForgotCode()
    {
        return $this->forgotCode;
    }

    public function setForgotCode($forgotCode)
    {
        $this->forgotCode = $forgotCode;

        return $this;
    }
}
