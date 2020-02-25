<?php


namespace App\Controller;


use App\Service\MediumService;
use App\Service\StoreService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class MediumController extends AbstractController
{
    const GOOGLE_OAUTH_REFRESH_TOKEN = 'google_oauth_refresh_token';
    /**
     * @var MediumService
     */
    private $mediumService;
    /**
     * @var StoreService
     */
    private $storeService;

    public function __construct(
        MediumService $mediumService,
        StoreService $storeService
    ) {
        $this->mediumService = $mediumService;
        $this->storeService = $storeService;
    }

    public function storeGet(): Response {
        $pieces = [];
        $echo = [
            self::GOOGLE_OAUTH_REFRESH_TOKEN,
        ];
        foreach ($echo as $name) {
            $pieces[] = $name . ' = ' . $this->getStoreValue($name);
        }
        return new Response('<pre>' . implode("\n", $pieces) . '</pre>');
    }

    public function storeClear(): Response {
        $this->storeService->remove(self::GOOGLE_OAUTH_REFRESH_TOKEN, true);
        return new Response('Done - check DB', Response::HTTP_OK);
    }

    private function getStoreValue(string $name, $default = null, bool $throw = false) {
        return $this->storeService->getStoreValue($name, $default, $throw);
    }

    // Google

    public function googleOAuthFlowStart(): Response {
        return new RedirectResponse($this->mediumService->getGoogleOauthFlowStartUri([
            'store' => $this->mediumService->encrypt(self::GOOGLE_OAUTH_REFRESH_TOKEN)
        ]));
    }

    public function googleOAuthFlowAnswer(Request $request): Response {
        $token = $request->get('token');
        $storeName = $request->get('store');
        if (!$token && !$storeName) {
            throw new HttpException(400, 'No token or store returned');
        }
        if (!$token && !$storeName) {
            throw new HttpException(404, 'No token returned');
        }
        if (!$storeName) {
            throw new HttpException(420, 'No store returned');
        }
        $this->storeService->createOrUpdateStore(
            $this->mediumService->decrypt($storeName),
            $this->mediumService->decrypt($token),
            true
        );
        return new Response('Google oauth flow answered - check DB');
    }

    public function googleDriveFileList(Request $request): JsonResponse {
        $refreshToken = $this->getStoreValue(self::GOOGLE_OAUTH_REFRESH_TOKEN, null, true);
        $response = $this->mediumService->getDriveFileList($refreshToken);
        return new JsonResponse($response->getContent(false), $response->getStatusCode(), [], true);
    }

    public function googleDriveFileGet(Request $request): JsonResponse {
        $refreshToken = $this->getStoreValue(self::GOOGLE_OAUTH_REFRESH_TOKEN, null, true);
        $response = $this->mediumService->getDriveFile($refreshToken, '1ms_ufPzR2QW5L_8jO04mcSgRbFtl_kWA', 'application/pdf');
        return new JsonResponse($response->getContent(false), $response->getStatusCode(), [], true);
    }

    public function googleShareUriCapabilities(Request $request): JsonResponse {
        $uri = $request->get('uri') ? : 'https://docs.google.com/spreadsheets/d/1jcUtg_pGv7krZbjArAKyZgCN22WkIT_LZUtSJMibruY/edit?usp=sharing';
        $response = $this->mediumService->getGoogleShareUriCapabilities($uri);
        return new JsonResponse($response->getContent(false), $response->getStatusCode(), [], true);
    }
}