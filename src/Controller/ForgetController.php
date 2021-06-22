<?php

namespace App\Controller;

use App\Domain\Forget as ForgetDomain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ForgetController extends AbstractController
{
    public function __construct(
        protected ForgetDomain $forgetDomain
    ){}

    #[Route('/forget', name: 'forget', methods: ['POST'])]
    public function sendEmail(Request $request): Response
    {
        try {
            $data = (object)$request->toArray();

            $result = $this->forgetDomain->sendEmail($data->email);

            return $this->json($result);
        } catch (\Exception $e) {
            return $this->json([
                'message' => $e->getMessage()
            ]);
        }
    }
}
