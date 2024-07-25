<?php declare(strict_types=1);

namespace MelasistemaMultiSearch\Storefront\Controller;

use Exception;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Log\Package;
use Shopware\Core\Framework\Routing\Exception\MissingRequestParameterException;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\StorefrontController;
use Shopware\Storefront\Page\Search\SearchPageLoader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @internal
 * Do not use direct or indirect repository calls in a controller. Always use a store-api route to get or put data
 */
#[Route(defaults: ['_routeScope' => ['storefront']])]
#[Package('system-settings')]
class MultiSearchController extends StorefrontController
{

    /**
     * @internal
     */
    public function __construct(
        private readonly SearchPageLoader $searchPageLoader,
        private readonly LoggerInterface $logger,
    ) {
    }

    /**
     * @param $request
     * @return mixed
     */
    private function getSession($request): mixed
    {
        return $request->getSession();
    }


    /**
     * @param SalesChannelContext $context
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/multi-search', name: 'frontend.multi.search.page', defaults: ['_httpCache' => true], methods: ['GET'])]
    public function multiSearch(SalesChannelContext $context, Request $request): Response
    {
        try {
            $searchTerms = $request->query->get('search');

            if (!$searchTerms) {
                throw new MissingRequestParameterException('search');
            }

            $session = $this->getSession($request); // Use the private method here
            $storedKeywordsString = $session->get('multiSearchKeywords', ''); // Get the string value

            $storedKeywords = explode('|', $storedKeywordsString); // Split into an array using the pipe delimiter

            $tabs = [];
            foreach ($storedKeywords as $searchTerm) {
                $tabs[] = [
                    'term' => $searchTerm,
                    // Generate URL for each term
                    'url' => $this->generateUrl('frontend.multi.search.page', ['search' => $searchTerm]), //
                ];
            }

            $page = $this->searchPageLoader->load($request, $context);

            return $this->renderStorefront('@MelasistemaMultiSearch/storefront/page/content/multiSearch/index.html.twig', [
                'page' => $page,
                'tabs' => $tabs,  // Pass the tabs data to the template
            ]);
        } catch (MissingRequestParameterException) {
            return $this->forwardToRoute('frontend.home.page');
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    #[Route(path: '/multi-search-submit', name: 'frontend.multi.search.submit', defaults: ['_httpCache' => true], methods: ['POST'])]
    public function multiSearchSubmit(Request $request): Response
    {
        try {

            $keywords = $request->request->get('allKeywords'); // Access keywords from POST data

            $session = $this->getSession($request);
            $session->set('multiSearchKeywords', $keywords); // Store keywords in session

            // Redirect to the multi-search page (GET request) with the first keyword
            $firstKeyword = explode('|', $keywords)[0];
            return $this->redirectToRoute('frontend.multi.search.page', ['search' => $firstKeyword]);

        } catch (Exception $e) {
            // Handle any exceptions during processing
            return new JsonResponse(['message' => 'Error processing multi-search request: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param Request $request
     * @return array|JsonResponse
     */
    #[Route(path: '/multi-search-get-tabs', name: 'frontend.multi.search.tabs', defaults: ['XmlHttpRequest' => true, '_httpCache' => false], methods: ['GET'])]
    public function multiSearchTabs(Request $request): JsonResponse|array
    {
        try {
            $session = $this->getSession($request);
            $storedKeywordsString = $session->get('multiSearchKeywords', ''); // Get the string value

            $storedKeywords = explode('|', $storedKeywordsString); // Split into an array using the pipe delimiter

            $tabs = [];
            foreach ($storedKeywords as $searchTerm) {
                $tabs[] = [
                    'term' => $searchTerm,
                    // Generate URL for each term
                    'url' => $this->generateUrl('frontend.multi.search.page', ['search' => $searchTerm]), //
                ];
            }
            return new JsonResponse(['tabs' => $tabs], Response::HTTP_OK);

        } catch (Exception $e) {
            // Handle any exceptions during processing
            return new JsonResponse(['message' => 'Error processing multi-search request: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}
