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
use App\Entity\User;
use App\Form\UserType;

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
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        $error = $session->get(Security::AUTHENTICATION_ERROR);
        $session->remove(Security::AUTHENTICATION_ERROR);

        $formBuilder = $this->createFormBuilder()
            ->add('_username', EmailType::class, [
                'required' => true,
                'label' => 'Email',
            ])
            ->add('_password', PasswordType::class, [
                'required' => true,
                'label' => 'Password'
            ])
            ->add('_remember', CheckboxType::class, [
                'required' => false,
                'label' => 'Remember me'
            ])
            ->add('submit', SubmitType::class, array('label' => 'Sign In'));
        $form = $formBuilder->getForm();

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
    public function registrationAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return $this->redirectToRoute('registration_success');
        }

        return [
            'form' => $form->createView(),
        ];
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
        throw new \Symfony\Component\Intl\Exception\NotImplementedException('Not implemented yet');
        $formBuilder = $this->createFormBuilder()
            ->add('email', EmailType::class, [
                'required' => true,
            ])
            ->add('submit', SubmitType::class, array('label' => 'Send confirmation Url'));
        $form = $formBuilder->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->container->get('school.user')->getByEmail($form->get('email')->getData());
            if ($user) {
                $this->container->get('school.user')->createConfirmationKey($user);
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', 'Confirmation link has been sent to requested email')
                ;
            }
            else {
                $request->getSession()
                    ->getFlashBag()
                    ->add('danger', 'Email not found')
                ;
            }
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
