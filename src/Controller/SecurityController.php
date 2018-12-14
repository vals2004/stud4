<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Psr\Log\LoggerInterface;
use App\Entity\User;
use App\Form\UserType;
use App\Form\LoginType;
use App\Form\ForgotType;
use App\Form\RestoreType;

/**
 * Class SecurityController
 * @package App\Controller
 */
class SecurityController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @Template()
     */
    public function loginAction(Request $request, \SunCat\MobileDetectBundle\DeviceDetector\MobileDetector $mobileDetector, \Psr\Log\LoggerInterface $logger)
    {
        $logger->critical(serialize($mobileDetector));

        $session = $request->getSession();

        // get the login error if there is one
        $error = $session->get(Security::AUTHENTICATION_ERROR);
        $session->remove(Security::AUTHENTICATION_ERROR);

        $form = $this->createForm(LoginType::class, null);

        return [
            'last_username' => $session->get(Security::LAST_USERNAME),
            'error' => $error,
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/login_check", name="login_check")
     */
    public function loginCheckAction()
    {
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logoutAction()
    {
    }

    /**
     * @Route("/registration", name="registration")
     * @Template()
     */
    public function registrationAction(Request $request, \SunCat\MobileDetectBundle\DeviceDetector\MobileDetector $mobileDetector, \Psr\Log\LoggerInterface $logger)
    {
        $logger->critical(serialize($mobileDetector));

        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setConfirmationCode(md5(tempnam('/tmp', 'tmp').time()));
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            $messageBody = $this->container->get('twig')->render('email/confirm_registration.html.twig', [
                "first_name" => $user->getFirstName(),
                "last_name" => $user->getLastName(),
                "code" => $user->getConfirmationCode()
            ]);

            $message = (new \Swift_Message())
                ->setSubject('Student confirmation')
                ->setFrom('ann.zavolodko@gmail.com')
                ->setTo($user->getEmail())
                ->setBody($messageBody, 'text/html');

            if (!$this->container->get('mailer')->send($message)) {
                throw new \Exeption("Can't send email", Response::HTTP_INTERNAL_SERVER_ERROR);
            }
            
            return $this->redirectToRoute('login');
        }

        return [
            'form' => $form->createView(),
        ];
    }

    /**
     * @Route("/confirm/{code}", name="confirm_registration")
     * @Template()
     */
    public function confirmRegistrationAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->findOneBy(['confirmationCode'=>$code]);
        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }
        $user->setConfirmationCode(null);
        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken($user, null, 'main', $user->getRoles());
        $this->get('security.token_storage')->setToken($token);            
        $event = new \Symfony\Component\Security\Http\Event\InteractiveLoginEvent($request, $token);
        $this->get("event_dispatcher")->dispatch("security.interactive_login", $event);

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/registration-success", name="registration_success")
     * @Template()
     */
    public function registrationSuccessAction(Request $request)
    {
        return [
        ];
    }

    /**
     * @Route("/forgot-password", name="forgot_password")
     * @Template()
     */
    public function forgotPasswordAction(Request $request)
    {
        $form = $this->createForm(ForgotType::class);
        $form->handleRequest($request);
        $error = null;

        if ($form->isSubmitted()) {
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('App:User')->findOneByEmailOrPhone($form->get('email')->getData(), $form->get('phone')->getData());
            if ($user) {
                $user->setForgotCode(md5(tempnam('/tmp', 'tmp').time()));
                $em->persist($user);
                $em->flush();

                $messageBody = $this->container->get('twig')->render('email/reset_password.html.twig', [
                    "first_name" => $user->getFirstName(),
                    "last_name" => $user->getLastName(),
                    "code" => $user->getForgotCode()
                ]);

                $message = (new \Swift_Message())
                    ->setSubject('Student reset password')
                    ->setFrom('ann.zavolodko@gmail.com')
                    ->setTo($user->getEmail())
                    ->setBody($messageBody, 'text/html');

                if (!$this->container->get('mailer')->send($message)) {
                    throw new \Exeption("Can't send email", Response::HTTP_INTERNAL_SERVER_ERROR);
                }

                return $this->redirectToRoute('login');
            }
            else {
                $error = 'User not found';
            }
        }

        return [
            'form' => $form->createView(),
            'error' => $error,
        ];
    }

    /**
     * @Route("/forgot/{code}", name="restore_password")
     * @Template()
     */
    public function restorePasswordAction(Request $request, $code)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('App:User')->findOneBy(['forgotCode'=>$code]);
        if (!$user) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        $form = $this->createForm(RestoreType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $user->setPassword($form->get('password')->getData());
            $user->setForgotCode(null);
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('login');
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
