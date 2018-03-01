<?php

namespace App\Controller;

use App\Entity\WhoisCheck;
use App\Form\WhoisType;
use App\Services\WhoisChecker;
use App\Utils\Validator;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Noxlogic\RateLimitBundle\Annotation\RateLimit;

/**
 * Class WhoisController
 * @package App\Controller
 * @RateLimit(limit=10, period=60)
 */
class WhoisController extends Controller
{
    /**
     * @Route("/whois", name="whois_checker_index")
     */
    public function index(Request $request, ValidatorInterface $validator): Response
    {
        $whoisCheck = new WhoisCheck();
        $form = $this->createForm(WhoisType::class, $whoisCheck, ['action' => $this->generateUrl('whois_checker_index')]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if($form->isValid()){
                $data = $form->getData();
                return $this->redirectToRoute('whois_checker_domain', ['domain' => $data->getDomain() ]);
            } else {
                $errors = $validator->validate($whoisCheck);
                if (count($errors) > 0) {
                    foreach ($errors as $error){
                        $this->addFlash('danger', $error->getMessage());
                    }
                }
            }
        }

        return $this->render('whois/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/whois/{domain}", name="whois_checker_domain")
     */
    public function domain($domain, Request $request, WhoisChecker $whoisChecker, ValidatorInterface $validator): Response
    {
        $domain = mb_strtolower($domain);
        $whoisCheck = new WhoisCheck();
        $whoisCheck->setDomain($domain);
        $errors = $validator->validate($whoisCheck);
        if (count($errors) > 0) {
            foreach ($errors as $error){
                $this->addFlash('danger', $error->getMessage());
            }
            return $this->redirectToRoute('whois_checker_index');
        }
        $form = $this->createForm(WhoisType::class, $whoisCheck, ['action' => $this->generateUrl('whois_checker_index')]);
        $result = false;
        try {
            $result = $whoisChecker->check($domain)->rawdata;
        } catch (Exception $e){

        }
        return $this->render('whois/domain.html.twig', [
                'domain' => $whoisCheck->getDomain(),
                'form' => $form->createView(),
                'result' => $result
            ]);
    }
}
