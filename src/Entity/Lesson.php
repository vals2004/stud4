<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * Lesson
 *
 * @Vich\Uploadable
 * @ORM\Table(name="lesson")
 * @ORM\Entity(repositoryClass="App\Repository\LessonRepository")
 */
class Lesson
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
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startTime", type="time")
     */
    private $startTime;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endTime", type="time")
     */
    private $endTime;
    
    /**
     * @var Subject
     *
     * @ORM\ManyToOne(targetEntity="Subject")
     */
    private $subject;

    /**
     * @var LessonType
     *
     * @ORM\Column(name="type", type="LessonType", nullable=false)
     * @DoctrineAssert\Enum(entity="App\DBAL\Types\LessonType") 
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $document;

    /**
     * @Vich\UploadableField(mapping="lesson_documents", fileNameProperty="document")
     * @var File
     */
    private $documentFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $documentLab;

    /**
     * @Vich\UploadableField(mapping="lesson_documents", fileNameProperty="documentLab")
     * @var File
     */
    private $documentLabFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $documentLab1;

    /**
     * @Vich\UploadableField(mapping="lesson_documents", fileNameProperty="documentLab1")
     * @var File
     */
    private $documentLabFile1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $documentLab2;

    /**
     * @Vich\UploadableField(mapping="lesson_documents", fileNameProperty="documentLab2")
     * @var File
     */
    private $documentLabFile2;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $documentLab3;

    /**
     * @Vich\UploadableField(mapping="lesson_documents", fileNameProperty="documentLab3")
     * @var File
     */
    private $documentLabFile3;

    /**
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $updatedAt;    

    /**
     * @var Group[]
     *
     * @ORM\ManyToMany(targetEntity="Group", inversedBy="lessons")
     * @ORM\JoinTable(name="groups_lessons")    
     */
    private $groups;

    /**
     * @var Room
     *
     * @ORM\ManyToOne(targetEntity="Room", inversedBy="lessons")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="room_id", referencedColumnName="id")
     * })
     */
    private $room;

    /**
     * @var User[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="lessons")
     * @ORM\JoinTable(name="users_lessons")    
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getSubject() ? $this->getSubject()->getName().', '.$this->getRoom() : '';
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
     * Set date
     *
     * @param \DateTime $date
     *
     * @return TimeTable
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set startTime
     *
     * @param \DateTime $startTime
     *
     * @return TimeTable
     */
    public function setStartTime($startTime)
    {
        $this->startTime = $startTime;

        return $this;
    }

    /**
     * Get startTime
     *
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * Set endTime
     *
     * @param \DateTime $endTime
     *
     * @return TimeTable
     */
    public function setEndTime($endTime)
    {
        $this->endTime = $endTime;

        return $this;
    }

    /**
     * Get endTime
     *
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Add group
     *
     * @param \App\Entity\Group $group
     *
     * @return Lesson
     */
    public function addGroup(\App\Entity\Group $group)
    {
        $this->groups[] = $group;

        return $this;
    }

    /**
     * Remove group
     *
     * @param \App\Entity\Group $group
     */
    public function removeGroup(\App\Entity\Group $group)
    {
        $this->groups->removeElement($group);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set room
     *
     * @param \App\Entity\Room $room
     *
     * @return Lesson
     */
    public function setRoom(\App\Entity\Room $room = null)
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get room
     *
     * @return \App\Entity\Room
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * Add user
     *
     * @param \App\Entity\User $user
     *
     * @return Lesson
     */
    public function addUser(\App\Entity\User $user)
    {
        $this->users[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param \App\Entity\User $user
     */
    public function removeUser(\App\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Get type
     *
     * @return \App\DBAL\Types\LessonType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param \App\DBAL\Types\LessonType 
     *
     * @return Lesson
     */
    public function setType($type)
    {
        return $this->type = $type;
    }

    /**
     * Get subject
     *
     * @return \App\Entity\Subject
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set subject
     *
     * @param \App\Entity\Subject
     *
     * @return Lesson
     */
    public function setSubject(\App\Entity\Subject $subject)
    {
        return $this->subject = $subject;
    }

    public function setDocumentFile(File $document = null)
    {
        $this->documentFile = $document;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($document) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getDocumentFile()
    {
        return $this->documentFile;
    }

    public function setDocument($document)
    {
        $this->document= $document;
    }

    public function getDocument()
    {
        return $this->document;
    }

    public function setDocumentLabFile(File $document = null)
    {
        $this->documentLabFile = $document;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($document) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getDocumentLabFile()
    {
        return $this->documentLabFile;
    }

    public function setDocumentLab($document)
    {
        $this->documentLab = $document;
        $this->updatedAt = new \DateTime('now');
    }

    public function getDocumentLab()
    {
        return $this->documentLab;
    }

    public function getDocumentLab1()
    {
        return $this->documentLab1;
    }

    public function setDocumentLab1($document)
    {
        $this->documentLab1 = $document;
        $this->updatedAt = new \DateTime('now');
    }

    public function getDocumentLab2()
    {
        return $this->documentLab2;
    }

    public function setDocumentLab2($document)
    {
        $this->documentLab2 = $document;
        $this->updatedAt = new \DateTime('now');
    }

    public function getDocumentLab3()
    {
        return $this->documentLab3;
    }

    public function setDocumentLab3($document)
    {
        $this->documentLab3 = $document;
        $this->updatedAt = new \DateTime('now');
    }
    
    public function setDocumentLabFile1(File $document = null)
    {
        $this->documentLabFile1 = $document;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($document) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getDocumentLabFile1()
    {
        return $this->documentLabFile1;
    }
    public function setDocumentLabFile2(File $document = null)
    {
        $this->documentLabFile2 = $document;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($document) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getDocumentLabFile2()
    {
        return $this->documentLabFile2;
    }
    public function setDocumentLabFile3(File $document = null)
    {
        $this->documentLabFile3 = $document;

        // VERY IMPORTANT:
        // It is required that at least one field changes if you are using Doctrine,
        // otherwise the event listeners won't be called and the file is lost
        if ($document) {
            // if 'updatedAt' is not defined in your entity, use another property
            $this->updatedAt = new \DateTime('now');
        }
    }

    public function getDocumentLabFile3()
    {
        return $this->documentLabFile3;
    }
}
