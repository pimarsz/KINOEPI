<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-02
 * Time: 13:00
 */
/**
 * Screening controller.
 */
namespace Controller;


use Form\ScreeningType;
use Repository\AuditoriumRepository;
use Repository\MovieListRepository;
use Repository\ScreeningRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Class Screening Controller.
 */
class ScreeningController implements ControllerProviderInterface
{

    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match('/show', [$this, 'showAction'])->bind('screening_show');
        $controller->match('/add', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('screening_add');
        $controller->get('/{id}', [$this, 'viewAction'])->bind('screening_index');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('GET|POST')
            ->assert('id', '[0-9]\d*')
            ->bind('screening_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[0-9]\d*')
            ->bind('screening_delete');


        return $controller;
    }

    /**
     * show action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function showAction(Application $app)
    {
        $ScreeningRepository = new ScreeningRepository($app['db']);

        return $app['twig']->render(
            'screening/show.html.twig',
            ['screenings' => $ScreeningRepository->findAllScreenings()]
        );
    }

    /**
     * view action.
     *
     * @param \Silex\Application $app Silex application
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function viewAction(Application $app, $id)
    {
        $ScreeningRepository = new ScreeningRepository($app['db']);
        $screening = $ScreeningRepository->findOneById($id);

        return $app['twig']->render(
            'screening/view.html.twig',
            ['screening' => $screening]
        );
    }


    public function addAction(Application $app, Request $request)
    {
        $screening = [];

        $form = $app['form.factory']->createBuilder(
            ScreeningType::class,
            $screening,
            ['auditorium_repository' => new AuditoriumRepository($app['db']),
                'movie_repository' => new MovieListRepository($app['db'])]
        )->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $ScreeningRepository = new ScreeningRepository($app['db']);
            $ScreeningRepository->save($form->getData());

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
            'screening/add.html.twig',
            [
                'screening' => $screening,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Edit action.
     *
     * @param \Silex\Application $app Silex application
     * @param int $id Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function editAction(Application $app, $id, Request $request)
    {
        $ScreeningRepository = new ScreeningRepository($app['db']);
        $screening = $ScreeningRepository->findOneScreeningById($id);

        if (!$screening) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('screening_show'));
        }
//
//        unset($screening['name']);
//        unset($screening['movie_id']);
//        unset($screening['movie_title']);

        $form = $app['form.factory']->createBuilder(
            ScreeningType::class,
            $screening,
            ['auditorium_repository' => new AuditoriumRepository($app['db']),
                'movie_repository' => new MovieListRepository($app['db'])]
        )->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ScreeningRepository->save($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('screening_show'), 301);
        }

        return $app['twig']->render(
            'screening/edit.html.twig',
            [
                'screening' => $screening,
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * Delete action.
     *
     * @param \Silex\Application $app Silex application
     * @param int $id Record id
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function deleteAction(Application $app, $id, Request $request)
    {
        $ScreeningRepository = new ScreeningRepository($app['db']);
        $screening = $ScreeningRepository->findOneScreeningById($id);

        if (!$screening) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('screening_show'));
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $screening)->add('screening_id', HiddenType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ScreeningRepository->delete($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('screening_show'),
                301
            );
        }
        return $app['twig']->render(
            'screening/delete.html.twig',
            [
                'screening' => $screening,
                'form' => $form->createView(),
            ]
        );
    }
}