<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ApiController extends AbstractController
{

    private $em;
    private $parameterBag;
    private $userRepository;

    public function __construct(EntityManagerInterface $em, ParameterBagInterface $parameterBag, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->parameterBag = $parameterBag;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/user_auth", name="api_user_auth")
     */
    public function index(Request $request): JsonResponse
    {
        // Получение параметров запроса
        $parameters = $request->query->all();

        // Удаление параметра 'sig' и сортировка по ключу
        unset($parameters['sig']);
        ksort($parameters);

        // Формирование строки из параметров и добавление секретного ключа
//        $secretKey = $this->parameterBag->get('APP_SECRET');
        $secretKey = $_SERVER['APP_SECRET'];
        $str = http_build_query($parameters) . $secretKey;

        // Вычисление хеша
        $hashedStr = mb_strtolower(md5($str), 'UTF-8');

        // Проверка совпадения хеша с 'sig'
        if ($hashedStr === $request->query->get('sig')) {
            // Пользователь прошел проверку подписи

            $userId = $request->query->get('id');
            $firstName = $request->query->get('first_name');
            $lastName = $request->query->get('last_name');
            $city = $request->query->get('city');
            $country = $request->query->get('country');

            $user = $this->userRepository->find($userId);

            if (!$user) {
                // Пользователь не существует, создайте его
                $user = new User();
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setCity($city);
                $user->setCountry($country);
                // Установите остальные поля
            } else {
                // Пользователь существует, обновите данные
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setCity($city);
                $user->setCountry($country);
                // Обновите остальные поля
            }

            // Сохраните пользователя в базе данных
            $this->em->persist($user);
            $this->em->flush();

            // Верните успешный ответ
            $response = [
                'access_token' => $request->query->get('access_token'),
                'user_info' => [
                    'id' => $user->getId(),
                    'first_name' => $user->getFirstName(),
                    'last_name' => $user->getLastName(),
                    'city' => $user->getCity(),
                    'country' => $user->getCountry(),
                ],
                'error' => '',
                'error_key' => '',
            ];

            return $this->json($response);
        } else {
            // Подпись не совпадает, верните ошибку
            $errorResponse = [
                'error' => 'Ошибка авторизации в приложении',
                'error_key' => 'signature error',


                'test1' => $hashedStr,
                'test2' => $request->query->get('sig'),
            ];

            return $this->json($errorResponse);
        }
    }
}




