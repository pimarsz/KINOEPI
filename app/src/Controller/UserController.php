<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-15
 * Time: 18:08
 */
namespace Controller;

use Repository\UserRepository;
use Repository\RoleRepository;
use Silex\Application;
use Form\UserType;
use Form\UserEditType;
use Symfony\Component\HttpFoundation\Request;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;


/**
 * Class UserController.
 */
class UserController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match('/signin', [$this, 'addAction'])
            ->method('POST|GET')
            ->bind('user_add');
        $controller->match('/show', [$this, 'showAction'])
            ->method('POST|GET')
            ->bind('user_show');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('user_edit');
        $controller->match('/{id}/delete', [$this, 'deleteAction'])
            ->method('GET|POST')
            ->assert('id', '[0-9]\d*')
            ->bind('user_delete');

        return $controller;
    }



    /**
     * Add action.
     *
     * @param \Silex\Application                        $app     Silex application
     * @param \Symfony\Component\HttpFoundation\Request $request HTTP Request
     *
     * @return \Symfony\Component\HttpFoundation\Response HTTP Response
     */
    public function addAction(Application $app, Request $request)
    {
        $user = [];

        $form = $app['form.factory']->createBuilder(UserType::class, $user)->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $usersRepository = new UserRepository($app['db']);

            $password = $app['security.encoder.bcrypt']->encodePassword($form['password']->getData(), '');


            $fromform = $form->getData();

            $fromform['password'] = $password;
            $usersRepository->save($fromform);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.new_account_added',
                ]
            );

            return $app->redirect($app['url_generator']->generate('auth_login'), 301);
        }

        return $app['twig']->render(
            'user/add.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );

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
        $UserRepository = new UserRepository($app['db']);

        return $app['twig']->render(
            'user/show.html.twig',
            ['users' => $UserRepository->findAll()]
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
        $UsersRepository = new UserRepository($app['db']);
        $user = $UsersRepository->findOneById($id);

        if (!$user) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_show'));
        }

        $form = $app['form.factory']->createBuilder(UserEditType::class, $user)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $UsersRepository->save($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('user_show'), 301);
        }

        return $app['twig']->render(
            'user/edit.html.twig',
            [
                'user' => $user,
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
        $UserRepository = new UserRepository($app['db']);
        $user = $UserRepository->findOneById($id);

        if (!$user) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('screening_show'));
        }

        $form = $app['form.factory']->createBuilder(FormType::class, $user)->add('id', HiddenType::class)->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $UserRepository->delete($form->getData());

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_deleted',
                ]
            );

            return $app->redirect(
                $app['url_generator']->generate('user_show'),
                301
            );
        }
        return $app['twig']->render(
            'user/delete.html.twig',
            [
                'user' => $user,
                'form' => $form->createView(),
            ]
        );
    }


}