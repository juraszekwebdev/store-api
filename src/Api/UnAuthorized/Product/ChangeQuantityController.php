<?php


namespace App\Api\UnAuthorized\Product;


use App\Entity\Product;
use App\Entity\User;
use App\JWT\JWTManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class ChangeQuantityController extends AbstractController
{
    /**
     * @var JWTManager $jwtManager
     */
    private $jwtManager;

    public function __construct(JWTManager $jwtManager)
    {
        $this->jwtManager = $jwtManager;
    }

    /**
     * @Route("/product/changeQuantity", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function changeQuantity(Request $request)
    {
        $requestedContent = json_decode($request->getContent(), true);
        $decodedToken = $this->jwtManager->decodeToken($request);

        $validate = $this->validate($requestedContent, $decodedToken);
        if($validate['status'] === 'error') {
            return new JsonResponse(
                [
                    'data' => null,
                    'status' => $validate['status'],
                    'statusMsg' => $validate['statusMsg'],
                ],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        $object = $this->getDoctrine()->getRepository(Product::class)->find($requestedContent['id']);
        $object->setQuantityLeft($requestedContent['quantity']);
        $em = $this->getDoctrine()->getManager();
        $em->persist($object);
        $em->flush();

        return new JsonResponse(
            [
                'data' => null,
                'status' => 'ok',
                'statusMsg' => '',
            ],
            JsonResponse::HTTP_OK
        );
    }

    private function validate($payload, $decodedToken)
    {
        return [
            'data' => null,
            'status' => 'ok',
            'statusMsg' => '',
        ];
    }
}