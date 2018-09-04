<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExamAnswer
 *
 * @ORM\Table(name="exam_answer")
 * @ORM\Entity(repositoryClass="App\Repository\ExamAnswerRepository")
 * @ORM\HasLifecycleCallbacks 
 */
class ExamAnswer
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
     * @var Exam
     *
     * @ORM\ManyToOne(targetEntity="Exam", inversedBy="answers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="exam_id", referencedColumnName="id")
     * })
     */
    private $exam;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="Question")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     * })
     */
    private $question;

    /**
     * @var []
     *
     * @ORM\Column(name="answers", type="simple_array")
     */
    private $answers;

     /**
     * @var \DateTime
     * 
     * @ORM\Column(name="created_at", type="datetime", nullable = true)
     */
    protected $createdAt;   

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @ORM\PrePersist
     */
    public function createdTimestamps()
    {
        $this->setCreatedAt(new \DateTime('now'));
    }

    /**
     * Set answers
     *
     * @param array $answers
     *
     * @return ExamAnswer
     */
    public function setAnswers($answers)
    {
        $this->answers = $answers;

        return $this;
    }

    /**
     * Get answers
     *
     * @return array
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Set exam
     *
     * @param \App\Entity\Exam $exam
     *
     * @return ExamAnswer
     */
    public function setExam(\App\Entity\Exam $exam = null)
    {
        $this->exam = $exam;

        return $this;
    }

    /**
     * Get exam
     *
     * @return \App\Entity\Exam
     */
    public function getExam()
    {
        return $this->exam;
    }

    /**
     * Set question
     *
     * @param \App\Entity\Question $question
     *
     * @return ExamAnswer
     */
    public function setQuestion(\App\Entity\Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get question
     *
     * @return \App\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return ExamAnswer
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
}
