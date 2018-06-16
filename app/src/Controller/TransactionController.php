<?php
/**
 * Created by PhpStorm.
 * User: Małgorzata
 * Date: 2018-06-13
 * Time: 20:38
 */



/**
 * Transaction controller.
 */
namespace Controller;

use Form\OrderType;
use Form\TransactionEditType;
use Form\TransactionType;
use Repository\TransactionRepository;
use Repository\PaymentMethodRepository;
use Repository\PaymentStatusRepository;
use Repository\UserRepository;
use Repository\OrderRepository;
use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\DBAL\Connection;



/**
 * Class TransactionController.
 */
class TransactionController implements ControllerProviderInterface
{
    /**
     * {@inheritdoc}
     */
    public function connect(Application $app)
    {
        $controller = $app['controllers_factory'];
        $controller->match('/show', [$this, 'showAction'])->bind('transaction_show');
        $controller->match('/{id}/edit', [$this, 'editAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('transaction_edit');
        $controller->match('/add/{id}', [$this, 'addAction'])
            ->method('POST|GET')
            ->assert('id', '[1-9]\d*')
            ->bind('transaction_add');

        return $controller;
    }




    /**
     * @param Application $app
     * @param string $id
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function addAction(Application $app, $id, Request $request)
    {
        $userRepository = new UserRepository($app['db']);
        $TransactionRepository = new TransactionRepository($app['db']);
        $transaction = [];
        $token = $app['security.token_storage']->getToken();
        if (null !== $token)
        {
            $user = $token->getUser();
        }

        $userId = ($userRepository->getIdByLogin($user));

        $transaction['id'] = $id;
        $transaction['transaction_repository'] = $TransactionRepository;
        $form = $app['form.factory']->createBuilder(TransactionType::class, $transaction,
            ['paymentmethod_repository' => new PaymentMethodRepository($app['db']),
            ])->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $TransactionRepository->save($form->getData(), $userId['id']);

            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.finishedtransaction',
                ]
            );

            return $app->redirect($app['url_generator']->generate('movieList_index'), 301);
        }

        return $app['twig']->render(
            'transaction/add.html.twig',
            [

                'ticket_ticket_id' => $id,
                'order' => $transaction,
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
        $TransactionRepository = new TransactionRepository($app['db']);

        return $app['twig']->render(
            'transaction/show.html.twig',
            ['transactions' => $TransactionRepository->findAll()]
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

        $TransactionRepository = new TransactionRepository($app['db']);
        $transaction = $TransactionRepository->findOneTransactionById($id);

//        $token = $app['security.token_storage']->getToken();
//        if (null !== $token)
//        {
//            $user = $token->getUser();
//        }
//        $userId = ($userRepository->getIdByLogin($user));

        if (!$transaction) {
            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'warning',
                    'message' => 'message.record_not_found',
                ]
            );

            return $app->redirect($app['url_generator']->generate('transaction_show'));
        }

        $transaction['id'] = $id;
        $transaction['transaction_repository'] = $TransactionRepository;
        $form = $app['form.factory']->createBuilder(TransactionEditType::class, $transaction,
            ['paymentstatus_repository' => new PaymentStatusRepository($app['db']),
            ])->getForm();
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $TransactionRepository->saveEdited($form->getData());



            $app['session']->getFlashBag()->add(
                'messages',
                [
                    'type' => 'success',
                    'message' => 'message.element_successfully_edited',
                ]
            );

            return $app->redirect($app['url_generator']->generate('transaction_show'), 301);
        }

        return $app['twig']->render(
            'transaction/edit.html.twig',
            [
                'transaction' => $transaction,
                'form' => $form->createView(),
            ]
        );
    }


}