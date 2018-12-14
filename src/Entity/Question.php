<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Question
 *
 * @ORM\Table(name="question")
 * @ORM\Entity(repositoryClass="App\Repository\QuestionRepository")
 */
class Question
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
     * @ORM\Column(name="text", type="string", length=255, unique=false)
     */
    private $text;

    /**
     * @var integer
     *
     * @ORM\Column(name="hide", type="integer", unique=false)
     */
    private $hide;

    /**
     * @var Answer[]
     *
     * @ORM\OneToMany(targetEntity="Answer", mappedBy="question")
     */
    private $answers;

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getText();
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
     * Set text
     *
     * @param string $text
     *
     * @return Question
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add answer
     *
     * @param \App\Entity\Answer $answer
     *
     * @return Question
     */
    public function addAnswer(\App\Entity\Answer $answer)
    {
        $this->answers[] = $answer;

        return $this;
    }

    /**
     * Remove answer
     *
     * @param \App\Entity\Answer $answer
     */
    public function removeAnswer(\App\Entity\Answer $answer)
    {
        $this->answers->removeElement($answer);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    public function getCorrectAnswers()
    {
        $correct = [];
        foreach ($this->getAnswers() as $a) {
            if ($a->getIsCorrect()) {
                $correct[] = $a;
            }
        }
        return new \Doctrine\Common\Collections\ArrayCollection($correct);
    }

    public function getHide()
    {
        return $this->hide;
    }

    public function setHide($hide)
    {
        $this->hide = $hide;
        return $this;
    }
}
