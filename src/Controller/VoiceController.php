<?php
namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class VoiceController extends Controller
{
    /**
     * @Route("/google", name="google")
     */
    public function googleAction(Request $request)
    {
	$route = $request->get('_route');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $request->getContent();

        $lambdaRequest = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        //$lambdaResponse = $lambdaRequest->post('http://10.8.1.43:8080/' . $route, [
        //$lambdaResponse = $lambdaRequest->post('http://10.8.5.56:8080/' . $route, [
        $lambdaResponse = $lambdaRequest->post('http://192.168.3.120:8080/' . $route, [
            'body' => $request->getContent(),
        ]);

        $response->setContent($this->jsonFilter($lambdaResponse->getBody()));

        return $response;        
    }

    /**
     * @Route("/alexa", name="alexa")
     */
    public function alexaAction(Request $request)
    {
	$route = $request->get('_route');

        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        $request->getContent();

        $lambdaRequest = new \GuzzleHttp\Client([
            'headers' => [ 'Content-Type' => 'application/json' ]
        ]);
        //$lambdaResponse = $lambdaRequest->post('http://10.8.1.43:8080/' . $route, [
        //$lambdaResponse = $lambdaRequest->post('http://10.8.5.56:8080/' . $route, [
        $lambdaResponse = $lambdaRequest->post('http://192.168.3.120:8080/' . $route, [
            'body' => $request->getContent(),
        ]);

        $response->setContent($this->jsonFilter($lambdaResponse->getBody()));

        return $response;        
    }


    private function jsonFilter($str){
        return str_replace("'}", '"}', str_replace("',", '",', str_replace("':", '":', str_replace(" '", ' "', str_replace('False', 'false', str_replace("{'", '{"', $str))))));
    }
}

