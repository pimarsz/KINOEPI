<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-05-31
 * Time: 12:34
 */

/**
 * MovieList controller.
 */
namespace Controller;


use Form\MovieType;
use Repository\MovieListRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Class MoviesController.
 */
class MovieListController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->get('/', [$this, 'indexAction'])->bind('movieList_index');
        $controller->get('/{id}', [$this, 'viewAction'])
            ->assert('id', '[1-9]\d*')
            ->bind('movieList_view');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('movieList_add');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('movieList_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[1-9]\d*')
            ->bind('movieList_delete');

        return $controller;
    }

    /**
     * Index action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function indexAction(Application $app)
    {
        $moviesRepository = new MovieListRepository($app['db']);

        return $app['twig']->render(
            'movieList/index.html.twig',
            ['movies' => $moviesRepository->findAll()]
        );
    }

    /**
     * View action.
     *
     * @param \Silex\Application $app Silex application
     * @param string             $id  Element Id
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function viewAction(Application $app, $id)
    {
        $moviesRepository = new MovieListRepository($app['db']);

        return $app['twig']->render(
            'movieList/view.html.twig',
            ['movie' => $moviesRepository->findOneById($id)]
        );
    }

    public function addAction(Application $app, Request $request)
    {
        $movie = [];

        $form = $app['form.factory']->createBuilder(MovieType::class, $movie)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $MovieListRepository = new MovieListRepository($app['db']);
            $MovieListRepository->save($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('movieList_index'), 301);
        }

        return $app['twig']->render(
            'movieList/add.html.twig',
            [
                'movie' => $movie,
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
    public function editAction(Application $app, $id, Request $request)
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

    /**
     * Remove record.
     *
     * @param array $movie Movie
     *
     * @return boolean Result
     */
    public function delete($movie)
    {
        return $this->db->delete('movie', ['id' => $movie['movie_id']]);
    }

    /**
     * Delete action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param int                                       $id      Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function deleteAction(Application $app, $id, Request $request)
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

        $form = $app['form.factory']->createBuilder(FormType::class, $movie)->add('movie_id', HiddenType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $MovieListRepository->delete($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('movieList_index'),
                301
            );
        }

        return $app['twig']->render(
            'movieList/delete.html.twig',
            [
                'movie' => $movie,
                'form' => $form->createView(),
            ]
        );
    }
}