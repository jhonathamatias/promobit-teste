<?php

namespace App\Controller;

use App\Domain\Users as UsersDomain;
use App\Services\Auth;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class UsersController extends AbstractController implements TokenAuthenticatedController
{
    public function __construct(
        protected UsersDomain $usersDomain
    ){}

    #[Route('/users', name: 'get_users', methods: ['GET'])]
    public function getUsers(): Response
    {
        try {

            $result = $this->usersDomain->getAll();

            return $this->json($result, 200);

        } catch(\Exception $e) {
            return $this->json([
                'error' => [
                    'message' => $e->getMessage()
                ]
            ], 400);
        }
    }

    #[Route('/users', name: 'create_user', methods: ['POST'])]
    public function createUser(Request $request)
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

    #[Route('/users/{id}', name: "update_user", methods: ['PUT'])]
    public function updateUser(Request $request, int $id): Response
    {
        try {
            $data = (object)$request->toArray();

            $result = $this->usersDomain->update($id, $data);

            return $this->json($result, 200);
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

    #[Route('/users/{id}', name: 'delete_user', methods: ['DELETE'])]
    public function deleteUser(int $id): Response
    {
        try {
            $result = $this->usersDomain->delete($id);

            return $this->json($result, 200);
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

    #[Route('/users/forget/password', name: 'user_forget_password', methods: ['PUT'])]
    public function updatePassword(Request $request)
    {
        try {
            $data = (object)$request->toArray();
            
            $token = $request->attributes->get('token');

            $tokenDecode = Auth::verifyToken($token);

            $result = $this->usersDomain->update($tokenDecode->user_id, $data);

            return $this->json($result, 200);
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
}
