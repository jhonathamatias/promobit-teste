<?php 

namespace App\Domain;

use App\Domain\Exceptions\UserNotFoundException;
use App\Entity\Users as UsersEntity;
use App\Repository\UsersRepository;
use App\Services\Auth;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Users
{
    public function __construct(
        protected EntityManagerInterface $entityManager,
        protected ValidatorInterface $validator,
        protected UsersRepository $usersRepository
    ){}

    public function create(object $data): array
    {
        $users = new UsersEntity;

        $users->setName($data->name)
              ->setEmail($data->email)
              ->setPassword($data->password)
              ->setCreatedAt(new DateTimeImmutable());

        $errors = $this->validator->validate($users);

        if (count($errors) > 0) {
            throw new \App\Domain\Exceptions\ValidationException("Validation error: " . (string)$errors);
        }

        $users->setPassword(password_hash($data->password, PASSWORD_BCRYPT));

        $this->entityManager->persist($users);
        $this->entityManager->flush();

        if (!$users->getId()) {
            throw new \App\Domain\Exceptions\UserCreateException('User not created.');
        }

        return [
            'success' => [
                'message' => 'User created.'
            ]
        ];
    }

    public function login(string $email, string $password): string
    {
        $user = $this->usersRepository->findOneBy([
            'email' => $email
        ]);

        if (!$user) {
            throw new \App\Domain\Exceptions\UserNotFoundException('User not found');
        }

        if ($user && password_verify($password, $user->getPassword()) === false) {
            throw new \App\Domain\Exceptions\LoginIncorrectException('Email or password incorrect.');
        }

        $date = new \DateTime('+3 hour');

        return Auth::createToken([
            'email' => $user->getEmail(),
            'user_id'    => $user->getId(),
            'exp'   => $date->getTimestamp()
        ]);
    }

    public function getAll(): array
    {
        $users = $this->usersRepository->findAll();
        $newUsers = [];

        foreach($users as $user) {
            $newUsers[] = [
                'id'      => $user->getId(),
                'name'      => $user->getName(),
                'email'     => $user->getEmail(),
                'createdAt' => $user->getCreatedAt()
            ];
        }

        return $newUsers;
    }

    public function update(int $id, object $data): array
    {
        $userEntity = $this->usersRepository->find($id);

        if (!$userEntity) {
            throw new UserNotFoundException('User not found.');
        }

        foreach ($data as $key => $value) {
            $set = 'set' . ucfirst($key);

            $userEntity->$set($value);
        }

        $errors = $this->validator->validate($userEntity);

        if (count($errors) > 0) {
            throw new \App\Domain\Exceptions\ValidationException("Validation error: " . (string)$errors);
        }

        if (isset($data->password)) {
            $userEntity->setPassword(password_hash($data->password, PASSWORD_BCRYPT));
        }

        $this->entityManager->persist($userEntity);
        $this->entityManager->flush();

        return [
            'Success' => [
                'message' => 'User updated.'
            ]
        ];
    }

    public function delete(int $id): array
    {
        $userEntity = $this->usersRepository->find($id);

        if (!$userEntity) {
            throw new UserNotFoundException('User not found.');
        }

        $this->entityManager->remove($userEntity);
        $this->entityManager->flush();

        return [
            'Success' => [
                'message' => 'User deleted.'
            ]
        ];
    }
}