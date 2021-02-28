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
use App\Form\MassMailType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;


class MailController extends Controller
{

    /**
     * @Route("/mail", name="mail")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $form = $this->createForm(MassMailType::class, null);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $recipientsList = [
                'ok' => [],
                'error' => [],
            ];
            $subject = $form->getData()['subject'];
            $message = $form->getData()['message'];
            $recipients = explode("\r\n", $form->getData()['recipients']);

            array_walk($recipients, function (&$e) {
                $e = explode('___', $e);
            });
            set_time_limit(0);

            $attachUploaded = false;
            $attachPath = tempnam('/tmp/', date('His'));
            if (file_exists($attachPath)) { unlink($attachPath); }
            mkdir($attachPath);
            if ($attach = $form->getData()['attachments']) {
                $attachUploaded = true;
                $zip = new \ZipArchive;
                $zipName = $attach->getRealPath();
                if ($zip->open($zipName) === TRUE) {
                    $zip->extractTo($attachPath);
                    $zip->close();                
                }
                else {
                    $attachUploaded = false;
                    dump('Error archive');die;
                }
            }

            foreach ($recipients as $r) {
                //$messageBody = preg_replace('/{name}/', $r[1], $message);
                //$certificateNumber = explode('_', $r[2])[0];
                $messageBody = $message;

                try {
                    $messageObj = (new \Swift_Message())
                        //->setSubject(preg_replace('/{num}/', $certificateNumber, $subject))
                        ->setSubject($subject)
                        ->setFrom('dComFra.Kharkiv@gmail.com')
                        ->setTo($r[0])
                        //->setBcc('ntu.khpi.si@gmail.com')
                        ->setBody($messageBody, 'text/html');

                    if ($attachUploaded) {
                        $attachName = $attachPath . '/ED2020_Cert/' . $r[1];//. '.pdf';
                        if (!file_exists($attachName)) {
                            $recipientsList['error'][$r[0]] = 'attach not found'; 
                            continue;
                            ////throw new \Exception('file not found');
                        }
                        $messageObj
                            ->attach(\Swift_Attachment::fromPath($attachName));
                    }

                    if (!$this->container->get('mailer')->send($messageObj)) {
                        $recipientsList['error'][$r[0]] = 'smtp error'; 
                        //continue;
                        throw new \Exeption("Can't send email", Response::HTTP_INTERNAL_SERVER_ERROR);
                    }
                    else {
                        $recipientsList['ok'][$r[0]] = 'queue'; 
                        //dump(sprintf("%s - sent", $r));
                    }
                } catch (\Exception $e) {
                    $recipientsList[$r[0]] = 'unknown error'; 
                    dump($e);
                    //dump(sprintf("%s - error", $r));
                }
            }

            dump($recipientsList);
        }

        return [
            'form' => $form->createView(),
        ];
    }
}
