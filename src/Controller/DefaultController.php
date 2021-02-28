<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use App\Entity\User;
use App\Entity\Location;
use App\Entity\Exam;
use App\Entity\ExamAnswer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;


class DefaultController extends Controller
{
    private static $rooms = [
        [
            'name' => 'ВЦ #23',
            'latitude' => 50.001048,
            'longitude' => 36.251733,
        ],
        [
            'name' => 'У1 #306',
            'latitude' => 49.998513,
            'longitude' => 36.251639,
        ],
    ];

    /**
     * @Route("/", name="homepage")
     * @Template()
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Request $request)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $lesson = $this->getDoctrine()->getRepository('App:Lesson')
            ->findLessonFor($user);
        
        if (!$lesson) {
            return $this->redirectToRoute('no_lesson');
        }

        //if ($lesson->getUsers()->contains($user)) {
            //return $this->redirectToRoute('exam');
        //}

        //$lesson = $this->getDoctrine()->getManager()->getRepository('App:Lesson')->find(13);
        return [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
            'lesson' => $lesson,
        ];
    }

    /**
     * @Route("/no-lesson/", name="no_lesson")
     * @Template()
     */
    public function noLessonAction(Request $request)
    {
        return [];
    }

    /**
     * @Route("/exam/", name="exam")
     * @Template()
     */
    public function examAction(Request $request, \App\Service\ExamService $examService)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        $exam = $this->getDoctrine()->getRepository('App:Exam')
            ->findNotFinishedFor($user);

        if (!$exam) {
            $exam = new Exam;
            $exam->setUser($user);
            $exam->setDateStart(new \DateTime());
            $questions = $this->getDoctrine()->getRepository('App:Question')
                ->getRandomQuestions(30);
            foreach ($questions as $q) {
                $exam->addQuestion($q);
            }
            $this->getDoctrine()->getManager()->persist($exam);
            $this->getDoctrine()->getManager()->flush();
        }

        $question = $examService->getUnansweredQuestion($exam);

        if ($request->query->get('answer',null)!=null) {
            $answer = new ExamAnswer;
            $answer->setExam($exam);
            $answer->setQuestion($question);
            $answer->setAnswers($request->query->get('answer'));
            $this->getDoctrine()->getManager()->persist($answer);
            $this->getDoctrine()->getManager()->flush();

            $exam->setUpdatedAt(new \DateTime('now'));
            $exam->setResult($examService->getResult($exam));
            $this->getDoctrine()->getManager()->persist($exam);
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('exam');
        }

        if (!$question) {
            $exam->setResult($examService->getResult($exam));
            $exam->setDateEnd(new \DateTime());
            $this->getDoctrine()->getManager()->persist($exam);
            $this->getDoctrine()->getManager()->flush();
            return [
                'exam' => $exam,
                'time' => (new \DateTime('now'))->diff($exam->getDateStart())->format("%H:%I:%S"),
            ];
        }

        $answers = $question->getAnswers()->toArray();
        shuffle($answers);
        return [
            'num' => sprintf("%d из %d", $exam->getAnswers()->count()+1, $exam->getQuestions()->count()),
            'question' => $question,
            'answers' => $answers,
            'time' => (new \DateTime('now'))->diff($exam->getDateStart())->format("%H:%I:%S"),
        ];
    }

    /**
     * @Route("/location/", name="location")
     * @Template()
     */
    public function locationAction(Request $request, LoggerInterface $logger)
    {
        $user = $this->get('security.token_storage')->getToken()->getUser();

        //$lesson = $this->getDoctrine()->getRepository('App:Lesson')
            //->findLessonFor($user);
        //$room = $lesson->getRoom();
        $room = $this->getDoctrine()->getManager()->getRepository('App:Room')->find(4);

        $message = sprintf("User: %d, IP: %s, Latitude: %f, Longitude: %f, UserAgent: [%s]", 
            $user->getId(), 
            $request->server->get('REMOTE_ADDR'), 
            $request->query->get('lat'), 
            $request->query->get('long'),
            $request->server->get('HTTP_USER_AGENT')
        );
        $logger->critical($message);

        $location = new Location();
        $location->setLatitude($request->query->get('lat'));
        $location->setLongitude($request->query->get('long'));
        $location->setUserAgent($request->server->get('HTTP_USER_AGENT'));
        $location->setUser($user);
        $em = $this->getDoctrine()->getManager();
        $em->persist($location);
        $em->flush();

        $message = '';
        $lt_1 = $room->getLatitude();
        $lg_1 = $room->getLongitude();
        $lt_2 = $location->getLatitude();
        $lg_2 = $location->getLongitude();
        $distance = (int)round(111.2*acos(sin($lt_1)*sin($lt_2) + cos($lt_1)*cos($lt_2) * cos($lg_1-$lg_2))*1000);
        
        $message = sprintf("User: %d, Lesson: %d, Room: %d, Distance: %d", 
            $user->getId(), 
            0,//$lesson->getId(), 
            $room->getId(), 
            $distance
        );
        $logger->critical($message);

        //if ($distance>300) {
            //$message = 'Похоже вы находитесь не в аудитории.<br/> Если это не так, то попробуйте чуть <a href="'.($this->get('router')->generate('homepage')).'">позже</a> или обратитесь к преподавателю';
        //}
        //else {
            //try {
                //$lesson->addUser($user);
                //$em->persist($lesson);
                //$em->flush();
            //} catch (\Exception $e) {
            //}
            
            $message = 'Поздравляем!<br/> Вы находитесь в аудитории, поэтому не шумите и внимательно слушайте преподавателя.';
        //}

        return [
            'message' => $message,
        ];
    }

    /**
     * @Route("/error/", name="error")
     * @Template()
     */
    public function errorAction(Request $request)
    {
        $message = sprintf("IP: %s, ERROR", $request->server->get('REMOTE_ADDR'));
        $this->container->get('logger')->info($message);

        return [];
    }
}
