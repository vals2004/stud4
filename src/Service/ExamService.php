<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Exam;
use App\Entity\Question;
use App\Entity\ExamAnswer;

class ExamService
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function getUnansweredQuestion(Exam $exam)
    {
        $questions = new \Doctrine\Common\Collections\ArrayCollection($exam->getQuestions()->toArray());
        $answers = new \Doctrine\Common\Collections\ArrayCollection($exam->getAnswers()->toArray());
        foreach ($answers as $a) {
            $questions->removeElement($a->getQuestion());
        }
        return $questions->first();
    }

    public function getResult(Exam $exam)
    {
        $questions = new \Doctrine\Common\Collections\ArrayCollection($exam->getQuestions()->toArray());
        $answers = new \Doctrine\Common\Collections\ArrayCollection($exam->getAnswers()->toArray());
        $result = 0;
//        if ($questions->count()==$answers->count()) {
        foreach ($answers as $a) {
            $result += $this->isCorrectAnswer($a) ? 1 : 0;
        }
//        }

        return $result;
    }

    public function isCorrectAnswer(ExamAnswer $answer)
    {
        $result = false;
        $base = $answer->getQuestion()->getCorrectAnswers()->toArray();
        $real = $answer->getAnswers();
        if (count($base)==count($real)) {
            foreach($base as $b) {
                if (in_array($b->getId(), $real)) {
                    $result = true;
                }
                else {
                    $result = false;
                }
            }
        }
        return $result;
    }
}
