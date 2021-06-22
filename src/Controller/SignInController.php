<?php

namespace App\Controller;

use App\Domain\Users as UsersDomain;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SignInController extends AbstractController
{
    public function __construct(
        protected UsersDomain $usersDomain
    ){}

    #[Route('/signin', name: 'signin', methods: ['POST'])]
    public function signIn(Request $request): Response
    {
        try {
            $data = (object)$request->toArray();
    
            $token = $this->usersDomain->login($data->email, $data->password);

            return $this->json([
                'token' => $token
            ], 200);

        } catch(\App\Domain\Exceptions\LoginIncorrectException $e) {
            
            return $this->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 403);

        } catch(\App\Domain\Exceptions\UserNotFoundException $e) {
           
            return $this->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 404);

        } catch(\Exception $e) {
            
            return $this->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    #[Route('/signup', name: 'signup', methods: ['POST'])]
    public function signUp(Request $request)
    {
        try {

            $data = (object)$request->toArray();

            $result = $this->usersDomain->create($data);

            return $this->json($result, 201);

        } catch(\Exception $e) {
            return $this->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }
}
