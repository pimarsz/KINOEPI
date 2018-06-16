<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-10
 * Time: 20:15
 */


/**
 * Order controller.
 */
namespace Controller;


use Form\OrderType;
use Controller\TransactionController;
use Repository\MovieListRepository;
use Repository\OrderRepository;
use Repository\ScreeningRepository;
use Repository\UserRepository;
use Provider\UserProvider;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Class MoviesController.
 */
class OrderController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        /*$controller->get('/', [$this, 'indexAction'])->bind('order_index');*/
        /*$controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('movieList_view');*/
        /*$controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('order_edit');*/
        $controller->match('/add/{id}', [$this, 'addAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('order_add');

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
   /* public function indexAction(Application $app)
    {
        $moviesRepository = new MovieListRepository($app['db']);

        return $app['twig']->render(
            'movieList/index.html.twig',
            ['movies' => $moviesRepository->findAll()]
        );
    }*/

    /**
     * View action.
     *
     * @param \Silex\Application $app Silex application
     * @param string             $id  Element Id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
   /* public function viewAction(Application $app, $id)
    {
        $moviesRepository = new MovieListRepository($app['db']);

        return $app['twig']->render(
            'movieList/view.html.twig',
            ['movie' => $moviesRepository->findOneById($id)]
        );
    }*/


    /**
     * @param Application $app
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Application $app, $id, Request $request)
    {
        $userRepository = new UserRepository($app['db']);

        $token = $app['security.token_storage']->getToken();
        if (null !== $token)
        {
            $user = $token->getUser();
        }

        $userId = ($userRepository->getIdByLogin($user));

        $OrderRepository = new OrderRepository($app['db']);
        $ScreeningRepository = new ScreeningRepository($app['db']);
        $screening = [];
        $screening['id'] = $id;
        $order = [];
        $order['id'] = $id;
//        dump($OrderRepository->CountSeats($id));
//        dump($OrderRepository->FindBookedSeats($id));
//        dump($OrderRepository->FindAvailableSeats($id));
        $order['order_repository'] = $OrderRepository;
        $screening['screening_repository'] = $ScreeningRepository;
        $form = $app['form.factory']->createBuilder(OrderType::class, $order)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){


            $OrderRepository->save($form->getData(), $userId['id']);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.finishyourorder',
                ]
            );

            return $app->redirect($app['url_generator']->generate('transaction_add', array('id' => $id)), 301);
        }

        return $app['twig']->render(
            'order/add.html.twig',
            [
                'screening_screening_id' => $id,
                'movie' => $order,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param int                                       $id      Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    /*public function editAction(Application $app, $id, Request $request)
    {
        $MovieListRepository = new MovieListRepository($app['db']);
        $movie = $MovieListRepository->findOneById($id);

        if (!$movie) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('movieList_index'));
        }

        $form = $app['form.factory']->createBuilder(MovieType::class, $movie)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $MovieListRepository->save($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('movieList_index'), 301);
        }

        return $app['twig']->render(
            'movieList/edit.html.twig',
            [
                'movie' => $movie,
                'form' => $form->createView(),
            ]
        );
    }
*/

}